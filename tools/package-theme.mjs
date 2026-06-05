import { spawnSync } from 'node:child_process';

const version = `v${process.env.npm_package_version || ''}`;
const candidates = process.platform === 'win32'
  ? ['pwsh', 'powershell']
  : ['pwsh'];

let lastError = null;

for (const shell of candidates) {
  const args = [
    '-NoProfile',
    '-File',
    './tools/package-theme.ps1',
    '-Version',
    version
  ];

  if (process.platform === 'win32') {
    args.splice(1, 0, '-ExecutionPolicy', 'Bypass');
  }

  const result = spawnSync(
    shell,
    args,
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
