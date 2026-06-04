import { readdir, stat } from 'node:fs/promises';
import { basename, dirname, resolve } from 'node:path';
import { createInterface } from 'node:readline';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const releaseDir = resolve(root, 'releases');
const forbiddenPatterns = [
  /^Yneko-Reimu\/assets\/src\//,
  /^Yneko-Reimu\/node_modules\//,
  /^Yneko-Reimu\/vendor\//,
  /^Yneko-Reimu\/tools\//,
  /^Yneko-Reimu\/assets\/dist\/manifest\.json$/,
  /^Yneko-Reimu\/PROJECT\.md$/,
  /^Yneko-Reimu\/AGENTS\.md$/,
  /^Yneko-Reimu\/task_plan\.md$/,
  /^Yneko-Reimu\/findings\.md$/,
  /^Yneko-Reimu\/progress\.md$/
];

async function latestZip() {
  const entries = await readdir(releaseDir, { withFileTypes: true });
  const zips = await Promise.all(
    entries
      .filter((entry) => entry.isFile() && entry.name.toLowerCase().endsWith('.zip'))
      .map(async (entry) => {
        const path = resolve(releaseDir, entry.name);
        const info = await stat(path);
        return { path, mtimeMs: info.mtimeMs };
      })
  );

  zips.sort((a, b) => b.mtimeMs - a.mtimeMs);
  return zips[0]?.path;
}

async function listZipEntriesWithPowerShell(zipPath) {
  const { spawn } = await import('node:child_process');
  const command = [
    '$ErrorActionPreference = "Stop";',
    'Add-Type -AssemblyName System.IO.Compression.FileSystem;',
    `$zip = [System.IO.Compression.ZipFile]::OpenRead(${JSON.stringify(zipPath)});`,
    'try { $zip.Entries | ForEach-Object { $_.FullName } } finally { $zip.Dispose() }'
  ].join(' ');

  const child = spawn('powershell', ['-NoProfile', '-Command', command], { stdio: ['ignore', 'pipe', 'inherit'] });
  const lines = [];
  const rl = createInterface({ input: child.stdout });

  rl.on('line', (line) => {
    if (line.trim()) {
      lines.push(line.trim());
    }
  });

  const code = await new Promise((resolveCode) => {
    child.on('close', resolveCode);
  });

  if (code !== 0) {
    throw new Error(`Unable to inspect ${zipPath}`);
  }

  return lines;
}

const zipPath = await latestZip();

if (!zipPath) {
  console.error('[package] No release ZIP found. Run npm run package first.');
  process.exit(1);
}

const entries = await listZipEntriesWithPowerShell(zipPath);

const normalized = entries.map((entry) => entry.replace(/\\/g, '/'));
const forbidden = normalized.filter((entry) => forbiddenPatterns.some((pattern) => pattern.test(entry)));
const requiredEntries = [
  'Yneko-Reimu/readme.txt'
];
const missingRequired = requiredEntries.filter((entry) => !normalized.includes(entry));

if (forbidden.length) {
  console.error(`[package] ${basename(zipPath)} contains forbidden files:`);
  for (const entry of forbidden) {
    console.error(`- ${entry}`);
  }
  process.exit(1);
}

if (missingRequired.length) {
  console.error(`[package] ${basename(zipPath)} is missing required runtime files:`);
  for (const entry of missingRequired) {
    console.error(`- ${entry}`);
  }
  process.exit(1);
}

console.log(`[package] ${basename(zipPath)} contains ${normalized.length} entries and no forbidden development files.`);
