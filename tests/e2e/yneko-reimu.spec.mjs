import { expect, test } from '@playwright/test';
import { createHmac } from 'node:crypto';
import { execFileSync } from 'node:child_process';

const user = {
  email: 'reimu-user@example.test',
  password: 'password'
};

test.describe.configure({ mode: 'serial' });

test.beforeEach(async ({ page }) => {
  page.on('console', (message) => {
    if (message.type() === 'error' && !/^Failed to load resource:/.test(message.text())) {
      throw new Error(`Browser console error: ${message.text()}`);
    }
  });
  page.on('pageerror', (error) => {
    throw error;
  });
});

test.afterEach(() => {
  cleanupTestUserTwoFactor();
});

async function waitForThemeRuntime(page) {
  await expect(page.locator('script[src*="assets/dist/reimu.js"]')).toHaveCount(1);
  await page.waitForFunction(() => Boolean(window.ReimuWP && typeof window.ReimuWP.init === 'function'));
}

async function expectCustomCursorEnabled(page) {
  await expect.poll(async () => page.evaluate(() => getComputedStyle(document.documentElement).getPropertyValue('--cursor-default'))).toContain('lily-normal.png');
}

async function loginFromCommentModal(page, twoFactorSecret = '') {
  await page.goto('/reimu-e2e-post/#comments');
  await waitForThemeRuntime(page);
  await expectCustomCursorEnabled(page);
  await expect(page.locator('#comments')).toBeVisible();
  await page.locator('.reimu-comment-login-link, [data-login-open]').first().click();
  await expect(page.locator('#reimu-login-modal')).toHaveAttribute('aria-hidden', 'false');

  const loginForm = page.locator('[data-reimu-login-form]');
  const loginSubmit = loginForm.locator('button[type="submit"]');
  await loginForm.locator('[name="log"]').fill(user.email);
  await loginForm.locator('[name="pwd"]').fill(user.password);
  await loginSubmit.click();

  const twoFactor = loginForm.locator('[data-login-2fa]');
  const loginMessage = loginForm.locator('[data-login-message]');
  if (await twoFactor.isVisible()) {
    await expect(loginMessage).toContainText('请输入两步验证码。');
    if (!twoFactorSecret) {
      throw new Error('Login requires a two-factor code, but the test does not know the current secret. Run qa:e2e:seed first.');
    }
    await expect(loginSubmit).toBeEnabled();
    await loginForm.locator('[name="two_factor_code"]').fill(totpCode(twoFactorSecret));
    await loginSubmit.click();
  }

  await expect(page.locator('#reimu-login-modal')).toHaveAttribute('aria-hidden', 'true');
  await expect(page.locator('[data-reimu-profile-open]')).toBeVisible();
}

async function expectLoginRequiresTwoFactorInChinese(page) {
  await page.goto('/reimu-e2e-post/#comments');
  await waitForThemeRuntime(page);
  await expectCustomCursorEnabled(page);
  await page.locator('.reimu-comment-login-link, [data-login-open]').first().click();
  await expect(page.locator('#reimu-login-modal')).toHaveAttribute('aria-hidden', 'false');

  const loginForm = page.locator('[data-reimu-login-form]');
  await loginForm.locator('[name="log"]').fill(user.email);
  await loginForm.locator('[name="pwd"]').fill(user.password);
  await loginForm.locator('button[type="submit"]').click();
  await expect(loginForm.locator('[data-login-2fa]')).toBeVisible();
  await expect(loginForm.locator('[data-login-message]')).toContainText('请输入两步验证码。');
}

function base32Decode(secret) {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  const clean = String(secret || '').toUpperCase().replace(/[^A-Z2-7]/g, '');
  let bits = '';
  for (const char of clean) {
    const index = chars.indexOf(char);
    if (index >= 0) {
      bits += index.toString(2).padStart(5, '0');
    }
  }
  const bytes = [];
  for (let i = 0; i + 8 <= bits.length; i += 8) {
    bytes.push(parseInt(bits.slice(i, i + 8), 2));
  }
  return Buffer.from(bytes);
}

function totpCode(secret, timeSlice = Math.floor(Date.now() / 1000 / 30)) {
  const key = base32Decode(secret);
  const counter = Buffer.alloc(8);
  counter.writeUInt32BE(0, 0);
  counter.writeUInt32BE(timeSlice, 4);
  const hash = createHmac('sha1', key).update(counter).digest();
  const offset = hash[hash.length - 1] & 0x0f;
  const value = (hash.readUInt32BE(offset) & 0x7fffffff) % 1000000;
  return String(value).padStart(6, '0');
}

function cleanupTestUserTwoFactor() {
  try {
    const containers = execFileSync('docker', ['ps', '--format', '{{.Names}}'], { encoding: 'utf8' })
      .split(/\r?\n/)
      .map((value) => value.trim())
      .filter(Boolean);
    const cliContainer = containers.find((name) => /-cli-1$/.test(name));
    if (!cliContainer) {
      return;
    }
    for (const key of ['_yneko_reimu_totp_enabled', '_yneko_reimu_totp_secret', '_yneko_reimu_totp_pending_secret', '_yneko_reimu_totp_recovery_codes']) {
      try {
        execFileSync('docker', ['exec', cliContainer, 'wp', '--path=/var/www/html', 'user', 'meta', 'delete', 'reimu_user', key], { stdio: 'ignore' });
      } catch {
        // Missing meta is fine; this cleanup only keeps repeated local E2E runs stable.
      }
    }
  } catch {
    // E2E can still report its primary failure when Docker cleanup is unavailable.
  }
}

