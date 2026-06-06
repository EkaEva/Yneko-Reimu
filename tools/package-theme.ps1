param(
  [string]$Version = '',
  [string]$OutputName = ''
)

$ErrorActionPreference = 'Stop'

$repoDir = Resolve-Path (Join-Path $PSScriptRoot '..')
$themeDir = Resolve-Path (Join-Path $repoDir 'theme/Yneko-Reimu')
$releaseDir = Join-Path $repoDir 'releases'
$stageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ('yneko-reimu-package-' + [System.Guid]::NewGuid().ToString('N'))
$stageTheme = Join-Path $stageRoot 'Yneko-Reimu'

function Get-SafeFileNamePart {
  param([string]$Value)

  $safe = $Value.Trim()
  foreach ($char in [System.IO.Path]::GetInvalidFileNameChars()) {
    $safe = $safe.Replace($char, '-')
  }
  $safe = $safe -replace '\s+', '-'
  return $safe
}

if ($OutputName.Trim()) {
  $zipFileName = Get-SafeFileNamePart $OutputName
  if (-not $zipFileName.EndsWith('.zip', [System.StringComparison]::OrdinalIgnoreCase)) {
    $zipFileName = "$zipFileName.zip"
  }
} elseif ($Version.Trim()) {
  $versionLabel = Get-SafeFileNamePart $Version
  if ($versionLabel -notmatch '^[vV]') {
    $versionLabel = "v$versionLabel"
  }
  $timestamp = Get-Date -Format 'yyyyMMdd-HHmm'
  $zipFileName = "Yneko-Reimu-$versionLabel-$timestamp.zip"
} else {
  $zipFileName = 'Yneko-Reimu.zip'
}

$zipPath = Join-Path $releaseDir $zipFileName

$allowedRoots = @(
  'inc',
  'template-parts',
  'languages',
  'assets/dist',
  'assets/images'
)

$allowedFiles = @(
  '404.php',
  'archive.php',
  'author.php',
  'category.php',
  'comments.php',
  'footer.php',
  'functions.php',
  'header.php',
  'home.php',
  'index.php',
  'LICENSE.txt',
  'page.php',
  'readme.txt',
  'screenshot.png',
  'search.php',
  'searchform.php',
  'sidebar.php',
  'single.php',
  'style.css',
  'tag.php',
  'theme.json',
  'virtual-page.php'
)

$rootFiles = @(
  'NOTICE.md'
)

$docFiles = @(
  "release-notes-$Version.md"
)

New-Item -ItemType Directory -Path $releaseDir -Force | Out-Null
New-Item -ItemType Directory -Path $stageTheme -Force | Out-Null

foreach ($root in $allowedRoots) {
  $source = Join-Path $themeDir $root
  if (Test-Path -LiteralPath $source) {
    $dest = Join-Path $stageTheme $root
    New-Item -ItemType Directory -Path (Split-Path -Parent $dest) -Force | Out-Null
    Copy-Item -LiteralPath $source -Destination $dest -Recurse -Force
  }
}

foreach ($file in $allowedFiles) {
  $source = Join-Path $themeDir $file
  if (Test-Path -LiteralPath $source) {
    $dest = Join-Path $stageTheme $file
    New-Item -ItemType Directory -Path (Split-Path -Parent $dest) -Force | Out-Null
    Copy-Item -LiteralPath $source -Destination $dest -Force
  }
}

foreach ($file in $rootFiles) {
  $source = Join-Path $repoDir $file
  if (Test-Path -LiteralPath $source) {
    $dest = Join-Path $stageTheme $file
    Copy-Item -LiteralPath $source -Destination $dest -Force
  }
}

foreach ($docFile in $docFiles) {
  $source = Join-Path $repoDir (Join-Path 'docs' $docFile)
  if (Test-Path -LiteralPath $source) {
    $dest = Join-Path $stageTheme (Join-Path 'docs' $docFile)
    New-Item -ItemType Directory -Path (Split-Path -Parent $dest) -Force | Out-Null
    Copy-Item -LiteralPath $source -Destination $dest -Force
  }
}

$removePatterns = @(
  'assets/dist/manifest.json',
  'assets/dist/*.map',
  'assets/dist/taichi.png',
  'assets/dist/xiaohongshu.svg',
  'assets/images/covers/README.md',
  'assets/images/avatar.png',
  'assets/images/avatar.webp',
  'assets/images/banner.png',
  'assets/images/favicon.ico',
  'assets/images/reimu.png',
  'assets/images/projects',
  'assets/images/search-bg.png',
  'assets/images/taichi-fill.png',
  'assets/images/taichi-fill.svg'
)

foreach ($pattern in $removePatterns) {
  $target = Join-Path $stageTheme $pattern
  if ($pattern.Contains('*')) {
    $parent = Split-Path -Parent $target
    $leaf = Split-Path -Leaf $target
    if (Test-Path -LiteralPath $parent) {
      Get-ChildItem -LiteralPath $parent -Filter $leaf -File | ForEach-Object {
        Remove-Item -LiteralPath $_.FullName -Force
      }
    }
  } elseif (Test-Path -LiteralPath $target) {
    if ((Get-Item -LiteralPath $target).PSIsContainer) {
      Remove-Item -LiteralPath $target -Recurse -Force
    } else {
      Remove-Item -LiteralPath $target -Force
    }
  }
}

$translationSourcePatterns = @(
  'languages/*.po',
  'languages/*.pot'
)

foreach ($pattern in $translationSourcePatterns) {
  $target = Join-Path $stageTheme $pattern
  $parent = Split-Path -Parent $target
  $leaf = Split-Path -Leaf $target
  if (Test-Path -LiteralPath $parent) {
    Get-ChildItem -LiteralPath $parent -Filter $leaf -File | ForEach-Object {
      Remove-Item -LiteralPath $_.FullName -Force
    }
  }
}

if (Test-Path -LiteralPath $zipPath) {
  Remove-Item -LiteralPath $zipPath -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
try {
  $stageRootPath = [System.IO.Path]::GetFullPath($stageRoot)
  $files = Get-ChildItem -LiteralPath $stageTheme -Recurse -File
  foreach ($file in $files) {
    $filePath = [System.IO.Path]::GetFullPath($file.FullName)
    $relative = $filePath.Substring($stageRootPath.Length).TrimStart([System.IO.Path]::DirectorySeparatorChar, [System.IO.Path]::AltDirectorySeparatorChar)
    $entryName = $relative.Replace([System.IO.Path]::DirectorySeparatorChar, '/').Replace([System.IO.Path]::AltDirectorySeparatorChar, '/')
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $filePath, $entryName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
  }
} finally {
  $zip.Dispose()
}
Remove-Item -LiteralPath $stageRoot -Recurse -Force

Write-Host "Created $zipPath"
