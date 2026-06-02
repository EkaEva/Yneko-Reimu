# Yneko-Reimu v0.1.13

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.13 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。
- 本版本新增 WordPress 指纹和作者枚举的主题层兜底，但 `readme.html`、`license.txt`、登录入口保护、目录访问和 Cloudflare/WAF 规则仍建议在服务器或 CDN 层处理。

## 更新亮点

- 移除前台 HTML、RSS/Atom feed 等位置的 WordPress generator 版本号输出，降低针对具体 WordPress 版本的指纹识别风险。
- 阻止前台 `?author=数字` 形式的作者枚举跳转，避免直接暴露后台用户名 slug。
- 增加主题层安全响应头兜底：`X-Content-Type-Options: nosniff`、`X-Frame-Options: SAMEORIGIN`、`Referrer-Policy: strict-origin-when-cross-origin`。
- 尝试移除 `X-Powered-By` 响应头，减少 PHP 版本指纹暴露；若服务器层重新追加，请在 PHP、OpenResty、Nginx 或 Cloudflare 侧关闭。
- 安全响应头可通过 `yneko_reimu_security_headers` filter 调整，方便与 Cloudflare、OpenResty、Nginx 或其它安全插件共存。
- 侧边栏近期评论会识别图片/GIF 评论，并以 `[Image:1]`、`[GIF:1]` 形式显示短摘要，避免长图片 Markdown 链接撑破小组件。
- 评论区新增登录用户头像角标，使用主题色 `personal.svg` 标记注册用户评论。
- 评论区新增七种基础特殊标签：站长、管理员、编辑、作者、贡献者、会员/Yko、订阅者，并按 `站长 > 管理员 > 编辑 > 作者 > 贡献者 > 会员 > 订阅者` 排序。
- 新增用户自定义标签系统：特殊标签和自定义标签合计最多 2 个；用户可在个人资料弹窗中开关自己的特殊标签，并设置最多剩余容量内的自定义标签和颜色。
- 新增自定义标签屏蔽词和用户标签审核。屏蔽词用 `/` 分隔；开启审核后，非管理员的新自定义标签需要后台批准后才会显示。
- 后台“用户设置”新增用户标签审核管理列表，即使未开启审核，也可以查看并撤销某个用户已存在的自定义标签。
- 新增评论区头像框功能：头像框总开关默认关闭；七种基础身份可分别配置 PNG、WebP 或 AVIF 头像框，默认使用主题内置 `assets/images/avatar-frame.png`。
- 用户个人资料弹窗新增“显示我的评论头像框”开关，默认开启；保存后当前页面评论区头像会无刷新更新。
- GitHub 登录用户首次会默认使用 GitHub 主页作为个人主页；用户手动修改后优先使用手动值，清空并保存后评论区名称不再跳转。

## 修复

- 修复默认 WordPress generator meta 暴露 `WordPress 7.0` 的问题。
- 修复 RSS feed `<generator>` 暴露 WordPress 版本号的问题。
- 修复访问 `/?author=1` 会 301 到 `/author/{user}/` 并暴露用户名的问题。
- 修复编辑角色因为继承 WordPress 能力而误显示作者、贡献者、订阅者等多重角色标签的问题。
- 修复个人资料保存标签后必须刷新页面，评论区标签才会更新的问题。
- 修复保留标签或屏蔽词错误提示出现在资料弹窗底部、不易发现的问题；现在会在“评论标签”卡片内提示并标红对应输入框。
- 修复清空个人主页后，评论区名称仍 fallback 到 GitHub 链接的问题。
- 修复用户开关头像框后，评论区头像框状态必须刷新页面才同步的问题。

## 说明

