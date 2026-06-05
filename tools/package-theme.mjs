import { spawnSync } from 'node:child_process';

const version = `v${process.env.npm_package_version || ''}`;
const candidates = process.platform === 'win32'
  ? ['pwsh', 'powershell']
  : ['pwsh'];

let lastError = null;

for (const shell of candidates) {
  const result = spawnSync(
    shell,
    [
      '-NoProfile',
      '-ExecutionPolicy',
      'Bypass',
      '-File',
      './tools/package-theme.ps1',
      '-Version',
      version
    ],
    {
      stdio: 'inherit',
      shell: false
    }
  );

  if (!result.error) {
    process.exit(result.status ?? 1);
  }

  lastError = result.error;
}

console.error('Unable to find PowerShell. Install PowerShell 7 (`pwsh`) or run tools/package-theme.ps1 directly on Windows PowerShell.');
if (lastError) {
  console.error(lastError.message);
}
process.exit(1);
