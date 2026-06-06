import { spawn } from 'node:child_process';
import { readFile, writeFile } from 'node:fs/promises';
import { join } from 'node:path';

const command = process.platform === 'win32' ? 'npx.cmd' : 'npx';
const safeUsername = 'wpuser';

function run(cmd, args, options = {}) {
  return new Promise((resolve, reject) => {
    const child = spawn(cmd, args, {
      cwd: options.cwd,
      env: options.env || process.env,
      shell: process.platform === 'win32' && /\.cmd$/i.test(cmd),
      stdio: options.capture ? ['ignore', 'pipe', 'pipe'] : 'inherit'
    });
    let stdout = '';
    let stderr = '';

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
      if (code === 0) {
        resolve({ stdout: stdout.trim(), stderr: stderr.trim() });
        return;
      }
      reject(new Error(`${cmd} ${args.join(' ')} failed with exit code ${code}${stderr ? `\n${stderr.trim()}` : ''}`));
    });
  });
}

async function wpEnvInstallPath() {
  const result = await run(command, ['wp-env', 'install-path'], { capture: true });
  return result.stdout.split(/\r?\n/).find((line) => /^[A-Z]:\\|^\//.test(line.trim()))?.trim() || '';
}

async function replaceInFile(path, replacements) {
  let content = await readFile(path, 'utf8');
  for (const [from, to] of replacements) {
    content = content.split(from).join(to);
  }
  await writeFile(path, content);
}

async function patchGeneratedWpEnv(workDir) {
  const composePath = join(workDir, 'docker-compose.yml');
  const dockerfiles = [
    'WordPress.Dockerfile',
    'Tests-WordPress.Dockerfile',
    'CLI.Dockerfile',
    'Tests-CLI.Dockerfile'
  ];

  for (const file of dockerfiles) {
    await replaceInFile(join(workDir, file), [['86135', safeUsername]]);
  }

  await replaceInFile(composePath, [
    ["HOST_USERNAME: '86135'", `HOST_USERNAME: '${safeUsername}'`],
    ["user-home:/home/86135", `user-home:/home/${safeUsername}`],
    ["tests-user-home:/home/86135", `tests-user-home:/home/${safeUsername}`]
  ]);
}

async function startWithGeneratedCompose(workDir) {
  const composePath = join(workDir, 'docker-compose.yml');
  const services = ['mysql', 'wordpress', 'cli'];
  try {
    await run('docker', ['compose', '-f', composePath, 'up', '-d', '--no-build', ...services], { cwd: workDir });
  } catch {
    console.warn('[e2e-start] Cached wp-env images were not usable; building the minimal E2E service set.');
    await run('docker', ['compose', '-f', composePath, 'build', ...services], { cwd: workDir });
    await run('docker', ['compose', '-f', composePath, 'up', '-d', '--no-build', ...services], { cwd: workDir });
  }
}

async function main() {
  if (process.env.YNEKO_E2E_TRY_WP_ENV === '1') {
    try {
      await run(command, ['wp-env', 'start', '--update']);
      console.log('[e2e-start] wp-env started normally.');
      return;
    } catch (error) {
      console.warn('[e2e-start] wp-env start hit a known Windows/numeric-username Docker build issue.');
    }
  }

  const workDir = await wpEnvInstallPath();
  if (!workDir) {
    throw new Error('Could not resolve wp-env install path for workaround.');
  }

  console.log(`[e2e-start] Applying generated wp-env workaround in ${workDir}`);
  await patchGeneratedWpEnv(workDir);
  await startWithGeneratedCompose(workDir);
  console.log('[e2e-start] Docker services are running. Use npm run qa:e2e:seed next.');
}

main().catch((error) => {
  console.error(`[e2e-start] ${error.message}`);
  process.exit(1);
});
