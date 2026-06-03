import { execFile } from 'node:child_process';
import { existsSync } from 'node:fs';
import { mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { promisify } from 'node:util';
import { fileURLToPath } from 'node:url';

const exec = promisify(execFile);
const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const sourceDir = process.env.LILY_CURSOR_SOURCE_DIR
  ? resolve(process.env.LILY_CURSOR_SOURCE_DIR)
  : resolve(root, '../MousePointer/En Ver/png');
const cursorDir = resolve(themeRoot, 'assets/images/cursor');
const cursorSize = Number(process.env.LILY_CURSOR_SIZE || 24);
const magickCandidates = [
  process.env.MAGICK_EXE,
  'C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI\\magick.exe',
  'C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI-x64-dll\\magick.exe',
  'magick'
].filter(Boolean);

const cursors = [
  ['pointer.png', 'lily-normal.png'],
  ['link.png', 'lily-link.png'],
  ['text.png', 'lily-text.png'],
  ['busy.png', 'lily-busy.png'],
  ['work.png', 'lily-work.png'],
  ['unavailable.png', 'lily-unavailable.png'],
  ['help.png', 'lily-help.png'],
  ['move.png', 'lily-move.png'],
  ['hand.png', 'lily-hand.png'],
  ['cross.png', 'lily-cross.png'],
  ['horz.png', 'lily-resize-ew.png'],
  ['vert.png', 'lily-resize-ns.png'],
  ['dgn1.png', 'lily-resize-nwse.png'],
  ['dgn2.png', 'lily-resize-nesw.png'],
  ['alternate.png', 'lily-alternate.png']
];

await mkdir(cursorDir, { recursive: true });

const missingSources = cursors
  .map(([sourceName]) => resolve(sourceDir, sourceName))
  .filter((source) => !existsSync(source));

if (missingSources.length > 0) {
  const missingTargets = cursors
    .map(([, targetName]) => resolve(cursorDir, targetName))
    .filter((target) => !existsSync(target));

  if (!process.env.LILY_CURSOR_SOURCE_DIR && missingTargets.length === 0) {
    console.log(`Cursor source directory is unavailable; keeping existing built cursor PNG files in ${cursorDir}`);
    process.exit(0);
  }

  throw new Error(
    [
      `Missing cursor source PNG: ${missingSources[0]}`,
      'Set LILY_CURSOR_SOURCE_DIR to the Lily cursor source directory, or commit the built cursor PNG files before packaging.'
    ].join('\n')
  );
}

function resolveMagick() {
  for (const candidate of magickCandidates) {
    if (candidate === 'magick' || existsSync(candidate)) {
      return candidate;
    }
  }

  return 'magick';
}

const magick = resolveMagick();

for (const [sourceName, targetName] of cursors) {
  const source = resolve(sourceDir, sourceName);
  await exec(magick, [
    source,
    '-background',
    'none',
    '-resize',
    `${cursorSize}x${cursorSize}`,
    '-strip',
    '-define',
    'png:exclude-chunk=time,date',
    resolve(cursorDir, targetName)
  ], {
    windowsHide: true,
    maxBuffer: 1024 * 1024 * 8
  });
}

console.log(`Built ${cursors.length} Lily cursor PNG files at ${cursorSize}px from ${sourceDir}`);
