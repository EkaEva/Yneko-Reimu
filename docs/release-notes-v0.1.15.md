# Yneko-Reimu v0.1.15

## 中文

v0.1.15 是一次质量门与发布稳定性更新，重点让本地检查、CI 构建和发布包更可复现。

### 主要更新

- 将主题、PHP 常量和 npm 包版本同步到 `0.1.15`。
- 移除构建 manifest 中每次变化的时间戳字段，避免 `npm run check` 产生无意义 diff。
- 将 PHPCS/WPCS 检查范围扩展到整个 `theme/Yneko-Reimu` PHP 主题目录。
- 升级 Vite 开发依赖，修复旧 esbuild 带来的中等级别 npm audit 提示。
- 新增 Composer lockfile，用于固定 PHP 开发工具链版本。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.1.15-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.1.15.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## English

v0.1.15 focuses on quality gates and release stability so local checks, CI builds, and release packages are easier to reproduce.

### Highlights

- Synced the theme header, PHP constant, and npm package version to `0.1.15`.
- Removed the changing timestamp from the build manifest to avoid meaningless diffs after `npm run check`.
- Expanded PHPCS/WPCS coverage to the full `theme/Yneko-Reimu` PHP theme directory.
- Updated the Vite development dependency to clear the previous moderate npm audit advisory from esbuild.
- Added Composer lockfile support for reproducible PHP development tooling.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.1.15-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.1.15.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
