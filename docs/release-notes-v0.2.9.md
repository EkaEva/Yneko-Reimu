# Yneko-Reimu v0.2.9

## 中文

v0.2.9 是一次评论、登录、个人资料前端运行时维护版本。它完成 v0.2.8 延期任务中的安全第一步：将 comments/profile 前端 orchestration 从主源码入口拆到独立 source module，同时继续打包进主 classic `assets/dist/reimu.js`。本版本不新增默认功能行为，也不改变公开主题接口。

### 主要更新

- 新增 `assets/src/reimu/comments-profile.js`，集中承载登录、注册、找回密码、GitHub popup、profile modal、profile save、email/TOTP、评论提交/回复/点赞/编辑/删除、评论上传/丢弃、登录状态 DOM 替换、PJAX rebind 和审核状态 polling 的前端编排。
- 保留 `assets/src/reimu.js` 作为主 classic runtime 入口，继续提供 `window.ReimuWP.init()`、PJAX config replay、modal state restore 和 classic script 输出。
- 更新 comments/profile、PJAX runtime、GitHub OAuth 和 runtime smoke contract gates，使它们聚合新的 source module 边界。
- 将 feature loading plan 中 comments/profile 状态更新为 `source-split`。
- 评估独立 `assets/dist/reimu-comments.js` 后继续延期：该拆分需要完整本地 WordPress 手工 QA 覆盖 auth、profile、comment、upload、PJAX、GitHub popup 和 review polling 矩阵。
- 更新 `docs/comments-profile-contract.md` 和 `docs/development.md`，记录 v0.2.9 source runtime 边界和未来懒 runtime 接受条件。
- 将主题版本、PHP 常量、npm 包版本和 runtime `readme.txt` 同步到 `0.2.9`。

### 说明

- `assets/dist/reimu-comments.js` 未在 v0.2.9 发布；公开 runtime 仍是主 `assets/dist/reimu.js`。
- 公开设置键、AJAX action、nonce、请求字段、JSON 响应、meta key、hook、模板路径、URL、脚本/样式 handle、`window.REIMU_CONFIG` 结构和 release ZIP 边界保持兼容。

## English

v0.2.9 is a comments, login, and profile front-end runtime maintenance release. It completes the safest first step of the v0.2.8 deferred work: the comments/profile orchestration is split into a dedicated source module while still building into the main classic `assets/dist/reimu.js` runtime. This release does not add new default feature behavior or change public theme interfaces.

### Highlights

- Added `assets/src/reimu/comments-profile.js` to own the front-end orchestration for login, registration, lost password, GitHub popup, profile modal, profile save, email/TOTP, comment submit/reply/like/edit/delete, comment upload/discard, login-state DOM replacement, PJAX rebinds, and review-status polling.
- Kept `assets/src/reimu.js` as the main classic runtime entrypoint for `window.ReimuWP.init()`, PJAX config replay, modal state restore, and classic script output.
- Updated comments/profile, PJAX runtime, GitHub OAuth, and runtime smoke contract gates so they aggregate the new source module boundary.
- Updated the feature loading plan so comments/profile is now recorded as `source-split`.
- Deferred a standalone `assets/dist/reimu-comments.js` runtime after assessment: that move needs full local WordPress manual QA across auth, profile, comments, uploads, PJAX, GitHub popup recovery, and review-status polling.
- Updated `docs/comments-profile-contract.md` and `docs/development.md` with the v0.2.9 source runtime boundary and future lazy-runtime acceptance conditions.
- Synced the theme header, PHP constant, npm package version, and runtime `readme.txt` to `0.2.9`.

### Notes

- `assets/dist/reimu-comments.js` does not ship in v0.2.9; the public runtime remains the main `assets/dist/reimu.js`.
- Public setting keys, AJAX actions, nonces, request fields, JSON responses, meta keys, hooks, template paths, URLs, script/style handles, the `window.REIMU_CONFIG` shape, and the release ZIP boundary remain compatible.