- 本版本不会删除既有主题设置、评论、用户、友链、曲目或媒体库数据。
- 用户标签原名、当前特殊标签显示名和自定义屏蔽词都会作为保留词，普通用户不能自行设置为自定义标签。
- 头像框总开关关闭时，前台不会显示头像框；用户个人开关只影响自己的头像框，不影响头像和标签。
- `readme.html` 和 `license.txt` 是 WordPress 根目录静态文件，主题无法可靠删除或拦截。请在 1Panel、FTP、SSH、OpenResty/Nginx 或 Cloudflare 规则中删除或拦截这两个文件。
- REST API 用户接口、登录入口、目录列表、XML-RPC 等站点安全项仍建议继续用 Cloudflare 和服务器规则兜底。
- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.1.13-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.1.13.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.13 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.
- This release adds theme-level fallbacks for WordPress fingerprinting and author enumeration. Static files such as `readme.html` and `license.txt`, login-route protection, directory access, and Cloudflare/WAF rules should still be handled at the server or CDN layer.

## Highlights

- Removed WordPress generator version output from front-end HTML and RSS/Atom feeds to reduce WordPress-version fingerprinting.
- Blocked front-end `?author=number` author-enumeration redirects so user slugs are not exposed through numeric author probes.
- Added theme-level fallback security headers: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin`.
- Attempted to remove the `X-Powered-By` response header to reduce PHP-version fingerprinting; if the server adds it back, disable it at the PHP, OpenResty, Nginx, or Cloudflare layer.
- Security headers can be adjusted with the `yneko_reimu_security_headers` filter, making the theme easier to combine with Cloudflare, OpenResty, Nginx, or security plugins.
- Sidebar recent comments now summarize image/GIF-only comments as `[Image:1]` or `[GIF:1]`, avoiding long Markdown image URLs inside compact widgets.
- WordPress comments now show a theme-colored `personal.svg` marker on registered-user avatars.
- Added seven base special badges: Owner, Admin, Editor, Author, Contributor, Member/Yko, and Subscriber, ordered by `Owner > Admin > Editor > Author > Contributor > Member > Subscriber`.
- Added user custom badges. Special badges and custom badges share a maximum display capacity of 2; users can toggle their own special badges and set custom badge labels/colors within the remaining capacity.
- Added custom-badge blocklist and user-badge review. Blocked labels are separated with `/`; when review is enabled, new custom badges from non-administrators require approval before they display.
- Added a user badge review list in User settings. Existing custom badges can be reviewed and revoked per user even when review is disabled.
- Added comment avatar frames. The master switch is off by default; each base identity can configure a PNG, WebP, or AVIF frame, defaulting to the bundled `assets/images/avatar-frame.png`.
- Added a profile-modal switch for users to show or hide their own comment avatar frame. It is enabled by default and updates visible comments without a full page reload.
- GitHub-login users receive their GitHub profile URL as the initial website value. Manual changes take priority, and clearing the field removes the comment-name link.

## Fixes

- Fixed the default WordPress generator meta exposing `WordPress 7.0`.
- Fixed RSS feed `<generator>` exposing the WordPress version.
- Fixed `/?author=1` redirecting to `/author/{user}/` and exposing the username slug.
- Fixed Editor users incorrectly receiving Author, Contributor, and Subscriber role badges because inherited WordPress capabilities were treated as roles.
- Fixed profile badge changes requiring a full refresh before visible comments updated.
- Fixed reserved/blocked custom-badge errors appearing at the bottom of the profile modal; they now appear inside the comment-badge card and mark the related input.
- Fixed cleared profile URLs still falling back to GitHub profile links in comment author names.
- Fixed avatar-frame visibility changes requiring a page refresh before visible comments updated.

## Notes

- This release does not delete existing settings, comments, users, friend links, music tracks, or Media Library data.
- Original special-badge names, current special-badge labels, and custom blocked labels are all reserved and cannot be used as user custom badges.
- When the avatar-frame master switch is off, frames are not displayed. The per-user frame switch affects only that user's frame, not their avatar or badges.
- `readme.html` and `license.txt` are static files in the WordPress root, so the theme cannot reliably delete or block them. Remove or block them through 1Panel, FTP, SSH, OpenResty/Nginx, or Cloudflare rules.
- REST API user endpoints, login routes, directory listing, XML-RPC, and similar site-level protections should still be handled with Cloudflare and server rules.
- Local validation packages use timestamped names such as `Yneko-Reimu-v0.1.13-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.1.13.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
