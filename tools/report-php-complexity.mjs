import { readdir, readFile } from 'node:fs/promises';
import { relative, resolve } from 'node:path';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const phpExtension = /\.php$/i;
const namedFunctionPattern = /\bfunction\s+([A-Za-z_\u0080-\uffff][A-Za-z0-9_\u0080-\uffff]*)\s*\(/g;
const complexityPattern = /\b(if|elseif|for|foreach|while|case|catch)\b|&&|\|\||\?/g;

async function collectPhpFiles(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  const files = [];

  for (const entry of entries) {
    const absolutePath = resolve(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await collectPhpFiles(absolutePath));
      continue;
    }
    if (entry.isFile() && phpExtension.test(entry.name)) {
      files.push(absolutePath);
    }
  }

  return files;
}

function lineNumberAt(source, index) {
  let line = 1;
  for (let i = 0; i < index; i += 1) {
    if (source.charCodeAt(i) === 10) {
      line += 1;
    }
  }
  return line;
}

function findFunctionBody(source, declarationIndex) {
  const openIndex = source.indexOf('{', declarationIndex);
  if (openIndex === -1) {
    return null;
  }

  let depth = 0;
  for (let i = openIndex; i < source.length; i += 1) {
    const char = source[i];
    if (char === '{') {
      depth += 1;
    } else if (char === '}') {
      depth -= 1;
      if (depth === 0) {
        return {
          startIndex: openIndex,
          endIndex: i,
          body: source.slice(openIndex + 1, i)
        };
      }
    }
  }

  return null;
}

function countMatches(source, pattern) {
  return Array.from(source.matchAll(pattern)).length;
}

function summarizeFile(file, source) {
  const lines = source.split(/\r?\n/);
  const nonBlankLines = lines.filter((line) => line.trim()).length;
  const relativePath = relative(themeRoot, file).replace(/\\/g, '/');
  const functions = [];

  namedFunctionPattern.lastIndex = 0;
  let match;
  while ((match = namedFunctionPattern.exec(source))) {
    const body = findFunctionBody(source, match.index);
    if (!body) {
      continue;
    }
    const startLine = lineNumberAt(source, match.index);
    const endLine = lineNumberAt(source, body.endIndex);
    functions.push({
      file: relativePath,
      name: match[1],
      startLine,
      lines: endLine - startLine + 1,
      complexity: 1 + countMatches(body.body, complexityPattern)
    });
  }

  return {
    file: relativePath,
    lines: lines.length,
    nonBlankLines,
    functionCount: functions.length,
    complexity: functions.reduce((total, item) => total + item.complexity, 0),
    functions
  };
}

function formatTable(rows, columns) {
  const widths = columns.map((column) => {
    return Math.max(column.label.length, ...rows.map((row) => String(column.value(row)).length));
  });
  const header = columns.map((column, index) => column.label.padEnd(widths[index])).join('  ');
  const separator = widths.map((width) => '-'.repeat(width)).join('  ');
  const body = rows.map((row) => {
    return columns.map((column, index) => String(column.value(row)).padEnd(widths[index])).join('  ');
  });
  return [header, separator, ...body].join('\n');
}

const files = await collectPhpFiles(themeRoot);
const summaries = [];

for (const file of files) {
  const source = await readFile(file, 'utf8');
  summaries.push(summarizeFile(file, source));
}

summaries.sort((a, b) => b.nonBlankLines - a.nonBlankLines || a.file.localeCompare(b.file));
const functions = summaries.flatMap((summary) => summary.functions)
  .sort((a, b) => b.lines - a.lines || b.complexity - a.complexity || a.file.localeCompare(b.file));
const complexFunctions = [...functions]
  .sort((a, b) => b.complexity - a.complexity || b.lines - a.lines || a.file.localeCompare(b.file));

const totalLines = summaries.reduce((total, item) => total + item.lines, 0);
const totalNonBlankLines = summaries.reduce((total, item) => total + item.nonBlankLines, 0);
const totalFunctions = summaries.reduce((total, item) => total + item.functionCount, 0);
const totalComplexity = summaries.reduce((total, item) => total + item.complexity, 0);

console.log(`[php-complexity] scanned ${summaries.length} PHP files, ${totalFunctions} named functions.`);
console.log(`[php-complexity] total lines: ${totalLines}; nonblank lines: ${totalNonBlankLines}; approximate branch score: ${totalComplexity}.`);
console.log('');
console.log('[php-complexity] largest files by nonblank lines:');
console.log(formatTable(summaries.slice(0, 10), [
  { label: 'lines', value: (row) => row.nonBlankLines },
  { label: 'funcs', value: (row) => row.functionCount },
  { label: 'score', value: (row) => row.complexity },
  { label: 'file', value: (row) => row.file }
]));
console.log('');
console.log('[php-complexity] largest named functions by lines:');
console.log(formatTable(functions.slice(0, 10), [
  { label: 'lines', value: (row) => row.lines },
  { label: 'score', value: (row) => row.complexity },
  { label: 'location', value: (row) => `${row.file}:${row.startLine}` },
  { label: 'function', value: (row) => row.name }
]));
console.log('');
console.log('[php-complexity] highest branch-score named functions:');
console.log(formatTable(complexFunctions.slice(0, 10), [
  { label: 'score', value: (row) => row.complexity },
  { label: 'lines', value: (row) => row.lines },
  { label: 'location', value: (row) => `${row.file}:${row.startLine}` },
  { label: 'function', value: (row) => row.name }
]));
