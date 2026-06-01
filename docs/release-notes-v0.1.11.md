# Yneko-Reimu v0.1.11

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.11 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。

## 更新亮点

- 友链设置新增独立的“本站友链信息”配置区，可单独配置友链页“本站信息”中的名称、链接、描述和图片。
- “本站友链信息”图片仅接受 WebP 或 PNG，后台会给出格式建议；未配置时继续按站点头像、作者头像、主题内置头像顺序兜底。
- 深色模式的底部与页面下方背景进一步补齐，避免切到深色后 footer 或底部区域仍保持浅色。
- PJAX 语言切换现在会同步替换页脚，切换中文/英文后无需刷新即可让底部文本跟随当前语言。
- PJAX 进入文章页后，文章目录/导航侧栏滚动状态会重新同步，避免从首页进入文章后侧栏无法滚动。
- README 已同步更新 v0.1.11 的友链设置、PJAX 侧栏滚动和主题包说明。

## 修复

- 修复任意页面切换语言后，页脚仍保留切换前语言，必须刷新才生效的问题。
- 修复从首页等页面软导航进入文章后，桌面侧边栏目录或导航无法滚动，刷新后才恢复的问题。
- 修复深色模式下页面底部和 footer 深色不彻底的问题。
- 修复友链页“本站信息”只能使用默认站点资料，无法在后台单独配置的问题。

## 说明

- 本版本不会迁移既有友链、评论或用户数据。
- “本站友链信息”图片建议使用正方形 WebP 或 PNG，推荐 `512x512`，体积控制在 `200KB` 以内。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.1.11.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.11 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.

## Highlights

- Added a dedicated Site friend-link info section for the friend-links page Site info block, with separate name, URL, description, and image fields.
- The Site friend-link image accepts WebP or PNG only, with format guidance in the admin. When empty, the theme falls back to the site avatar, author avatar, then bundled theme avatar.
- Dark mode now covers the footer and lower page background more completely, avoiding leftover light areas.
- PJAX language switching now replaces the footer too, so Chinese/English footer text updates without a full refresh.
- After entering posts through PJAX, the article TOC/navigation sidebar scroll state is resynchronized so the sidebar remains scrollable.
- README has been updated for the v0.1.11 friend-link settings, PJAX sidebar behavior, and theme package notes.

## Fixes

- Fixed footer text staying in the previous language after switching languages without a full refresh.
- Fixed desktop article TOC/navigation sidebars becoming non-scrollable after entering a post through soft navigation.
- Fixed incomplete dark-mode coverage in the page bottom and footer.
- Fixed the friend-links page Site info block lacking a dedicated admin configuration.

## Notes

- This release does not migrate existing friend links, comments, or user data.
- A square WebP or PNG image is recommended for Site friend-link info, ideally `512x512` and under `200KB`.
- GitHub Actions will generate `Yneko-Reimu-v0.1.11.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
