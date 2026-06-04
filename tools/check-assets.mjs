import { readFile, readdir } from 'node:fs/promises';
import { extname, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { dirname } from 'node:path';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const checkedExtensions = new Set(['.php', '.css', '.js']);
const forbiddenPatterns = [
  {
    label: 'data:image URI',
    pattern: /data:image\//i
  },
  {
    label: 'base64-encoded SVG image MIME',
    pattern: /image\/svg\+xml[^"')\s]*base64/i
  },
  {
    label: 'base64 payload marker',
    pattern: /;base64,/i
  }
];

async function walk(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  const files = [];

  for (const entry of entries) {
    const path = resolve(dir, entry.name);

    if (entry.isDirectory()) {
      files.push(...await walk(path));
      continue;
    }

    if (checkedExtensions.has(extname(entry.name).toLowerCase())) {
      files.push(path);
    }
  }

  return files;
}

function themePath(path) {
  return relative(themeRoot, path).replace(/\\/g, '/');
}

const files = await walk(themeRoot);
const findings = [];

for (const file of files) {
  const text = await readFile(file, 'utf8');

  for (const check of forbiddenPatterns) {
    const match = check.pattern.exec(text);
    if (match) {
      findings.push({
        file: themePath(file),
        label: check.label,
        match: match[0].slice(0, 80)
      });
    }
  }
}

if (findings.length) {
  console.error('[assets] Inline image data is not allowed in runtime PHP/CSS/JS files:');
  for (const finding of findings) {
    console.error(`- ${finding.file}: ${finding.label} (${finding.match})`);
  }
  process.exit(1);
}

console.log(`[assets] ${files.length} runtime PHP/CSS/JS files contain no inline data:image assets.`);
