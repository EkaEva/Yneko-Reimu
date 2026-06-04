# Yneko-Reimu v0.2.2

## 中文

v0.2.2 是一次 PJAX 导航样式回归修复与 comments/profile/auth 维护性收口版本，重点修复文章页、关于页、友链页等页面首次局部进入时，底部分享区和文章信息区布局错乱的问题，同时拆分高风险评论/资料/认证处理器边界。

### 主要更新

- 修复从其它页面通过 PJAX 进入文章或虚拟页面时，QQ/微信分享区域和标签/阅读量区域首次渲染未套用分享增强样式的问题。
- 将 `reimu-share.css` 调整为全局可用的小型增强样式，避免局部页面替换后分享 markup 已插入但 stylesheet 尚未加载。
- 保持分享 JavaScript 运行时的懒加载策略不变：只有页面存在 `.share-wrapper` 时才加载 `reimu-share.js`，微信二维码依旧按点击懒加载。
- 将评论/资料/认证 AJAX handler 拆分到内部 `inc/comments/auth.php`、`profile.php` 和 `mutations.php` 模块，保留 `inc/comments.php` 作为统一入口。
- 保持 action 名、nonce 名、请求字段、返回 JSON、模板 markup 和前端运行时行为不变，并扩展 `npm run check:comments-profile` 覆盖新的 PHP 模块边界。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.2`。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.2.2-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.2.2.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## English

v0.2.2 is a PJAX navigation style-regression and comments/profile/auth maintainability release, focused on the first-render layout of the footer share area and article meta/footer controls while splitting high-risk request-handler boundaries.

### Highlights

- Fixed the first PJAX navigation into posts or virtual pages where QQ/Weixin share controls and tag/view/comment footer controls could render unstyled until a full refresh.
- Made `reimu-share.css` a small global enhancement stylesheet so share markup inserted by PJAX always has its layout rules available.
- Kept the share JavaScript runtime lazy: `reimu-share.js` still loads only when `.share-wrapper` exists, and Weixin QR rendering remains click-lazy.
- Split comments/profile/auth AJAX handlers into internal `inc/comments/auth.php`, `profile.php`, and `mutations.php` modules while keeping `inc/comments.php` as the single entrypoint.
- Preserved action names, nonce names, request fields, JSON response shapes, template markup, and front-end runtime behavior, with `npm run check:comments-profile` expanded to cover the new PHP module boundaries.
- Synced the theme header, PHP constant, and npm package version to `0.2.2`.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.2.2-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.2.2.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
