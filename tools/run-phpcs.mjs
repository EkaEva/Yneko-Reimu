import { spawnSync } from 'node:child_process';
import { existsSync } from 'node:fs';
import { resolve } from 'node:path';

const phpcs = process.platform === 'win32'
  ? resolve('vendor/squizlabs/php_codesniffer/bin/phpcs')
  : resolve('vendor/bin/phpcs');

if (!existsSync(phpcs)) {
  console.error('Local PHPCS binary not found. Run composer install before npm run lint:php.');
  process.exit(1);
}

const command = process.platform === 'win32' ? 'php' : phpcs;
const args = process.platform === 'win32'
  ? [phpcs, '--standard=phpcs.xml.dist']
  : ['--standard=phpcs.xml.dist'];

const result = spawnSync(command, args, {
  stdio: 'inherit',
  shell: false
});

process.exit(result.status ?? 1);
