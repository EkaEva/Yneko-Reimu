# Yneko-Reimu v0.2.8

## 中文

v0.2.8 是一次维护性闭包版本，重点完成前几版累积的高风险模块拆分，并为后续开发保留更清晰的内部边界。本版本不默认新增功能行为，也不改变设置 key、AJAX action、nonce、请求字段、meta key、hook、模板路径、URL、脚本/样式 handle、`window.REIMU_CONFIG` 结构、classic script 加载方式或 release ZIP 边界。

### 主要更新

- 完成评论/资料/上传 PHP 高风险闭包：`inc/comments.php` 保持入口职责，评论上下文、用户徽章/标签、头像与审核状态、管理员审核动作分别进入内部模块。
- 将 `inc/comments/uploads.php` 拆为上传配置/路径/媒体 helper、共享校验、库查询、前台上传/丢弃 AJAX、提升/清理生命周期、评论审核过滤器和管理员审核模块。
- 将设置 schema 拆为 defaults、normalizers、sanitizers、getters 和 compatibility 模块，保留 `yneko_reimu_settings` option 和 `yneko_reimu_sanitize_settings()` 回调。
- 将 enqueue/resource 层拆为 asset helper、head 输出、样式 enqueue、前端配置、第三方 vendor 和主 classic runtime enqueue 模块。
- 将 Customizer section 注册拆为内部 section 模块，保留所有 panel/section ID、setting/control ID、option-backed 字段、sanitizer 和注册顺序。
- 更新 settings/admin、auth-security、config-surface、enqueue、Customizer、comments/profile、PJAX/runtime 和 GitHub OAuth contract gate，使它们聚合新模块边界。
- 评估 comments/profile 前端 runtime 拆分后，将 `assets/dist/reimu-comments.js` / `reimu-profile.js` 懒 runtime 推迟到未来版本；当前 login/profile/comment/GitHub popup/PJAX/review polling 共享运行时状态，必须先有完整 WordPress 手工 QA 才适合拆。
- 更新 `docs/development.md` 和 `docs/comments-profile-contract.md`，记录新的模块边界、contract 责任和未来 runtime 拆分条件。
- 将主题版本、PHP 常量、npm 包版本和 runtime `readme.txt` 同步到 `0.2.8`。

### 说明

- 这是维护性版本，不做视觉重设计。
- 公开主题运行时接口保持兼容。
- 每个重要阶段都生成了本地验证包并通过 `npm run check:package`；最终 release candidate 以 v0.2.8 ZIP 为准。

## English

v0.2.8 is a maintainability-closure release. It finishes the high-risk module splits accumulated across earlier versions and leaves clearer internal boundaries for future work. This release does not add new default feature behavior and does not change setting keys, AJAX actions, nonces, request fields, meta keys, hooks, template paths, URLs, script/style handles, the `window.REIMU_CONFIG` shape, classic script loading, or the release ZIP boundary.

### Highlights

- Completed the high-risk comments/profile/upload PHP closure: `inc/comments.php` remains the entrypoint while comment context, user badges/tags, avatars/review status, and administrator review actions live in focused internal modules.
- Split `inc/comments/uploads.php` into upload config/path/media helpers, shared validation, library queries, front-end upload/discard AJAX, promotion/cleanup lifecycle tasks, moderation filters, and administrator review modules.
- Split the settings schema into defaults, normalizers, sanitizers, getters, and compatibility modules while preserving the `yneko_reimu_settings` option and `yneko_reimu_sanitize_settings()` callback.
- Split enqueue/resource code into asset helpers, head output, stylesheet enqueueing, front-end config, third-party vendor loading, and main classic runtime enqueue modules.
- Split Customizer section registration into internal section modules while preserving every panel/section ID, setting/control ID, option-backed field, sanitizer, and registration order.
- Updated the settings/admin, auth-security, config-surface, enqueue, Customizer, comments/profile, PJAX/runtime, and GitHub OAuth contract gates so they aggregate the new module boundaries.
- Assessed the comments/profile front-end runtime split and deferred a lazy `assets/dist/reimu-comments.js` / `reimu-profile.js` runtime to a future release; the current login/profile/comment/GitHub popup/PJAX/review-polling flows still share runtime state and need full WordPress manual QA before splitting.
- Updated `docs/development.md` and `docs/comments-profile-contract.md` with the new module boundaries, contract responsibilities, and future runtime split conditions.
- Synced the theme header, PHP constant, npm package version, and runtime `readme.txt` to `0.2.8`.

### Notes

- This is a maintainability release, not a visual redesign.
- Public runtime theme interfaces remain compatible.
- Each important stage generated a local validation package and passed `npm run check:package`; the final v0.2.8 ZIP is the release candidate.
