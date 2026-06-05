# Yneko-Reimu v0.2.10

## 中文

v0.2.10 是一次评论、登录、个人资料前端运行时和小型体验修复版本。它完成 v0.2.9 延期的 `assets/dist/reimu-comments.js` classic 懒 runtime 拆分，同时修复自定义鼠标指针开启后被主样式变量覆盖的问题。本版本不改变公开主题接口。

### 主要更新

- 新增懒加载的 `assets/dist/reimu-comments.js` classic runtime，承载登录、注册、找回密码、GitHub popup、profile modal、profile save、email/TOTP、头像/标签审核状态、评论提交/回复/点赞/编辑/删除、评论媒体上传/丢弃、登录状态 DOM 替换、PJAX rebind 和 review-status polling。
- 精简主 `assets/dist/reimu.js`：保留 `window.ReimuWP.init()`、PJAX/config 桥接、modal 状态恢复和 comments/profile 锚点触发 loader。
- 固定懒加载触发锚点为 `#comments`、`#respond`、`#reimu-login-modal`、`#reimu-profile-modal`、`[data-reimu-profile-open]`。
- 注册内部脚本 handle `yneko-reimu-comments-runtime`，但保持公开主脚本 handle 和 classic script 加载行为兼容。
- 保持 `assets/dist/reimu-comments.css` 全局加载，避免 PJAX 或 footer modal 场景缺少样式。
- 修复自定义鼠标指针级联顺序：开启后 Lily cursor 变量会在主样式之后再次输出，不再被 `reimu.css` 的默认 cursor 变量覆盖。
- 更新 comments/profile、PJAX runtime、runtime smoke、enqueue 和 size gates，覆盖新的懒 runtime 边界和预算。

### 说明

- 公开设置键、AJAX action、nonce、请求字段、JSON 响应、meta key、hook、模板路径、URL、公开脚本/样式 handle、`window.REIMU_CONFIG` 结构和 release ZIP 边界保持兼容。
- v0.2.10 发布前已完成本地静态验证和真实 WordPress 手工 QA 矩阵。

## English

v0.2.10 is a comments, login, profile front-end runtime and small UX-fix release. It completes the `assets/dist/reimu-comments.js` classic lazy runtime split deferred from v0.2.9 and fixes the custom cursor cascade issue where the enabled cursor variables could be overwritten by the main stylesheet. This release does not change public theme interfaces.

### Highlights

- Added the lazy `assets/dist/reimu-comments.js` classic runtime for login, registration, lost password, GitHub popup, profile modal, profile save, email/TOTP, avatar/tag review state, comment submit/reply/like/edit/delete, comment media upload/discard, login-state DOM replacement, PJAX rebinds, and review-status polling.
- Slimmed the main `assets/dist/reimu.js` runtime so it keeps `window.ReimuWP.init()`, PJAX/config bridges, modal-state restoration, and the comments/profile anchor-triggered loader.
- Fixed the lazy trigger set to `#comments`, `#respond`, `#reimu-login-modal`, `#reimu-profile-modal`, and `[data-reimu-profile-open]`.
- Registered the internal `yneko-reimu-comments-runtime` handle while keeping the public main script handle and classic script loading behavior compatible.
- Kept `assets/dist/reimu-comments.css` globally loaded so PJAX and footer modal contexts keep their styling.
- Fixed the custom cursor cascade so enabled Lily cursor variables are emitted after the main stylesheet and are no longer overwritten by default cursor variables in `reimu.css`.
- Updated comments/profile, PJAX runtime, runtime smoke, enqueue, and size gates for the new lazy runtime boundary and budgets.

### Notes

- Public setting keys, AJAX actions, nonces, request fields, JSON responses, meta keys, hooks, template paths, URLs, public script/style handles, the `window.REIMU_CONFIG` shape, and the release ZIP boundary remain compatible.
- v0.2.10 passed local static validation and the real WordPress manual QA matrix before release.
