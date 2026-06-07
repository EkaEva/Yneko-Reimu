import { spawn } from 'node:child_process';
import { existsSync, readdirSync, statSync } from 'node:fs';
import { homedir } from 'node:os';
import { join } from 'node:path';

const cli = process.platform === 'win32' ? 'npx.cmd' : 'npx';
let composeFile = process.env.YNEKO_E2E_COMPOSE_FILE || '';
const testUser = {
  login: 'reimu_user',
  email: 'reimu-user@example.test',
  password: 'password'
};
const adminUser = {
  login: 'admin',
  email: 'admin@example.test',
  password: 'password'
};
const commandTimeoutMs = Number(process.env.YNEKO_E2E_WP_TIMEOUT_MS || 60000);

function wp(args, options = {}) {
  return new Promise((resolve, reject) => {
    const command = composeFile ? 'docker' : cli;
    const commandArgs = composeFile
      ? ['compose', '-f', composeFile, 'exec', '-T', '--user', '1000:1000', 'cli', 'wp', ...args]
      : ['wp-env', 'run', 'cli', 'wp', ...args];
    const child = spawn(
      command,
      commandArgs,
      {
        stdio: options.capture ? ['ignore', 'pipe', 'pipe'] : 'inherit',
        shell: process.platform === 'win32' && /\.cmd$/i.test(command)
      }
    );
    let stdout = '';
    let stderr = '';
    let timedOut = false;
    const timer = setTimeout(() => {
      timedOut = true;
      child.kill();
    }, commandTimeoutMs);

    if (options.capture) {
      child.stdout.on('data', (chunk) => {
        stdout += chunk.toString();
      });
      child.stderr.on('data', (chunk) => {
        stderr += chunk.toString();
      });
    }

    child.on('error', reject);
    child.on('close', (code) => {
      clearTimeout(timer);
      if (timedOut) {
        reject(new Error(`wp ${args.join(' ')} timed out after ${commandTimeoutMs}ms`));
        return;
      }
      if (code === 0) {
        resolve(stdout.trim());
        return;
      }

      const detail = stderr.trim() || stdout.trim();
      reject(new Error(`wp ${args.join(' ')} failed with exit code ${code}${detail ? `\n${detail}` : ''}`));
    });
  });
}

