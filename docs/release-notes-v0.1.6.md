# Yneko-Reimu v0.1.6

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- 请升级到 v0.1.7 或更新版本。若已误绑定，请清理管理员用户的 `_yneko_reimu_github_*` 与旧版 `_yneko_github_*` user meta 后再重新测试。

## 更新亮点

- 修复前台国际化加载时机，英文页面页脚、评论区、标签云、文章目录、上一篇/下一篇等字符串会使用主题内置英文翻译。
- 在主题版本、多语言开关或英文前缀变化时刷新 rewrite rules，修复上传新版后英文分类和归档 URL 可能 404 的问题。
- 分类、标签、日期归档等归档类页面改为按年份分组的归档式年表，不再复用首页卡片流。
- 优化桌面端和移动端文章侧栏行为，更接近上游 Reimu，包括 TOC/导航切换、卡片内部滚动和播放器布局稳定性。
- 优化 TOC 激活逻辑：只展开当前项及父级链，点击跳转期间锁定滚动监听，移动端点击目录后自动关闭抽屉。
- Yneko-Reimu 设置页为友链和曲目增加序号显示，曲目序号与前台 APlayer 列表顺序一致。

## 修复

- 修复发布 ZIP 结构问题，确保 WordPress 上传主题时能识别 `style.css`。
- 修复自定义 Banner 响应式图片源在不同断点下不一致的问题。
- 修复移动端侧栏头像裁切、重复滚动条和长目录滚动体验问题。
- 修复主题头部元信息，提升 WordPress 安装兼容性。

## 说明

- 本版本不需要数据库迁移。
- 友链和音乐设置的数据结构保持不变，序号仅为设置页显示辅助。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- Upgrade to v0.1.7 or later. If an administrator account was accidentally linked, remove the `_yneko_reimu_github_*` and legacy `_yneko_github_*` user meta values before testing again.

## Highlights

- Fixed front-end i18n loading so English pages use the bundled English translations for footer, comments, tag cloud, TOC, and post navigation strings.
- Refreshed multilingual rewrite rules on version/config changes so English category and archive URLs resolve after uploading a new theme build.
- Reworked category, tag, and date archives to use a year-grouped archive timeline instead of the home card layout.
- Improved desktop and mobile article sidebar behavior to better match upstream Reimu, including TOC/common-sidebar switching, internal scrolling, and player layout stability.
- Updated TOC activation to follow upstream behavior: only the active item and its parent chain remain expanded, click navigation locks scroll updates during the jump, and mobile TOC clicks close the drawer.
- Added numbered rows in the Yneko-Reimu settings page for friend links and music tracks. Music numbering matches the front-end APlayer list order.

## Fixes

- Fixed a ZIP packaging issue by normalizing archive entry paths for WordPress theme uploads.
- Fixed custom banner responsive sources so custom banner images are used consistently across breakpoints.
- Fixed mobile drawer clipping and duplicate-scrollbar regressions while keeping long TOC lists scrollable.
- Fixed the theme header metadata for WordPress installation compatibility.

## Notes

- No database migration is required.
- Existing friend-link and music settings keep their current data structure; numbering is display-only in the settings UI.
