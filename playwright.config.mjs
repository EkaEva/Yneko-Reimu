import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.YNEKO_E2E_BASE_URL || 'http://localhost:8888';
const baseHostname = new URL(baseURL).hostname;
const localHostnames = new Set(['localhost', '127.0.0.1', '[::1]', '::1']);

if (localHostnames.has(baseHostname) && process.env.YNEKO_E2E_KEEP_PROXY !== '1') {
  for (const key of ['HTTP_PROXY', 'HTTPS_PROXY', 'ALL_PROXY', 'http_proxy', 'https_proxy', 'all_proxy']) {
    delete process.env[key];
  }
  const noProxyHosts = ['localhost', '127.0.0.1', '::1'];
  const currentNoProxy = process.env.NO_PROXY || process.env.no_proxy || '';
  const mergedNoProxy = new Set([
    ...currentNoProxy.split(',').map((value) => value.trim()).filter(Boolean),
    ...noProxyHosts
  ]);
  process.env.NO_PROXY = [...mergedNoProxy].join(',');
  process.env.no_proxy = process.env.NO_PROXY;
}

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  workers: 1,
  timeout: 45_000,
  expect: {
    timeout: 10_000
  },
  reporter: process.env.CI ? [['list'], ['github']] : [['list']],
  use: {
    baseURL,
    actionTimeout: 10_000,
    navigationTimeout: 30_000,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    video: 'retain-on-failure'
  },
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome']
      }
    }
  ]
});