async function wpEnvInstallPath() {
	if (composeFile) {
		return '';
	}

	const wpEnvRoot = join(homedir(), '.wp-env');
	if (existsSync(wpEnvRoot)) {
		const candidates = readdirSync(wpEnvRoot, { withFileTypes: true })
			.filter((entry) => entry.isDirectory())
			.map((entry) => join(wpEnvRoot, entry.name))
			.filter((path) => existsSync(join(path, 'docker-compose.yml')))
			.sort((a, b) => statSync(join(b, 'docker-compose.yml')).mtimeMs - statSync(join(a, 'docker-compose.yml')).mtimeMs);
		if (candidates.length) {
			return candidates[0];
		}
	}

	try {
    const output = await new Promise((resolve, reject) => {
      const child = spawn(cli, ['wp-env', 'install-path'], { stdio: ['ignore', 'pipe', 'pipe'], shell: process.platform === 'win32' && /\.cmd$/i.test(cli) });
      let stdout = '';
      let stderr = '';
      let timedOut = false;
      const timer = setTimeout(() => {
        timedOut = true;
        child.kill();
      }, commandTimeoutMs);
      child.stdout.on('data', (chunk) => {
        stdout += chunk.toString();
      });
      child.stderr.on('data', (chunk) => {
        stderr += chunk.toString();
      });
      child.on('error', reject);
      child.on('close', (code) => {
        clearTimeout(timer);
        if (timedOut) {
          reject(new Error(`wp-env install-path timed out after ${commandTimeoutMs}ms`));
          return;
        }
        code === 0 ? resolve(stdout) : reject(new Error(stderr.trim() || stdout.trim()));
      });
    });
    return output.split(/\r?\n/).find((line) => /^[A-Z]:\\|^\//.test(line.trim()))?.trim() || '';
  } catch {
    return '';
  }
}

async function wpMaybe(args) {
  try {
    await wp(args);
  } catch (error) {
    console.warn(`[e2e-seed] non-fatal: ${error.message}`);
  }
}

async function wpCapture(args) {
  return wp(args, { capture: true });
}

async function wpCaptureMaybe(args) {
  try {
    return await wpCapture(args);
  } catch {
    return '';
  }
}

async function waitForWpCli() {
  let lastError = null;
  for (let attempt = 0; attempt < 40; attempt += 1) {
    try {
      await wpCapture(['--info']);
      return;
    } catch (error) {
      lastError = error;
      await new Promise((resolve) => setTimeout(resolve, 1500));
    }
  }
  throw lastError || new Error('WP-CLI did not become ready.');
}

async function ensureWordPressInstalled() {
  try {
    await wpCapture(['core', 'is-installed']);
    return;
  } catch {
    await wp([
      'core',
      'install',
      '--url=http://localhost:8888',
      '--title=Yneko Reimu E2E',
      `--admin_user=${adminUser.login}`,
      `--admin_password=${adminUser.password}`,
      `--admin_email=${adminUser.email}`,
      '--skip-email'
    ]);
  }
}

async function ensureAdminUser() {
  await wpMaybe([
    'user',
    'create',
    adminUser.login,
    adminUser.email,
    '--role=administrator',
    `--user_pass=${adminUser.password}`,
    '--display_name=Reimu QA Admin'
  ]);
  await wp([
    'user',
    'update',
    adminUser.login,
    `--user_pass=${adminUser.password}`,
    '--role=administrator',
    '--display_name=Reimu QA Admin'
  ]);
}

async function ensureUser() {
  await wpMaybe([
    'user',
    'create',
    testUser.login,
    testUser.email,
    '--role=subscriber',
    `--user_pass=${testUser.password}`,
    '--display_name=Reimu QA User'
  ]);
  await wp(['user', 'update', testUser.login, `--user_pass=${testUser.password}`, '--display_name=Reimu QA User']);
}

async function clearUserMeta(login, keys) {
  for (const key of keys) {
    const existingMeta = await wpCaptureMaybe(['user', 'meta', 'get', login, key]);
    if (existingMeta) {
      await wpMaybe(['user', 'meta', 'delete', login, key]);
    }
  }
}

async function upsertPost(slug, title, content, type = 'post') {
  const existing = await wpCapture(['post', 'list', `--post_type=${type}`, `--name=${slug}`, '--field=ID']);
  const args = [
    'post',
    existing ? 'update' : 'create',
    ...(existing ? [existing] : []),
    `--post_type=${type}`,
    `--post_title=${title}`,
    `--post_name=${slug}`,
    `--post_content=${content}`,
    '--post_status=publish',
    '--comment_status=open',
    '--ping_status=closed'
  ];
  const output = await wpCapture([...args, '--porcelain']);
  return existing || output.trim();
}

async function setOptionJson(name, value) {
  await wp(['option', 'update', name, JSON.stringify(value), '--format=json']);
}

async function createSeedComment(postId) {
  const comment = {
    comment_post_ID: Number(postId),
    comment_author: 'Seed Visitor',
    comment_author_email: 'seed@example.test',
    comment_content: 'Seed comment for sorting and reply checks.',
    comment_approved: '1'
  };
  await wp([
    'eval',
    `$comment = json_decode( ${JSON.stringify(JSON.stringify(comment))}, true ); $id = wp_insert_comment( wp_slash( $comment ) ); if ( ! $id ) { WP_CLI::error( 'Seed comment could not be created.' ); } WP_CLI::success( 'Created comment ' . $id . '.' );`
  ]);
}

async function ensurePermalinkRules() {
  const rules = [
    '# BEGIN WordPress',
    '<IfModule mod_rewrite.c>',
    'RewriteEngine On',
    'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]',
    'RewriteBase /',
    'RewriteRule ^index\\.php$ - [L]',
    'RewriteCond %{REQUEST_FILENAME} !-f',
    'RewriteCond %{REQUEST_FILENAME} !-d',
    'RewriteRule . /index.php [L]',
    '</IfModule>',
    '# END WordPress',
    ''
  ].join('\n');
  await wp(['eval', `file_put_contents(ABSPATH . '.htaccess', ${JSON.stringify(rules)});`]);
}

async function main() {
  console.log('[e2e-seed] Preparing Yneko-Reimu wp-env site...');
  const installPath = await wpEnvInstallPath();
  if (installPath) {
    composeFile = join(installPath, 'docker-compose.yml');
  }

  await waitForWpCli();
  await ensureWordPressInstalled();
  await wp(['theme', 'activate', 'Yneko-Reimu']);
  await wp(['option', 'update', 'WPLANG', 'zh_CN']);
  await wp(['option', 'update', 'blogname', 'Yneko Reimu E2E']);
  await wp(['option', 'update', 'permalink_structure', '/%postname%/']);
  await wp(['option', 'update', 'default_comment_status', 'open']);
  await wp(['option', 'update', 'comment_registration', '0']);
  await wp(['option', 'update', 'thread_comments', '1']);
  await wp(['rewrite', 'flush', '--hard']);
  await ensurePermalinkRules();

  await ensureAdminUser();
  await ensureUser();
  const volatileUserMetaKeys = [
    '_yneko_reimu_avatar_review_status',
    '_yneko_reimu_avatar_review_status_time',
    '_yneko_reimu_comment_tags_review_status',
    '_yneko_reimu_comment_tags_review_status_time',
    '_yneko_reimu_comment_review_status',
    '_yneko_reimu_comment_review_status_time',
    '_yneko_reimu_comment_review_status_comment_id',
    '_yneko_reimu_comment_review_status_count',
    '_yneko_reimu_comment_pending_tags',
    '_yneko_reimu_pending_avatar_url',
    '_yneko_reimu_totp_enabled',
    '_yneko_reimu_totp_secret',
    '_yneko_reimu_totp_pending_secret',
    '_yneko_reimu_totp_recovery_codes'
  ];
  await clearUserMeta(testUser.login, volatileUserMetaKeys);
  await clearUserMeta(adminUser.login, [
    '_yneko_reimu_totp_enabled',
    '_yneko_reimu_totp_secret',
    '_yneko_reimu_totp_pending_secret',
    '_yneko_reimu_totp_recovery_codes'
  ]);

  const postId = await upsertPost(
    'reimu-e2e-post',
    'Reimu E2E Post',
    'This post is created by the local E2E seed. It has comments enabled and enough content for PJAX checks.'
  );
  await upsertPost(
    'reimu-e2e-page',
    'Reimu E2E Page',
    'This page is created by the local E2E seed.',
    'page'
  );

  const existingCommentIds = await wpCapture(['comment', 'list', `--post_id=${postId}`, '--field=comment_ID']);
  for (const commentId of existingCommentIds.split(/\r?\n/).map((value) => value.trim()).filter(Boolean)) {
    await wpMaybe(['comment', 'delete', commentId, '--force']);
  }
  await createSeedComment(postId);

  await setOptionJson('yneko_reimu_settings', {
    comment_upload: {
      enabled: '1',
      image_enabled: '1',
      gif_enabled: '1',
      image_review: '0',
      gif_review: '0',
      image_max_mb: 1,
      gif_max_mb: 3,
      temp_cleanup_days: 7,
      rejected_cleanup_hours: 24,
      avatar_enabled: '1',
      avatar_review: '0',
      avatar_max_mb: 1
    },
    features: {
      preloader_enable: '0',
      top_enable: '1',
      triangle_badge: '1',
      firework_enable: '0',
      pjax_enable: '1',
      busuanzi_enable: '0',
      katex_enable: '0',
      photoswipe_enable: '0',
      mermaid_enable: '0',
      custom_cursor: '1',
      show_admin_toolbar: '0'
    },
    search: {
      algolia_enable: '0',
      algolia_app_id: '',
      algolia_api_key: '',
      algolia_index_name: '',
      local_enable: '1',
      local_json_url: '',
      index_full_content: '0'
    },
    external_comments: {
      giscus_enable: '0',
      utterances_enable: '0',
      disqus_enable: '0',
      waline_enable: '0',
      twikoo_enable: '0',
      valine_enable: '0'
    },
    builtin_pages: {
      projects: '1',
      archives: '1',
      about: '1',
      friend: '1'
    }
  });

  console.log('[e2e-seed] Done.');
  console.log(`[e2e-seed] Test post: /reimu-e2e-post/`);
  console.log(`[e2e-seed] Admin: ${adminUser.login} / ${adminUser.password}`);
  console.log(`[e2e-seed] User: ${testUser.email} / ${testUser.password}`);
}

main().then(() => {
  process.exit(0);
}).catch((error) => {
  console.error(`[e2e-seed] ${error.message}`);
  process.exit(1);
});