test('loads primary public pages without browser runtime errors', async ({ page }) => {
  for (const path of ['/', '/reimu-e2e-post/', '/?s=Reimu', '/projects/', '/not-found-e2e/']) {
    await page.goto(path);
    await waitForThemeRuntime(page);
    await expect(page.locator('#wrap')).toBeVisible();
  }
});

test('handles comment login, profile save, upload controls, and comment submit', async ({ page }) => {
  await loginFromCommentModal(page);

  await page.locator('[data-reimu-profile-open]').first().click();
  await expect(page.locator('#reimu-profile-modal')).toHaveAttribute('aria-hidden', 'false');

  const profileForm = page.locator('[data-reimu-profile-form]');
  await expect(profileForm).toContainText('开启认证器两步验证');
  await expect(profileForm).toContainText('头像链接');
  await expect(profileForm).toContainText('昵称');
  await expect(profileForm).toContainText('个人主页');
  await expect(profileForm).toContainText('新邮箱验证码');
  await expect(profileForm).toContainText('发送验证码');
  await expect(profileForm).toContainText('确认新密码');
  await expect(profileForm).toContainText('生成密钥');
  await expect(profileForm).toContainText('认证器验证码');
  await expect(profileForm).toContainText('取消');
  await expect(profileForm).toContainText('保存');
  await expect(profileForm.locator('[data-profile-2fa-setup]')).toBeHidden();
  await profileForm.locator('[data-profile-2fa-toggle]').check();
  const [generateResponse] = await Promise.all([
    page.waitForResponse((response) => response.url().includes('admin-ajax.php') && response.request().postData()?.includes('yneko_reimu_profile_totp_generate')),
    profileForm.locator('[data-profile-2fa-generate]').click()
  ]);
  const generated = await generateResponse.json();
  expect(generated.success).toBe(true);
  const twoFactorSecret = generated.data.secret;
  await expect(profileForm.locator('[data-profile-2fa-setup]')).toBeVisible();
  await profileForm.locator('[name="totp_code"]').fill(totpCode(twoFactorSecret));
  const profileStamp = Date.now();
  const profileUrl = `https://example.test/reimu-${profileStamp}`;
  await profileForm.locator('[name="display_name"]').fill(`Reimu QA User ${profileStamp}`);
  await profileForm.locator('[name="profile_url"]').fill(profileUrl);
  await profileForm.locator('button[type="submit"]').click();
  await expect(page.locator('#reimu-profile-modal')).toHaveAttribute('aria-hidden', 'true');
  await page.locator('[data-reimu-profile-open]').first().click();
  await expect(page.locator('#reimu-profile-modal')).toHaveAttribute('aria-hidden', 'false');
  await expect(profileForm.locator('[data-profile-2fa-toggle]')).toBeChecked();
  await expect(profileForm.locator('[data-profile-2fa-setup]')).toBeHidden();
  await page.locator('button[data-profile-close]').first().click();
  await expect(page.locator('#reimu-profile-modal')).toHaveAttribute('aria-hidden', 'true');

  await page.getByRole('button', { name: /上传图片|Upload image/i }).click();
  await expect(page.locator('[data-comment-popover="image"]')).toBeVisible();
  await expect(page.locator('[data-comment-upload-button="image"]')).toBeVisible();
  await expect(page.locator('[data-comment-upload-input="image"]')).toHaveCount(1);
  await expect(page.locator('[data-comment-upload-status="image"]')).toHaveCount(1);

  const commentText = `E2E comment ${Date.now()}`;
  await page.locator('#comment').fill(commentText);
  await page.locator('.reimu-comment-submit').click();
  await expect(page.locator('#reimu-comment-list')).toContainText(commentText);

  await page.locator('.reimu-comment-current-user__logout, .reimu-comment-current-user__logout-text').first().click();
  await expect(page.locator('[data-reimu-profile-open]')).toHaveCount(0);
  await expectLoginRequiresTwoFactorInChinese(page);
});

test('keeps comments runtime initialized after PJAX navigation', async ({ page }) => {
  await page.goto('/');
  await waitForThemeRuntime(page);

  const postLink = page.locator('a[href*="reimu-e2e-post"]').first();
  await expect(postLink).toBeVisible();
  await postLink.click();
  await expect(page).toHaveURL(/reimu-e2e-post/);
  await expect(page.locator('#comments')).toBeVisible();
  await page.waitForFunction(() => Boolean(window.ReimuCommentsRuntime && typeof window.ReimuCommentsRuntime.init === 'function'));

  await page.goBack();
  await waitForThemeRuntime(page);
  await page.goto('/reimu-e2e-post/#comments');
  await expect(page.locator('#comments [data-comment-sort]')).toHaveCount(3);
});
