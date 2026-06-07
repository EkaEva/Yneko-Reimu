# Yneko-Reimu v0.2.17

## 中文

v0.2.17 是一个质量加固版本。它不扩展默认启用功能，而是把本地 WordPress + Playwright 回归网补到后台设置页和区块编辑器，减少后续维护区块样板、后台设置、评论/profile/PJAX 时的手工回归压力。

### 主要更新

- 扩展本地 E2E 种子流程，固定管理员账号 `admin / password` 与订阅者账号，并在每次种子运行时清理本地 TOTP 临时状态。
- 新增 Yneko-Reimu 设置页 E2E smoke，覆盖后台设置页面、`assets/dist/admin-settings.js`、主要设置 tab、默认 General 面板和管理员 TOTP 控件。
- 新增区块编辑器 E2E smoke，确认 `assets/dist/reimu-editor.css` 在编辑器中加载，并验证 v0.2.16 的 Reimu block patterns 与 block styles 已注册。
- 扩展 `npm run test:runtime` 的后台/编辑器锚点检查，保护 admin settings runtime、editor stylesheet、pattern slug 和 style class 的实现边界。
- 更新 `docs/e2e-qa.md` 和 PHP complexity baseline，明确 E2E 仍是本地回归网，不替代真实上传、邮件、GitHub OAuth、发布包和高风险评论/profile/auth 手动 QA。

### 说明

- 本版本不新增设置 key、AJAX action、nonce、post meta key、hook、URL、模板路径或公开 script/style handle。
- v0.2.16 新增的 editor pattern slug 与 block style class 保持兼容。
- 旧的 Dependabot 分支基于早期版本，不能直接合并；后续依赖更新应从当前 `main` 重新开分支处理。

## English

v0.2.17 is a quality-hardening release. It does not expand default-enabled features; it widens the local WordPress + Playwright regression net to cover the admin settings page and block editor, reducing manual regression pressure for future editor, settings, comments/profile, and PJAX maintenance.

### Highlights

- Strengthened the local E2E seed so the administrator account `admin / password` and subscriber account are repeatable, with local TOTP state cleaned on each seed run.
- Added a Yneko-Reimu settings page E2E smoke test covering the admin settings page, `assets/dist/admin-settings.js`, primary settings tabs, the default General panel, and administrator TOTP controls.
- Added a block editor E2E smoke test confirming `assets/dist/reimu-editor.css` loads in the editor and the v0.2.16 Reimu block patterns/styles are registered.
- Extended `npm run test:runtime` with backend/editor anchors for the admin settings runtime, editor stylesheet, pattern slugs, and style classes.
- Updated `docs/e2e-qa.md` and the PHP complexity baseline, while documenting that local E2E is still a regression net rather than a replacement for manual QA of real uploads, email delivery, GitHub OAuth, release packaging, and high-risk comments/profile/auth flows.

### Notes

- This release does not add setting keys, AJAX actions, nonces, post meta keys, hooks, URLs, template paths, or public script/style handles.
- The editor pattern slugs and block style classes introduced in v0.2.16 remain compatible.
- The old Dependabot branch was based on an earlier release line and must not be merged directly; future dependency updates should be recreated from current `main`.
