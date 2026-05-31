# Yneko-Reimu v0.1.9

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.9 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。

## 更新亮点

- WordPress 原生评论改为 AJAX 提交流程：发布评论时页面不再整页刷新，提交按钮会显示加载状态，成功后新评论直接插入评论列表。
- AJAX 评论提交支持普通评论和楼中楼回复；回复评论会插入到对应父评论下方。
- 提交成功后会自动清空评论框、关闭工具面板、更新评论数量，并把新评论滚动到可见区域。
- 评论提交继续走 WordPress 原生审核、反垃圾、重复评论和主题评论上传处理流程，不绕过 v0.1.8 的临时上传和审核逻辑。
- 文章页桌面侧边栏的目录和导航滚动逻辑重新收束：目录过长时只滚动目录区域，导航过长时滚动导航卡片本身。

## 修复

- 修复文章页目录展开后有时无法滚动、有时才出现滚动条的问题。
- 修复文章页侧边栏切换到导航后无法滚动的问题。
- 修复侧边栏 hover 时圆角外侧出现三角阴影伪影的问题。
- 修复评论为空时 AJAX 提交流程没有前端提示的问题。
- 修复 AJAX 渲染新评论时可能污染全局 `$comment` 状态的边界问题。
- 修复构建时上游 CSS 快照残留 `reimu-cursor-*.png` 路径导致 Vite 输出资源解析警告的问题。

## 说明

- 本版本不会迁移既有评论内容。
- 评论无刷新提交仅作用于主题内置的 WordPress 原生评论面板；外部评论系统仍按各自服务的前端逻辑运行。
- 构建警告清理不改变前台 Lily 光标效果，主题适配层仍会覆盖为现有自定义光标。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.9 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.

## Highlights

- Native WordPress comments now submit through AJAX. Posting a comment no longer reloads the page; the submit button shows a loading state and the new comment is inserted into the list.
- AJAX submission supports both top-level comments and threaded replies. Replies are inserted under their parent comment.
- After a successful submission, the comment box is cleared, comment tool panels are closed, the comment count is refreshed, and the new comment is scrolled into view.
- Comment submission still uses WordPress core moderation, anti-spam, duplicate-comment checks, and the theme's v0.1.8 temporary upload approval flow.
- Desktop article sidebars now handle TOC and navigation scrolling predictably: long TOCs scroll inside the TOC area, and long navigation cards scroll inside the navigation panel.

## Fixes

- Fixed inconsistent TOC scrolling after expanding long article outlines.
- Fixed article sidebar navigation becoming non-scrollable after switching from TOC to navigation mode.
- Fixed triangular hover-shadow artifacts outside rounded sidebar corners.
- Added a front-end empty-comment guard for AJAX comment submission.
- Restored global comment state after rendering AJAX-returned comment HTML.
- Removed stale `reimu-cursor-*.png` references from the upstream CSS snapshot so Vite builds no longer emit missing cursor asset warnings.

## Notes

- This release does not migrate existing comments.
- No-reload submission applies to the built-in WordPress comment panel only. External comment systems continue to use their own front-end behavior.
- Cleaning the build warning does not change the front-end Lily cursor behavior; the adapter layer still applies the existing custom cursors.
