import { readFile, readdir } from 'node:fs/promises';
import { createReadStream } from 'node:fs';
import { dirname, extname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const expectedTestedUpTo = '7.0';
let failed = false;

function fail(message) {
  console.error(message);
  failed = true;
}

async function walk(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    const path = resolve(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await walk(path));
    } else {
      files.push(path);
    }
  }
  return files;
}

function relativeThemePath(path) {
  return path.slice(themeRoot.length + 1).replace(/\\/g, '/');
}

function parsePngSize(path) {
  return new Promise((resolveSize, reject) => {
    const stream = createReadStream(path, { start: 0, end: 23 });
    const chunks = [];
    stream.on('data', (chunk) => chunks.push(chunk));
    stream.on('error', reject);
    stream.on('end', () => {
      const buffer = Buffer.concat(chunks);
      const signature = buffer.subarray(0, 8).toString('hex');
      if (signature !== '89504e470d0a1a0a' || buffer.length < 24) {
        reject(new Error(`${relativeThemePath(path)} is not a valid PNG file.`));
        return;
      }
      resolveSize({
        width: buffer.readUInt32BE(16),
        height: buffer.readUInt32BE(20)
      });
    });
  });
}

const allFiles = await walk(themeRoot);
const phpFiles = allFiles.filter((file) => extname(file).toLowerCase() === '.php');
const phpMissingGuard = [];

for (const file of phpFiles) {
  const text = await readFile(file, 'utf8');
  if (!/defined\(\s*['"]ABSPATH['"]\s*\)/.test(text)) {
    phpMissingGuard.push(relativeThemePath(file));
  }
}

if (phpMissingGuard.length) {
  fail('[release-readiness] PHP files missing ABSPATH direct-access guard:');
  for (const file of phpMissingGuard) {
    console.error(`- ${file}`);
  }
} else {
  console.log(`[release-readiness] ${phpFiles.length} PHP files include ABSPATH direct-access guards.`);
}

const styleText = await readFile(resolve(themeRoot, 'style.css'), 'utf8');
const testedMatch = styleText.match(/^Tested up to:\s*(.+)$/m);
if (!testedMatch || testedMatch[1].trim() !== expectedTestedUpTo) {
  fail(`[release-readiness] style.css must declare Tested up to: ${expectedTestedUpTo}.`);
} else {
  console.log(`[release-readiness] style.css Tested up to is ${expectedTestedUpTo}.`);
}

for (const requiredField of ['Theme Name', 'Version', 'Requires at least', 'Requires PHP', 'License', 'Text Domain']) {
  if (!new RegExp(`^${requiredField}:\\s*\\S+`, 'm').test(styleText)) {
    fail(`[release-readiness] style.css is missing ${requiredField}.`);
  }
}

const readmePath = resolve(themeRoot, 'readme.txt');
let readmeText = '';
try {
  readmeText = await readFile(readmePath, 'utf8');
} catch {
  fail('[release-readiness] runtime theme readme.txt is missing.');
}

if (readmeText) {
  for (const snippet of ['=== Yneko-Reimu ===', 'Tested up to: 7.0', 'License: MIT', 'Privacy And Remote Resources', 'Credits And Licensing']) {
    if (!readmeText.includes(snippet)) {
      fail(`[release-readiness] readme.txt must include "${snippet}".`);
    }
  }
  if (!failed) {
    console.log('[release-readiness] runtime readme.txt includes release, privacy, and licensing notes.');
  }
}

const screenshotPath = resolve(themeRoot, 'screenshot.png');
try {
  const screenshot = await parsePngSize(screenshotPath);
  if (screenshot.width !== 1200 || screenshot.height !== 900) {
    fail(`[release-readiness] screenshot.png must be 1200x900, found ${screenshot.width}x${screenshot.height}.`);
  } else {
    console.log('[release-readiness] screenshot.png is 1200x900.');
  }
} catch (error) {
  fail(`[release-readiness] ${error.message}`);
}

if (failed) {
  process.exitCode = 1;
}
