# Yneko-Reimu v0.2.7

## 中文

v0.2.7 是一次专业开源协作与维护治理版本，重点补齐贡献入口、PR/Issue 模板、安全报告流程、Dependabot、PR 质量门、跨平台打包入口、公开维护记录、runtime 烟测和 PHP 复杂度基线。本版本不改变前台视觉行为、设置 key、AJAX action、nonce、模板路径、公开 hook、URL、ZIP 根目录或 classic script 兼容性。

### 主要更新

- 新增 `CONTRIBUTING.md`，记录本地环境、分支/PR 规范、公开接口护栏、高风险区域、必跑检查和依赖更新策略。
- 新增 GitHub PR 模板、Bug/Feature Issue 模板和 `CODEOWNERS`，让外部贡献者更清楚地说明接口影响、检查结果、手动 QA 和包边界。
- 新增 `SECURITY.md`，说明支持版本、私下漏洞报告方式、响应预期，以及登录、上传、OAuth、TOTP、评论审核等敏感范围。
- 新增 `.github/dependabot.yml`，覆盖 GitHub Actions、npm 和 Composer 依赖更新。
- 新增 `.github/workflows/quality.yml`，在 PR 和 `main` push 上运行完整质量门：`npm run check`、`npm audit --audit-level=moderate`、`npm run package` 和 `npm run check:package`。
- 更新 release workflow，使 tag 发布也先跑完整质量门、audit 和包检查后再上传 GitHub Release ZIP。
- 将根目录 `task_plan.md`、`findings.md`、`progress.md` 从 Git 跟踪中移除，并把可公开维护历史整理到 `docs/maintenance-notes/`。
- 新增跨平台 `tools/package-theme.mjs`，让 `npm run package` 在 Windows、Linux、macOS/CI 上通过 `pwsh` 或 Windows PowerShell 调用现有打包脚本。
- 新增 `npm run test:runtime`，对构建后的 classic runtime 脚本做快速烟测，保护 PJAX、懒加载 runtime、评论/资料弹窗、enqueue 和前端全局锚点。
- 新增公开 PHP 复杂度基线 `docs/maintenance-notes/complexity-baseline.md`，继续把复杂度治理作为非失败、可审查的维护指标。
- 更新 README、`docs/development.md` 和 `docs/release.md`，同步贡献入口、工具版本、CI 行为、打包命名和维护记录说明。
- 将主题版本、PHP 常量、npm 包版本和 runtime `readme.txt` 同步到 `0.2.7`。

### 说明

- 本版本是开源协作与维护流程版本，不做视觉重设计。
- 公开主题运行时接口保持兼容。
- 根目录本地 planning 文件仍可在本机作为 agent 工作记忆使用，但不再进入 Git 或 release ZIP。

## English

v0.2.7 is a professional open-source collaboration and maintainability release. It adds contributor onboarding, PR/issue templates, a security reporting process, Dependabot, PR quality gates, cross-platform packaging, public maintenance notes, runtime smoke tests, and a PHP complexity baseline. This release does not change front-end behavior, setting keys, AJAX actions, nonces, template paths, public hooks, URLs, the ZIP root, or classic script compatibility.

### Highlights

- Added `CONTRIBUTING.md` with setup, branch/PR expectations, public-interface guardrails, high-risk areas, required checks, and dependency-update policy.
- Added GitHub PR, bug, and feature templates plus `CODEOWNERS` so contributors can document interface impact, checks, manual QA, and package boundaries.
- Added `SECURITY.md` for supported versions, private vulnerability reporting, response expectations, and sensitive auth/upload/OAuth/TOTP/comment-review surfaces.
- Added `.github/dependabot.yml` for GitHub Actions, npm, and Composer dependency updates.
- Added `.github/workflows/quality.yml` so PRs and pushes to `main` run the full quality gate: `npm run check`, `npm audit --audit-level=moderate`, `npm run package`, and `npm run check:package`.
- Updated the release workflow so tag releases also run the full quality gate, audit, and package validation before uploading the GitHub Release ZIP.
- Removed root `task_plan.md`, `findings.md`, and `progress.md` from Git tracking and moved public maintenance summaries into `docs/maintenance-notes/`.
- Added cross-platform `tools/package-theme.mjs`, allowing `npm run package` to call the existing PowerShell package script through `pwsh` or Windows PowerShell.
- Added `npm run test:runtime` for fast smoke checks against built classic runtime scripts and key PJAX, lazy runtime, comments/profile modal, enqueue, and front-end global anchors.
- Added a public PHP complexity baseline in `docs/maintenance-notes/complexity-baseline.md` as a non-failing review aid.
- Updated README, `docs/development.md`, and `docs/release.md` for contributor entry points, tool versions, CI behavior, package naming, and maintenance notes.
- Synced the theme header, PHP constant, npm package version, and runtime `readme.txt` to `0.2.7`.

### Notes

- This is an open-source collaboration and maintenance-process release, not a visual redesign.
- Public runtime theme interfaces remain compatible.
- Root planning files may still exist locally as agent working memory, but they are no longer tracked by Git or included in release ZIPs.
