import { readdir } from 'node:fs/promises';
import { dirname, extname, resolve } from 'node:path';
import { spawn } from 'node:child_process';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const scanRoots = [
  resolve(root, 'theme/Yneko-Reimu/assets/src'),
  resolve(root, 'tools')
];
const extensions = new Set(['.js', '.mjs']);

async function collectScripts(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  const files = [];

  for (const entry of entries) {
    const absolutePath = resolve(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await collectScripts(absolutePath));
      continue;
    }
    if (entry.isFile() && extensions.has(extname(entry.name))) {
      files.push(absolutePath);
    }
  }

  return files;
}

function checkScript(file) {
  return new Promise((resolveCheck, rejectCheck) => {
    const child = spawn(process.execPath, ['--check', file], { stdio: 'inherit' });
    child.on('error', rejectCheck);
    child.on('exit', (code) => {
      if (code === 0) {
        resolveCheck();
        return;
      }
      rejectCheck(new Error(`node --check failed for ${file}`));
    });
  });
}

const scripts = (await Promise.all(scanRoots.map(collectScripts))).flat().sort();

for (const script of scripts) {
  await checkScript(script);
}

console.log(`[check-js] ${scripts.length} JavaScript files passed syntax checks.`);
