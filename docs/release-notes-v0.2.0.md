# Yneko-Reimu v0.2.0

## 中文

v0.2.0 是一次架构、质量门与发布稳定性里程碑更新，重点让本地检查、CI 构建、真实登录链路 QA 和发布包更可复现。

### 主要更新

- 将主题、PHP 常量和 npm 包版本同步到 `0.2.0`。
- 移除构建 manifest 中每次变化的时间戳字段，避免 `npm run check` 产生无意义 diff。
- 将 PHPCS/WPCS 检查范围扩展到整个 `theme/Yneko-Reimu` PHP 主题目录。
- 升级 Vite 开发依赖，修复旧 esbuild 带来的中等级别 npm audit 提示。
- 新增 Composer lockfile，用于固定 PHP 开发工具链版本。
- 增加前端运行时拆分、设置页/评论上传模块拆分、包体/发布包/OAuth/i18n/settings 契约检查。
- 完成真实 Email/TOTP 浏览器 QA 与真实 GitHub OAuth happy-path QA。
- 修复 PJAX 中英切换时文章正文 `id="footer"` 与站点页脚冲突导致页脚未更新的问题。
- 将发布 workflow 的官方 actions/runtime 升级到 Node 24 兼容配置。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.2.0-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.2.0.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## English

v0.2.0 is an architecture, quality-gate, and release-stability milestone so local checks, CI builds, real login-flow QA, and release packages are easier to reproduce.

### Highlights

- Synced the theme header, PHP constant, and npm package version to `0.2.0`.
- Removed the changing timestamp from the build manifest to avoid meaningless diffs after `npm run check`.
- Expanded PHPCS/WPCS coverage to the full `theme/Yneko-Reimu` PHP theme directory.
- Updated the Vite development dependency to clear the previous moderate npm audit advisory from esbuild.
- Added Composer lockfile support for reproducible PHP development tooling.
- Added front-end runtime splits, settings/comment-upload module boundaries, and size/package/OAuth/i18n/settings contract checks.
- Completed real browser Email/TOTP QA and real GitHub OAuth happy-path QA.
- Fixed a PJAX footer replacement collision when article content also contains `id="footer"` during language switching.
- Updated the release workflow actions/runtime for Node 24 compatibility.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.2.0-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.2.0.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
