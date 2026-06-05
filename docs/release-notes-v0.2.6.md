# Yneko-Reimu v0.2.6

## 中文

v0.2.6 是一次稳定性与维护质量版本，重点收口评论/资料/上传高风险代码，继续整理后台设置页模块边界，并为 PJAX、懒加载资源和前台交互重绑增加静态 QA 护栏。本版本不改变公开 AJAX action、nonce、设置 key、表单字段、上传路径、审核 meta、前台经典脚本加载方式或 release ZIP 边界。

### 主要更新

- 拆分评论上传共享 helper 到 `inc/comments/uploads/helpers.php`，集中管理上传目录、文件请求、校验、清理 token 和附件注册等内部逻辑。
- 拆分评论上传/GIF 审核后台动作到 `inc/comments/uploads/admin.php`，保留现有管理员 GIF 库、评论图片/GIF 审核、批准、驳回、撤销和删除行为。
- 拆分个人资料保存 helper 到 `inc/comments/profile-save.php`，把资料字段解析、头像保存、标签准备、邮箱验证、密码和 TOTP 保存步骤从 AJAX callback 中隔离出来。
- 保持评论/资料/上传相关 AJAX action、nonce 名称、请求字段、上传路径、审核 meta 和 JSON 响应形状不变。
- 扩展 `npm run check:comments-profile`，覆盖新增评论上传和 profile-save 模块边界。
- 拆分后台设置页高密度面板：Users、Security、Music 分别移动到 `inc/settings/panels/users.php`、`security.php`、`music.php`。
- 保持后台设置 tab、option key、字段 name、nonce、保存行为和设置位置不变。
- 更新 `check:settings-admin`、`check:auth-security`、`check:config-surface` 和 `check:github-oauth`，让这些合同检查聚合读取新的设置面板模块。
- 新增 `npm run check:pjax-runtime`，静态保护 PJAX link 排除规则、内联配置同步、脚本 replay、搜索/分享/PhotoSwipe 懒 runtime、APlayer 状态保留、登录/资料弹窗状态恢复、Mermaid/KaTeX/代码块增强和评论交互重绑。
- 保持 `assets/dist/reimu.js` 为 WordPress classic script 兼容输出，不默认切换为 module。
- 保持搜索、分享和 PhotoSwipe 为内部懒加载 runtime，不新增公开前端 API。
- 更新 `docs/development.md`、runtime `readme.txt` 和 README 发布引用，记录新的模块边界和 QA 命令。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.6`。

### 说明

- 本版本是维护质量版本，不做视觉重设计。
- 评论、资料、上传、设置页和 PJAX 相关公开接口保持兼容。
- 三个开发阶段均已分别生成本地验证包并通过 `npm run check:package`；最终 release candidate 以 v0.2.6 ZIP 为准。

## English

v0.2.6 is a stability and maintainability release. It tightens the comments/profile/upload surface, continues the settings-admin module cleanup, and adds a static PJAX/runtime QA gate for lazy resources and front-end rebinding. This release does not change public AJAX actions, nonce names, setting keys, form field names, upload paths, review meta, classic script loading, or release ZIP boundaries.

### Highlights

- Split shared comment-upload helpers into `inc/comments/uploads/helpers.php`, covering upload directories, request-file handling, validation, cleanup tokens, and attachment registration.
- Split comment upload/GIF admin review actions into `inc/comments/uploads/admin.php` while preserving the existing GIF library and image/GIF review flows.
- Split profile-save helper steps into `inc/comments/profile-save.php`, isolating request parsing, avatar saving, tag preparation, email verification, password, and TOTP save logic from the AJAX callback.
- Preserved all comments/profile/upload AJAX actions, nonce names, request fields, upload paths, review meta, and JSON response shapes.
- Extended `npm run check:comments-profile` to cover the new comment-upload and profile-save module boundaries.
- Split high-density settings panels into `inc/settings/panels/users.php`, `security.php`, and `music.php`.
- Preserved settings tabs, option keys, field names, nonce usage, save behavior, and UI placement.
- Updated `check:settings-admin`, `check:auth-security`, `check:config-surface`, and `check:github-oauth` so contract checks aggregate the new settings panel modules.
- Added `npm run check:pjax-runtime` to protect PJAX link exclusions, inline config sync, script replay, lazy search/share/PhotoSwipe runtimes, APlayer preservation, login/profile modal restoration, Mermaid/KaTeX/code enhancement rebinding, and comment interaction rebinding.
- Kept `assets/dist/reimu.js` compatible with WordPress classic script loading instead of switching it to an ES module.
- Kept search, share, and PhotoSwipe as internal lazy runtimes without adding public front-end APIs.
- Updated `docs/development.md`, runtime `readme.txt`, and README release references for the new module boundaries and QA command.
- Synced the theme header, PHP constant, and npm package version to `0.2.6`.

### Notes

- This is a maintenance release, not a visual redesign.
- Public comments/profile/upload, settings-admin, and PJAX-related interfaces remain compatible.
- Each of the three development stages generated a local validation package and passed `npm run check:package`; the final v0.2.6 ZIP is the release candidate.
