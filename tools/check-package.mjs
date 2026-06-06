import { readFile, readdir, stat } from 'node:fs/promises';
import { basename, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { inflateRawSync } from 'node:zlib';

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

async function listZipEntries(zipPath) {
  const zip = await readFile(zipPath);
  const eocdSignature = 0x06054b50;
  const centralDirectorySignature = 0x02014b50;
  const minEocdSize = 22;
  const maxCommentSize = 0xffff;
  const searchStart = Math.max(0, zip.length - minEocdSize - maxCommentSize);

  let eocdOffset = -1;
  for (let offset = zip.length - minEocdSize; offset >= searchStart; offset -= 1) {
    if (zip.readUInt32LE(offset) === eocdSignature) {
      eocdOffset = offset;
      break;
    }
  }

  if (eocdOffset === -1) {
    throw new Error(`Unable to find ZIP central directory in ${zipPath}`);
  }

  const totalEntries = zip.readUInt16LE(eocdOffset + 10);
  const centralDirectoryOffset = zip.readUInt32LE(eocdOffset + 16);

  if (totalEntries === 0xffff || centralDirectoryOffset === 0xffffffff) {
    throw new Error(`ZIP64 package inspection is not supported for ${zipPath}`);
  }

  const entries = [];
  let offset = centralDirectoryOffset;

  for (let index = 0; index < totalEntries; index += 1) {
    if (zip.readUInt32LE(offset) !== centralDirectorySignature) {
      throw new Error(`Invalid ZIP central directory entry ${index + 1} in ${zipPath}`);
    }

    const fileNameLength = zip.readUInt16LE(offset + 28);
    const extraLength = zip.readUInt16LE(offset + 30);
    const commentLength = zip.readUInt16LE(offset + 32);
    const fileNameStart = offset + 46;
    const fileNameEnd = fileNameStart + fileNameLength;

    entries.push(zip.subarray(fileNameStart, fileNameEnd).toString('utf8'));
    offset = fileNameEnd + extraLength + commentLength;
  }

  return entries;
}

async function readZipFile(zipPath, targetEntry) {
  const zip = await readFile(zipPath);
  const localFileSignature = 0x04034b50;
  const entries = await listZipEntries(zipPath);

  if (!entries.includes(targetEntry)) {
    return '';
  }

  let offset = 0;
  while (offset < zip.length - 30) {
    if (zip.readUInt32LE(offset) !== localFileSignature) {
      offset += 1;
      continue;
    }

    const compressionMethod = zip.readUInt16LE(offset + 8);
    const compressedSize = zip.readUInt32LE(offset + 18);
    const fileNameLength = zip.readUInt16LE(offset + 26);
    const extraLength = zip.readUInt16LE(offset + 28);
    const fileNameStart = offset + 30;
    const fileNameEnd = fileNameStart + fileNameLength;
    const fileName = zip.subarray(fileNameStart, fileNameEnd).toString('utf8');
    const dataStart = fileNameEnd + extraLength;
    const dataEnd = dataStart + compressedSize;

    if (fileName === targetEntry) {
      if (compressionMethod === 0) {
        return zip.subarray(dataStart, dataEnd).toString('utf8');
      }

      if (compressionMethod === 8) {
        return inflateRawSync(zip.subarray(dataStart, dataEnd)).toString('utf8');
      }

      {
        throw new Error(`ZIP entry ${targetEntry} uses unsupported compression method ${compressionMethod}`);
      }
    }

    offset = dataEnd;
  }

  return '';
}

const zipPath = await latestZip();

if (!zipPath) {
  console.error('[package] No release ZIP found. Run npm run package first.');
  process.exit(1);
}

const entries = await listZipEntries(zipPath);

const normalized = entries.map((entry) => entry.replace(/\\/g, '/'));
const forbidden = normalized.filter((entry) => forbiddenPatterns.some((pattern) => pattern.test(entry)));
const requiredEntries = [
  'Yneko-Reimu/readme.txt',
  'Yneko-Reimu/docs/release-notes-v0.2.13.md'
];
const missingRequired = requiredEntries.filter((entry) => !normalized.includes(entry));
const missingCursorAssets = [
  'Yneko-Reimu/assets/images/cursor/lily-normal.png',
  'Yneko-Reimu/assets/images/cursor/lily-link.png',
  'Yneko-Reimu/assets/images/cursor/lily-text.png',
  'Yneko-Reimu/assets/images/cursor/lily-work.png'
].filter((entry) => !normalized.includes(entry));

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

if (missingCursorAssets.length) {
  console.error(`[package] ${basename(zipPath)} is missing required cursor assets:`);
  for (const entry of missingCursorAssets) {
    console.error(`- ${entry}`);
  }
  process.exit(1);
}

const mainCss = await readZipFile(zipPath, 'Yneko-Reimu/assets/dist/reimu.css');
if (mainCss.includes('url(./lily-')) {
  console.error(`[package] ${basename(zipPath)} references transient dist cursor files from reimu.css.`);
  process.exit(1);
}

console.log(`[package] ${basename(zipPath)} contains ${normalized.length} entries and no forbidden development files.`);
