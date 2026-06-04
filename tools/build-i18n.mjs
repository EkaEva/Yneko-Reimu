import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const repoDir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const themeDir = path.join(repoDir, 'theme', 'Yneko-Reimu');
const languagesDir = path.join(themeDir, 'languages');
const textDomain = 'yneko-reimu';

const enTranslations = new Map(Object.entries({
  '侧栏小组件': 'Sidebar widgets',
  '控制侧栏作者卡下方的主题内置小组件。标签云默认开启，其它默认关闭；搜索入口已在顶部导航中提供。': 'Controls the theme built-in widgets below the sidebar author card. Tag cloud is enabled by default, all others are disabled; search is already available in the top navigation.',
  '小组件排序': 'Widget order',
  '使用英文逗号分隔：tagcloud, projects, recent_posts, categories, archives, recent_comments。': 'Separate with English commas: tagcloud, projects, recent_posts, categories, archives, recent_comments.',
  '项目数量': 'Project count',
  '近期文章': 'Recent posts',
  '近期文章数量': 'Recent post count',
  '近期评论': 'Recent comments',
  '近期评论数量': 'Recent comment count',
  '归档数量': 'Archive count',
  '分类数量': 'Category count',
  '管理主题内置的导航、侧栏作者卡、首页胶囊和播放器位置。侧栏下方的小组件可在“侧栏小组件”中单独控制。': 'Manages the theme built-in navigation, sidebar author card, home capsules, and player position. Widgets below the sidebar can be controlled separately in Sidebar Widgets.',
  '开启后侧栏由主题自动生成，显示作者卡、站点统计、社交链接和菜单。': 'When enabled, the theme generates the sidebar with the author card, site stats, social links, and menu.',
  'Yneko-Reimu 设置': 'Yneko-Reimu Settings',
  '这些内容保存在 WordPress 数据库中，不会写入主题源码或主题包。': 'These settings are stored in the WordPress database and are never written into the theme source or release package.',
  '站点资料': 'Site Profile',
  '站点头像': 'Site Avatar',
  '作者头像': 'Author Avatar',
  '游客评论头像': 'Guest Comment Avatar',
  '选择图片': 'Choose Image',
  'GitHub 主页链接': 'GitHub Profile URL',
  '赞助二维码': 'Sponsor QR Code',
  '用于站点图标、默认 logo、分享图标兜底等站点级图片。': 'Used as the site icon, default logo, and fallback sharing image.',
  '用于前台侧栏作者卡、页面角色图、友链/项目缺省图；不覆盖 WordPress 用户资料头像。': 'Used by the front-end author card, character image, and friend/project fallback images; it does not override WordPress user avatars.',
  '用于未登录访客评论的默认头像。留空时使用 One User Avatar 的全站默认头像，再留空则使用作者头像。': 'Default avatar for logged-out commenters. If empty, One User Avatar site default is used first, then the author avatar.',
  '统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。': 'Used by the GitHub corner ribbon, sidebar GitHub link, and project-page repository source.',
  '用于底部赞助入口。留空时不会显示赞助二维码。': 'Used by the footer sponsor entry. If empty, the sponsor QR code is hidden.',
  '多语言设置': 'Multilingual Settings',
  '启用语言切换': 'Enable Language Switcher',
  '显示前台语言切换入口': 'Show the front-end language switcher',
  '默认语言': 'Default Language',
  '默认建议保持简体中文，中文内容继续使用站点原始地址。': 'Keeping Simplified Chinese as the default is recommended; Chinese content keeps the original site URLs.',
  '英文路径前缀': 'English URL Prefix',
  '例如 en 会让英文内容使用 /en/ 开头的地址。': 'For example, en makes English content use URLs starting with /en/.',
  '中文显示名': 'Chinese Label',
  '英文显示名': 'English Label',
  '显示在前台语言切换菜单中的中文名称。': 'The Chinese language name shown in the front-end language switcher.',
  '显示在前台语言切换菜单中的英文名称。': 'The English language name shown in the front-end language switcher.',
  'GitHub 登录': 'GitHub Login',
  '回调地址': 'Callback URL',
  '留空时自动使用下方默认地址；如果站点经过反向代理、固定域名或特殊登录路径，可在这里覆盖。GitHub OAuth App 中的 Authorization callback URL 需要与最终地址完全一致。': 'Leave empty to use the default URL below. Override it when the site uses a reverse proxy, fixed public domain, or custom login path. The Authorization callback URL in GitHub OAuth App must match the final URL exactly.',
  '客户端 ID': 'Client ID',
  '客户端密钥': 'Client Secret',
  '填写 GitHub OAuth App 提供的 Client ID。留空时前台不显示 GitHub 登录按钮。': 'Enter the Client ID from your GitHub OAuth App. If empty, the GitHub login button is hidden on the front end.',
  '密钥只保存在 WordPress 数据库中，不会写入主题源码或发布包。': 'The secret is stored only in the WordPress database and is never written into the theme source or release package.',
  '自动创建用户': 'Auto-create Users',
  '允许 GitHub 登录自动创建 WordPress 用户': 'Allow GitHub login to create WordPress users automatically',
  '友链列表': 'Friend Links',
  '用于友链页面的卡片列表，支持名称、链接、描述和头像。': 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.',
  '新增友链': 'Add Friend',
  '音乐列表': 'Music Playlist',
  '播放器曲目从媒体库读取。未配置曲目时，前台不会加载音乐播放器。': 'The player reads tracks from the Media Library. If no tracks are configured, the front-end music player is not loaded.',
  '新增曲目': 'Add Track',
  '保存设置': 'Save Settings',
  '名称': 'Name',
  '链接': 'URL',
  '描述': 'Description',
  '头像': 'Avatar',
  '选择': 'Choose',
  '删除': 'Remove',
  '歌名': 'Track Title',
  '作者': 'Artist',
  '音频': 'Audio',
  '封面': 'Cover',
  '歌词 LRC': 'Lyrics LRC',
  '主题色': 'Theme Color',
  '选择媒体': 'Select Media',
  '使用此媒体': 'Use This Media',
  '内容语言': 'Content Language',
  '对应翻译文章/页面': 'Linked Translation Post/Page',
  '无对应内容': 'No linked translation',
  '用于前台语言切换。保存后主题会自动同步对方文章的对应关系。': 'Used by the front-end language switcher. The theme syncs the reverse relation after saving.',
  '复制': 'Copy',
  '折叠代码': 'Collapse Code',
  '展开代码': 'Expand Code',
  '复制成功 (*^▽^*)': 'Copied (*^▽^*)',
  '复制失败 (ﾟ⊿ﾟ)ﾂ': 'Copy failed (ﾟ⊿ﾟ)ﾂ',
  '输入关键词后按回车搜索。': 'Type keywords and press Enter to search.',
  '少女检索中...': 'Searching...',
  '找到 {count} 条结果': '{count} results found',
  '未发现与「{query}」相关内容': 'No content found for "{query}"',
  '无标题': 'Untitled',
  '没有结果': 'No results',
  '本地搜索索引加载失败。': 'Local search index failed to load.',
  '加载更多...': 'Load more...',
  '到底了...': 'No more...',
  '还没有内容。': 'No content yet.',
  '暂时没有内容': 'No content yet',
  '请稍后再来或联系管理员处理。': 'Please come back later or contact the administrator.',
  '没有文章': 'No posts',
  '字': 'words',
  'words': 'words',
  '共 $1 篇文章, $2 字': '$1 post(s), $2 word(s) in total',
  '$1 post(s), $2 word(s) in total': '$1 post(s), $2 word(s) in total',
  '{date} 没有写作': 'No writing on {date}',
  'No writing on {date}': 'No writing on {date}',
  '{posts} {words} 于 {date}': '{posts} {words} on {date}',
  '{posts}, {words} on {date}': '{posts}, {words} on {date}',
  '{posts} {words} 于 {year}': '{posts} {words} in {year}',
  '{posts}, {words} in {year}': '{posts}, {words} in {year}',
  '请输入 http(s) 图片地址': 'Please enter an http(s) image URL',
  '取消回复': 'Cancel reply',
  '回复评论': 'Reply to comment',
  '登录中...': 'Logging in...',
  '登录成功，正在刷新...': 'Login successful, refreshing...',
  '登录失败，请检查账号和密码。': 'Login failed. Please check your account and password.',
  '图片预览': 'Image preview',
  '关闭预览': 'Close preview',
  '上一张图片': 'Previous image',
  '下一张图片': 'Next image',
  '登录失败。': 'Login failed.',
  '登录': 'Login',
  '使用 WordPress 账号登录后即可评论。': 'Log in with your WordPress account to comment.',
  '关闭登录窗口': 'Close login window',
  '使用 GitHub 登录': 'Login with GitHub',
  '登录成功。': 'Login successful.',
  '评论不存在。': 'Comment does not exist.',
  '评论环境信息': 'Comment environment information',
  '评论正在等待审核。': 'Your comment is awaiting moderation.',
  '评论操作': 'Comment actions',
  '回复': 'Reply',
  '搜索': 'Search',
  '搜索.....': 'Search.....',
  '搜索文章...': 'Search posts...',
  '提交搜索': 'Submit search',
  '关闭搜索': 'Close search',
  '语言选择': 'Language selection',
  '语言菜单': 'Language menu',
  '语言': 'Language',
  '主导航': 'Main navigation',
  '切换导航': 'Toggle navigation',
  '辅助导航': 'Auxiliary navigation',
  'RSS 订阅': 'RSS feed',
  '站点头图': 'Site header image',
  '首页': 'Home',
  'Home': 'Home',
  '项目': 'Projects',
  'Projects': 'Projects',
  '归档': 'Archives',
  'Archives': 'Archives',
  '关于': 'About',
  'About': 'About',
  '友链': 'Friends',
  'Friends': 'Friends',
  '关于这个站点与作者。': 'About this site and author.',
  'GitHub 项目与作品。': 'GitHub projects and works.',
  '按时间整理全部文章。': 'All posts organized by time.',
  '朋友们的站点入口。': 'Entry points to friends’ sites.',
  '搜索：%s': 'Search: %s',
  '以下是与你输入关键词相关的文章。': 'Posts related to your keywords are listed below.',
  '404（´◔ ₃ ◔`)': '404（´◔ ₃ ◔`)',
  '少年，你迷路了吗？': 'Hey, are you lost?',
  '总访问量': 'Total Views',
  '总访客量': 'Total Visitors',
  '基于 %1$s，Theme 基于 %2$s 改编': 'Based on %1$s. Theme adapted from %2$s.',
  '请在“外观 -> Yneko-Reimu 设置”中配置 GitHub 主页链接。': 'Please configure your GitHub profile URL in Appearance -> Yneko-Reimu Settings.',
  '配置 GitHub 主页链接后会自动同步 starred 项目。': 'Starred projects will be synced after a GitHub profile URL is configured.',
  '赞助': 'Sponsor',
  '无限进步': 'Keep improving',
  '我的 GitHub 主页与项目索引。': 'My GitHub profile and project index.',
  'GitHub 项目': 'GitHub Project',
  'Yneko-Reimu 主题作者': 'Yneko-Reimu theme author',
  'hexo-theme-reimu 原作者': 'Original hexo-theme-reimu author',
  '莉莉概念光标作者': 'Lily concept cursor author',
  '未来有你...': 'Loading...',
  '少女祈祷中...': 'Loading...',
  '(无标题)': '(Untitled)',
  '%d 分钟阅读': '%d min read',
  '%d 篇文章': '%d posts',
  '%d 字': '%d words',
  '%s 留言': '%s comments',
  '%s 评论': '%s comments',
  '%s 阅读量': '%s views',
  '%s 字': '%s words',
  '© %s. Powered by WordPress.': '© %s. Powered by WordPress.',
  '按倒序': 'Newest first',
  '按热度': 'By popularity',
  '按正序': 'Oldest first',
  '暗色': 'Dark',
  '浅色': 'Light',
  '暗色模式默认值': 'Default dark-mode setting',
  '版权框': 'Copyright box',
  '版权文本': 'Copyright text',
  '本博客所有文章除特别声明外，均采用 %s 许可协议。转载请注明出处！': 'Unless otherwise stated, all posts on this blog are licensed under %s. Please credit the source when reposting.',
  '本地搜索 JSON URL': 'Local search JSON URL',
  '本地搜索入口': 'Local search entry',
  '本文版权：': 'Copyright:',
  '本文链接：': 'Link:',
  '本文作者：': 'Author:',
  '本站信息': 'Site info',
  '编辑': 'Edit',
  '标签': 'Tags',
  '标签云': 'Tag cloud',
  '表情': 'Emoji',
  '表情包': 'Stickers',
  '播放列表最大高度': 'Playlist max height',
  '播放器互斥': 'Player mutex',
  '播放器位置': 'Player position',
  '播放顺序': 'Play order',
  '博客卡片': 'Blog cards',
  '不蒜子统计': 'Busuanzi statistics',
  '侧边栏': 'Sidebar',
  '侧边栏位置': 'Sidebar position',
  '侧栏卡片之后': 'After sidebar card',
  '侧栏卡片之前': 'Before sidebar card',
  '插入': 'Insert',
  '插入 GIF': 'Insert GIF',
  '插入图片': 'Insert image',
  '代码块折叠高度': 'Code collapse height',
  '导航滚动隐藏': 'Hide nav on scroll',
  '导航链接：%s': 'Navigation URL: %s',
  '导航文字：%s': 'Navigation label: %s',
  '点赞': 'Like',
  '分类': 'Categories',
  '分页导航': 'Pagination',
  '改编': 'Adapted',
  'Theme 基于': 'Theme adapted from',
  '歌词模式': 'Lyrics mode',
  '跟随 WordPress 置顶': 'Follow WordPress sticky state',
  '跟随全局': 'Follow global setting',
  '跟随文章设置': 'Follow post setting',
  '跟随系统': 'Follow system',
  '固定播放器': 'Fixed player',
  '固定导航': 'Fixed navigation',
  '关闭': 'Off',
  '关键词': 'Keywords',
  '关于页待编辑': 'About page placeholder',
  '关于页简介': 'About page intro',
  '关于与友链': 'About and friends',
  '过期提示': 'Outdated notice',
  '过期天数阈值': 'Outdated-day threshold',
  '还没有评论，来抢一张小板凳吧。': 'No comments yet. Be the first to leave one.',
  '还没有文章': 'No posts yet',
  '横幅与图片': 'Banners and images',
  '欢迎评论': 'Comments are welcome',
  '换个关键词，或者稍后再来看看。': 'Try another keyword, or check back later.',
  '回到顶部太极按钮': 'Back-to-top Taichi button',
  '保存': 'Save',
  '或使用 WordPress 账号': 'Or use a WordPress account',
  '或使用 GitHub 登录': 'Or use GitHub',
  '基于': 'Based on',
  '记住我': 'Remember me',
  '加载动画': 'Loading animation',
  '加载动画文案': 'Loading animation text',
  '可使用 {year} 作为年份占位。': 'You can use {year} as the year placeholder.',
  '密码': 'Password',
  '登录': 'Login',
  '登录中...': 'Logging in...',
  '登录成功。': 'Login successful.',
  '登录失败。': 'Login failed.',
  '关闭登录窗口': 'Close login window',
  '关闭个人资料窗口': 'Close profile window',
  '编辑个人资料': 'Edit profile',
  '请输入邮箱和密码。': 'Please enter your email and password.',
  '登录信息已过期，请重试。': 'Login information has expired. Please try again.',
  '两步验证码': 'Two-factor code',
  '请输入两步验证码。': 'Please enter your two-factor code.',
  '两步验证码不正确。': 'The two-factor code is incorrect.',
  '显示密码': 'Show password',
  '隐藏密码': 'Hide password',
  '注册': 'Register',
  '注册中...': 'Registering...',
  '注册成功，请返回登录。': 'Registration successful. Please return to login.',
  '当前未开放注册。': 'Registration is currently closed.',
  '验证邮箱后即可创建账号。': 'Verify your email to create an account.',
  '忘记密码？': 'Forgot password?',
  '验证邮箱后即可重置密码。': 'Verify your email to reset your password.',
  '返回登录': 'Back to login',
  '重置中...': 'Resetting...',
  '重置密码': 'Reset password',
  '新密码': 'New password',
  '邮箱验证码': 'Email verification code',
  '发送验证码': 'Send code',
  '发送中...': 'Sending...',
  '%s 秒后重发': 'Resend in %s seconds',
  '验证码会发送到您的邮箱，5 分钟内有效。': 'The code will be sent to your email and is valid for 5 minutes.',
  '验证码会发送到账号邮箱，5 分钟内有效。': 'The code will be sent to the account email and is valid for 5 minutes.',
  '请输入 6 位邮箱验证码。': 'Please enter the 6-digit email code.',
  '验证码已发送，请稍后再试。': 'The code has been sent. Please try again later.',
  '验证码已发送，请检查您的邮箱。': 'The code has been sent. Please check your email.',
  '验证码邮件发送失败，请稍后重试。': 'Failed to send the verification email. Please try again later.',
  '验证码已失效，请重新获取。': 'The code has expired. Please request a new one.',
  '验证码错误次数过多，请重新获取。': 'Too many incorrect code attempts. Please request a new one.',
  '验证码不正确。': 'The code is incorrect.',
  '验证码不正确或已失效。': 'The code is incorrect or has expired.',
  '如果该邮箱已注册，验证码将发送到对应邮箱。': 'If this email is registered, a code will be sent to it.',
  '请输入注册邮箱。': 'Please enter the registered email address.',
  '[%s] 注册验证码': '[%s] Registration verification code',
  '[%s] 密码重置验证码': '[%s] Password reset verification code',
  '[%s] 邮箱修改验证码': '[%s] Email change verification code',
  '您的注册验证码是：%1$s': 'Your registration verification code is: %1$s',
  '您的密码重置验证码是：%1$s': 'Your password reset verification code is: %1$s',
  '您的邮箱修改验证码是：%1$s': 'Your email change verification code is: %1$s',
  '该验证码将在 %2$d 分钟后失效。': 'This code expires in %2$d minutes.',
  '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请忽略这封邮件。': 'This code expires in %2$d minutes. If this was not you, please ignore this email.',
  '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请立即检查账号安全。': 'This code expires in %2$d minutes. If this was not you, please check your account security immediately.',
  '密码至少需要 8 个字符。': 'Password must be at least 8 characters.',
  '密码已重置，请返回登录。': 'Password reset. Please return to login.',
  '个人资料': 'Profile',
  '个人资料已保存，头像审核中。': 'Profile saved. Avatar is pending review.',
  '个人资料已保存，评论标签审核中。': 'Profile saved. Comment badges are pending review.',
  '个人资料已保存。': 'Profile saved.',
  '头像审核不通过': 'Avatar review rejected',
  '标签审核中': 'Badge pending review',
  '标签审核不通过': 'Badge review rejected',
  '评论审核中': 'Comment pending review',
  '评论审核不通过': 'Comment review rejected',
  '头像链接': 'Avatar URL',
  '头像上传失败。': 'Avatar upload failed.',
  '头像审核中': 'Avatar pending review',
  '头像文件超过大小限制。': 'Avatar file exceeds the size limit.',
  '个人主页': 'Website',
  '新邮箱': 'New email',
  '新邮箱验证码': 'New email code',
  '确认新密码': 'Confirm new password',
  '开启认证器两步验证': 'Enable authenticator two-factor verification',
  '生成密钥': 'Generate key',
  '认证器验证码': 'Authenticator code',
  '取消': 'Cancel',
  '上传': 'Upload',
  '上传中...': 'Uploading...',
  '上传失败。': 'Upload failed.',
  '当前未开启头像上传。': 'Avatar upload is currently disabled.',
  '请输入有效的邮箱地址。': 'Please enter a valid email address.',
  '新邮箱地址不要与原邮箱地址重复。': 'The new email must differ from the current email.',
  '该邮箱已被注册。': 'This email is already registered.',
  '邮箱验证码不正确或已失效。': 'The email code is incorrect or has expired.',
  '两次输入的密码不一致。': 'The two passwords do not match.',
  '请先登录。': 'Please log in first.',
  '请先生成认证器密钥。': 'Please generate an authenticator key first.',
  '认证器验证码不正确。': 'The authenticator code is incorrect.',
  '请用认证器扫码，并输入 6 位验证码后保存。': 'Scan with your authenticator app, then enter the 6-digit code and save.',
  '请输入 1 到 50 个字符的昵称。': 'Please enter a nickname between 1 and 50 characters.',
  '请输入有效的昵称。': 'Please enter a valid nickname.',
  '昵称不能超过 50 个字符。': 'Nickname cannot exceed 50 characters.',
  '退出': 'Logout',
  '退出登录': 'Log out',
  '已退出登录。': 'Logged out.',
  '登录后可上传 GIF。': 'Log in to upload GIFs.',
  '登录后可上传图片。': 'Log in to upload images.',
  '请选择要上传的文件。': 'Please choose a file to upload.',
  '请选择要上传的 GIF。': 'Please choose a GIF to upload.',
  '请选择 GIF 文件。': 'Please choose a GIF file.',
  '请先选择文件。': 'Please choose a file first.',
  '文件大小超出限制。': 'File size exceeds the limit.',
  '文件已上传，等待管理员审核。': 'File uploaded and pending administrator review.',
  '仅支持 JPG、PNG、WebP 和 GIF。': 'Only JPG, PNG, WebP, and GIF are supported.',
  '评论图片上传已关闭。': 'Comment image uploads are disabled.',
  '图片上传不支持 GIF，请使用 GIF 按钮。': 'Image upload does not support GIFs. Please use the GIF button.',
  '图片已上传。': 'Image uploaded.',
  '已插入评论。': 'Inserted into the comment.',
  '评论提交失败。': 'Comment submission failed.',
  '评论已发布。': 'Comment published.',
  '评论已提交，正在等待审核。': 'Comment submitted and awaiting moderation.',
  '评论已更新。': 'Comment updated.',
  '评论已删除。': 'Comment deleted.',
  '无效的评论上传附件。': 'Invalid comment upload attachment.',
  '权限不足。': 'Insufficient permissions.',
  '提交中...': 'Submitting...',
  '插入 GIF 链接': 'Insert GIF link',
  '插入图片链接': 'Insert image link',
  '暂无可选...': 'Nothing available...',
  '未知用户': 'Unknown user',
  '默认横幅图片': 'Default banner image',
  '默认卡片封面': 'Default card cover',
  '默认头像/角色图': 'Default avatar/character image',
  '默认音量 0-1': 'Default volume 0-1',
  '默认折叠播放列表': 'Fold playlist by default',
  '昵称': 'Nickname',
  '篇文章': 'posts',
  '评论': 'Comments',
  '评论排序': 'Comment order',
  '评论区': 'Comments',
  '评论已关闭。': 'Comments are closed.',
  '使用主题内置侧栏': 'Use theme built-in sidebar',
  '起始年份': 'Start year',
  '强调色': 'Accent color',
  '切换明暗模式': 'Toggle color scheme',
  '请输入用户名和密码。': 'Please enter a username and password.',
  '上传图片': 'Upload image',
  '上一篇': 'Previous post',
  '上一篇：%s': 'Previous: %s',
  '上一页': 'Previous page',
  '社交链接': 'Social links',
  '申请方法': 'How to apply',
  '视觉主题': 'Visual theme',
  '首页胶囊标题': 'Home capsule title',
  '首页胶囊封面 %d': 'Home capsule cover %d',
  '首页胶囊链接': 'Home capsule URL',
  '鼠标烟花': 'Mouse firework',
  '说些什么吧！': 'Say something!',
  '搜索弹窗背景图': 'Search popup background',
  '提交': 'Submit',
  '跳到内容': 'Skip to content',
  '推荐项目待同步': 'Recommended projects pending sync',
  '网址（可选）': 'Website (optional)',
  '忘记密码？': 'Forgot password?',
  '文章': 'Posts',
  '文章导航': 'Post navigation',
  '文章目录': 'Table of contents',
  '文章页': 'Post page',
  '文章摘要/副标题': 'Post summary/subtitle',
  '我的项目': 'My projects',
  '下一篇': 'Next post',
  '下一篇：%s': 'Next: %s',
  '下一页 »': 'Next page »',
  '显示': 'Show',
  '显示 TOC': 'Show TOC',
  '显示暗色模式切换': 'Show dark-mode toggle',
  '显示版权框': 'Show copyright box',
  '显示标签': 'Show tags',
  '显示分类': 'Show categories',
  '显示过期提示': 'Show outdated notice',
  '显示评论数': 'Show comment count',
  '显示上一篇/下一篇': 'Show previous/next post',
  '显示太极装饰': 'Show Taichi decoration',
  '显示阅读时间': 'Show reading time',
  '项目待同步': 'Projects pending sync',
  '项目推荐': 'Recommended projects',
  '项目信息': 'Project info',
  '小工具之后': 'After widgets',
  '小伙伴们': 'Friends',
  '学习笔记': 'Study notes',
  '循环模式': 'Loop mode',
  '在内置侧栏中显示标签云': 'Show tag cloud in built-in sidebar',
  '页脚': 'Footer',
  '页脚额外署名': 'Extra footer credit',
  '页面：': 'Page:',
  '页面内容': 'Page content',
  '右侧': 'Right',
  '左侧': 'Left',
  '隐藏': 'Hide',
  '置顶标识': 'Sticky marker',
  '阅读': 'Read',
  '阅读量': 'Views',
  '阅读时间': 'Reading time',
  '用户名或邮箱': 'Username or email',
  '用户名': 'Username',
  '友情链接': 'Friend links',
  '友情链接标题': 'Friend link title',
  '友链页说明': 'Friend page description',
  '语言入口': 'Language entry',
  '暂无项目': 'No projects yet',
  '主题设置': 'Theme settings',
  '自定义 Banner URL': 'Custom banner URL',
  '自定义封面 URL': 'Custom cover URL',
  '开启后在作者卡下方显示主题自带标签云。仅在“使用主题内置侧栏”开启时生效。': 'Shows the theme tag cloud below the author card. Only works when "Use theme built-in sidebar" is enabled.',
  '创建 slug 为 about 的 WordPress 页面后，这里会自动显示你的页面正文。': 'Create a WordPress page with the slug about, and its content will be shown here automatically.',
  '发布文章后，这里会按年份生成归档列表。': 'After publishing posts, archives will be generated here by year.',
  '开启后侧栏由主题自动生成，显示作者卡、站点统计、社交链接和菜单；关闭后改用 WordPress 小工具区。': 'When enabled, the theme generates the sidebar with the author card, site stats, social links, and menu. When disabled, WordPress widgets are used instead.',
  '可在 Customizer 的“关于与友链”中配置，或创建 slug 为 friend 的真实页面覆盖。': 'Configure this in Customizer -> About and Friends, or create a real page with the slug friend to override it.',
  '控制 Yneko-Reimu 的视觉、文章和社交入口。': 'Control Yneko-Reimu visuals, posts, and social entry points.',
  '扩展默认关闭，填写配置或开启后才加载外部脚本。': 'Extensions are disabled by default; external scripts load only after configuration or enabling.',
  '扩展默认关闭。需要配置的扩展会在配置完整后才输出前台 DOM 和外部脚本，避免空播放器或无效入口。': 'Extensions are disabled by default. Extensions that need settings output front-end DOM and external scripts only after the configuration is complete, avoiding empty players or inactive controls.',
  '例如 320px。超过高度后列表内部滚动。': 'For example, 320px. The playlist scrolls internally after exceeding this height.',
  '管理主题内置的导航、侧栏作者卡、标签云、首页胶囊和播放器位置。需要使用 WordPress 小工具时，请关闭“使用主题内置侧栏”。': 'Manages the theme built-in navigation, sidebar author card, tag cloud, home capsules, and player position. Disable "Use theme built-in sidebar" when you want to use WordPress widgets.',
  '默认使用主题自动生成的 /search.json；填写自定义本地 JSON URL 后会覆盖默认地址。搜索优先级：本地 JSON、Algolia、WordPress REST。': 'By default, the theme-generated /search.json is used. A custom local JSON URL overrides it. Search priority: local JSON, Algolia, WordPress REST.',
  '搜索优先级：Algolia 配置完整时优先使用 Algolia；否则使用本地 JSON；再否则回退 WordPress REST。': 'Search priority: use Algolia first when fully configured; otherwise use local JSON; otherwise fall back to WordPress REST.',
  '添加本站后，在本页留言，格式如下': 'After adding this site, leave a comment on this page in the following format.',
  '推荐在“外观 -> Yneko-Reimu 设置”中管理音乐。这里保留为旧配置兼容入口。': 'Managing music in Appearance -> Yneko-Reimu Settings is recommended. This remains as a legacy compatibility entry.',
  '推荐在“外观 -> Yneko-Reimu 设置”中管理友链。这里保留为旧配置兼容入口。': 'Managing friend links in Appearance -> Yneko-Reimu Settings is recommended. This remains as a legacy compatibility entry.',
  '移动端导航': 'Mobile navigation',
  '用于 Reimu 扩展包的 CDN 前缀，默认使用 jsDelivr；如果你的地区访问不稳定，可以换成自己的 npm CDN。': 'CDN prefix for Reimu extension packages. The default uses jsDelivr; if access is unstable in your region, switch it to your preferred npm CDN.',
  '邮箱': 'Email',
  '友链待添加': 'Friend links pending',
  '右上角 GitHub 三角标': 'Top-right GitHub corner ribbon',
  '预加载': 'Preload',
  '预览': 'Preview',
  '预览:': 'Preview:',
  '暂时没有内容': 'No content yet',
  '摘要字数': 'Excerpt length',
  '站点页脚': 'Site footer',
  '这里记录项目、学习笔记和日常灵感。': 'Projects, study notes, and everyday inspirations are recorded here.',
  '这篇文章最后更新于 %s，部分内容可能已经变化，请结合最新资料判断。': 'This post was last updated on %s. Some content may have changed; please check against current information.',
  '支持 APlayer 音频 JSON 或 Meting 歌单；未启用时不加载外部资源。': 'Supports APlayer audio JSON or Meting playlists; no external resources are loaded when disabled.',
  '支持 Yneko-Reimu 设置里的媒体库曲目或 Meting 歌单；没有曲目或 Meting 配置不完整时不输出空播放器。': 'Supports Media Library tracks from Yneko-Reimu Settings or Meting playlists. No empty player is output when tracks are missing or Meting is incomplete.',
  '只有在没有同 slug 的真实 WordPress 页面时，主题才会输出这些虚拟页面内容。': 'These virtual pages are output only when no real WordPress page with the same slug exists.',
  '置顶': 'Sticky',
  '主题会始终保留 WordPress 与 hexo-theme-reimu/MIT 署名。': 'The theme always keeps WordPress and hexo-theme-reimu/MIT credits.',
  '主要内容': 'Main content',
  '自定义链接': 'Custom link',
  '自定义鼠标指针': 'Custom cursor',
  '开启后在首屏加载与 PJAX 切换时显示加载层。': 'Shows the loading overlay on first-page load and during PJAX navigation.',
  '开启后侧栏显示回到顶部按钮。': 'Shows the back-to-top button in the sidebar.',
  '需要在 Yneko-Reimu 设置中填写 GitHub 主页链接。': 'Requires the GitHub profile URL in Yneko-Reimu Settings.',
  '开启后桌面端点击页面会显示鼠标烟花效果。': 'Shows a mouse firework effect when clicking on desktop.',
  '开启后站内页面切换不整页刷新，并保留播放器状态。': 'Enables same-site navigation without full reloads and preserves player state.',
  '开启后页脚与文章阅读量输出 Busuanzi 统计节点，并加载不蒜子脚本；关闭时使用本地计数。': 'Outputs Busuanzi statistic nodes in the footer and post view count, and loads the Busuanzi script. Local counts are used when disabled.',
  '需要先在“外观 -> Yneko-Reimu 设置”的音乐列表中添加曲目。没有曲目时不输出播放器。': 'Add tracks in Appearance -> Yneko-Reimu Settings first. No player is output when no tracks exist.',
  '需要填写 Meting auto URL，或同时填写 ID、server、type；配置为空时不输出播放器。': 'Requires a Meting auto URL, or ID, server, and type together. No player is output when empty.',
  '开启后加载 Live2D Widgets CDN 资源，并固定显示在右下角。': 'Loads Live2D Widgets CDN resources and fixes the widget at the bottom right.',
  '开启后渲染文章正文中的 $...$、$$...$$、\\(...\\)、\\[...\\] 数学公式。': 'Renders math in post content using $...$, $$...$$, \\(...\\), and \\[...\\].',
  '开启后文章正文图片会自动获得可点击灯箱效果。': 'Adds clickable lightbox behavior to images in post content.',
  '开启后渲染 class 为 language-mermaid 或 mermaid 的代码块。': 'Renders code blocks with the language-mermaid or mermaid class.',
  '需要完整填写 App ID、Search API Key 和 Index Name；配置不完整时自动回退本地搜索。': 'Requires App ID, Search API Key, and Index Name. Falls back to local search when incomplete.',
  '开启后使用主题生成的 search.json 或自定义本地 JSON 搜索。': 'Uses the theme-generated search.json or a custom local JSON search index.',
  '开启 Algolia 搜索时必填。': 'Required when Algolia search is enabled.',
  '填写 Algolia Search-Only API Key，不要填写 Admin API Key。': 'Enter an Algolia Search-Only API Key, not an Admin API Key.',
  '留空时使用主题自动生成的 /search.json。': 'Leave empty to use the theme-generated /search.json.',
  '歌单、专辑、歌曲或用户 ID。使用 auto URL 时可留空。': 'Playlist, album, song, or user ID. Leave empty when using an auto URL.',
  '例如 netease、tencent、kugou、xiami、baidu。使用 auto URL 时可留空。': 'For example: netease, tencent, kugou, xiami, baidu. Leave empty when using an auto URL.',
  '例如 song、playlist、album、search、artist。使用 auto URL 时可留空。': 'For example: song, playlist, album, search, artist. Leave empty when using an auto URL.',
  '填入音乐平台链接后，Meting 会自动识别来源；填写此项即可不填 ID/server/type。': 'Enter a music-platform URL and Meting will detect the source automatically. With this field, ID/server/type can be left empty.',
  'Live2D Widgets 资源地址': 'Live2D Widgets resource URL',
  '用于加载 waifu.css、live2d.min.js、waifu-tips.js 和 waifu-tips.json。默认使用 stevenjoezhang/live2d-widget CDN。': 'Used to load waifu.css, live2d.min.js, waifu-tips.js, and waifu-tips.json. The default uses the stevenjoezhang/live2d-widget CDN.',
  'Live2D 模型 CDN 地址': 'Live2D model CDN URL',
  '用于读取默认模型列表和模型文件。主题不内置模型资源。': 'Used to read the default model list and model files. The theme does not bundle model assets.',
  '自动播放': 'Autoplay',
  'KaTeX 数学公式': 'KaTeX math',
  'Mermaid 图表': 'Mermaid diagrams',
  'PhotoSwipe 图片灯箱': 'PhotoSwipe lightbox',
  'PJAX 软导航': 'PJAX navigation',
  'Reimu 播放器': 'Reimu player',
  '预设': 'Presets',
  'Reimu 扩展功能': 'Reimu extensions',
  'Reimu 评论系统': 'Reimu comments',
  'Reimu 设置': 'Reimu Settings',
  'Reimu 搜索': 'Reimu Search',
  'WordPress 评论始终可用；第三方评论未启用或未填配置时不会加载。': 'WordPress comments are always available. Third-party comment systems load only when enabled and configured.',
  'Yneko-Reimu 主题设置': 'Yneko-Reimu Theme Settings',
  'Yneko-Reimu GitHub Login': 'Yneko-Reimu GitHub Login',
  'GitHub 登录成功': 'GitHub login successful',
  'GitHub 登录成功，正在返回评论区...': 'GitHub login successful. Returning to the comments...',
  'GitHub login is not configured.': 'GitHub login is not configured.',
  'Missing GitHub OAuth response.': 'Missing GitHub OAuth response.',
  'GitHub login state expired. Please try again.': 'GitHub login state expired. Please try again.',
  'GitHub did not return an access token.': 'GitHub did not return an access token.',
  'GitHub API request failed.': 'GitHub API request failed.',
  'GitHub profile is missing required fields.': 'GitHub profile is missing required fields.',
  'This GitHub account is already linked to another WordPress account.': 'This GitHub account is already linked to another WordPress account.',
  'No WordPress account is linked to this GitHub account.': 'No WordPress account is linked to this GitHub account.',
  'This GitHub email already belongs to an existing WordPress account. Please log in normally first, then bind GitHub.': 'This GitHub email already belongs to an existing WordPress account. Please log in normally first, then bind GitHub.',
}));

async function listFiles(dir) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    if (entry.name === 'node_modules' || entry.name === 'assets') {
      continue;
    }
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await listFiles(full));
    } else if (entry.isFile() && full.endsWith('.php')) {
      files.push(full);
    }
  }
  return files;
}

function decodePhpString(raw) {
  return raw
    .replace(/\\'/g, "'")
    .replace(/\\"/g, '"')
    .replace(/\\\\/g, '\\')
    .replace(/\\n/g, '\n')
    .replace(/\\r/g, '\r')
    .replace(/\\t/g, '\t');
}

function extractStrings(source, file) {
  const strings = new Map();
  const callPattern = /(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\s*\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*(['"])yneko-reimu\3/gs;
  const pluralPattern = /_n\s*\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*(['"])((?:\\.|(?!\3).)*)\3\s*,\s*[^,]+,\s*(['"])yneko-reimu\5/gs;
  for (const match of source.matchAll(callPattern)) {
    const msgid = decodePhpString(match[2]);
    if (!strings.has(msgid)) {
      strings.set(msgid, []);
    }
    strings.get(msgid).push(file);
  }
  for (const match of source.matchAll(pluralPattern)) {
    const msgid = decodePhpString(match[2]);
    if (!strings.has(msgid)) {
      strings.set(msgid, []);
    }
    strings.get(msgid).push(file);
  }
  return strings;
}

function poEscape(value) {
  return String(value)
    .replace(/\\/g, '\\\\')
    .replace(/"/g, '\\"')
    .replace(/\n/g, '\\n');
}

function formatPoString(keyword, value) {
  return `${keyword} "${poEscape(value)}"`;
}

function potHeader() {
  return [
    'msgid ""',
    'msgstr ""',
    '"Project-Id-Version: Yneko-Reimu 0.1.3\\n"',
    '"Report-Msgid-Bugs-To: https://github.com/EkaEva/Yneko-Reimu/issues\\n"',
    '"POT-Creation-Date: 2026-05-30 00:00+0000\\n"',
    '"MIME-Version: 1.0\\n"',
    '"Content-Type: text/plain; charset=UTF-8\\n"',
    '"Content-Transfer-Encoding: 8bit\\n"',
    '"X-Domain: yneko-reimu\\n"',
    '',
  ].join('\n');
}

function poHeader(locale) {
  const lang = locale === 'en_US' ? 'en_US' : 'zh_CN';
  return [
    'msgid ""',
    'msgstr ""',
    '"Project-Id-Version: Yneko-Reimu 0.1.3\\n"',
    '"Report-Msgid-Bugs-To: https://github.com/EkaEva/Yneko-Reimu/issues\\n"',
    '"POT-Creation-Date: 2026-05-30 00:00+0000\\n"',
    '"PO-Revision-Date: 2026-05-30 00:00+0000\\n"',
    '"Last-Translator: Yneko-Reimu Contributors\\n"',
    '"Language-Team: Yneko-Reimu Contributors\\n"',
    `"Language: ${lang}\\n"`,
    '"MIME-Version: 1.0\\n"',
    '"Content-Type: text/plain; charset=UTF-8\\n"',
    '"Content-Transfer-Encoding: 8bit\\n"',
    '"X-Generator: tools/build-i18n.mjs\\n"',
    `"X-Domain: ${textDomain}\\n"`,
    '',
  ].join('\n');
}

function buildPo(entries, locale) {
  const lines = [poHeader(locale)];
  for (const [msgid, refs] of entries) {
    lines.push(`#: ${Array.from(new Set(refs)).slice(0, 5).join(' ')}`);
    lines.push(formatPoString('msgid', msgid));
    const msgstr = locale === 'zh_CN' ? msgid : (enTranslations.get(msgid) || '');
    lines.push(formatPoString('msgstr', msgstr));
    lines.push('');
  }
  return lines.join('\n');
}

function buildPot(entries) {
  const lines = [potHeader()];
  for (const [msgid, refs] of entries) {
    lines.push(`#: ${Array.from(new Set(refs)).slice(0, 5).join(' ')}`);
    lines.push(formatPoString('msgid', msgid));
    lines.push(formatPoString('msgstr', ''));
    lines.push('');
  }
  return lines.join('\n');
}

function parsePo(po) {
  const messages = [];
  const blocks = po.split(/\n\s*\n/);
  for (const block of blocks) {
    const idMatch = block.match(/^msgid\s+"((?:\\.|[^"])*)"/m);
    const strMatch = block.match(/^msgstr\s+"((?:\\.|[^"])*)"/m);
    if (!idMatch || !strMatch) {
      continue;
    }
    const msgid = idMatch[1]
      .replace(/\\n/g, '\n')
      .replace(/\\"/g, '"')
      .replace(/\\\\/g, '\\');
    const msgstr = strMatch[1]
      .replace(/\\n/g, '\n')
      .replace(/\\"/g, '"')
      .replace(/\\\\/g, '\\');
    messages.push([msgid, msgstr]);
  }
  return messages;
}

function writeUInt32(buffer, value, offset) {
  buffer.writeUInt32LE(value >>> 0, offset);
}

function compileMo(messages) {
  const sorted = messages.slice().sort((a, b) => Buffer.compare(Buffer.from(a[0]), Buffer.from(b[0])));
  const count = sorted.length;
  const headerSize = 28;
  const originalsOffset = headerSize;
  const translationsOffset = originalsOffset + count * 8;
  let stringsOffset = translationsOffset + count * 8;
  const originalBuffers = sorted.map(([id]) => Buffer.from(id, 'utf8'));
  const translationBuffers = sorted.map(([, str]) => Buffer.from(str, 'utf8'));
  const totalStringBytes = originalBuffers.reduce((sum, b) => sum + b.length + 1, 0) + translationBuffers.reduce((sum, b) => sum + b.length + 1, 0);
  const buffer = Buffer.alloc(stringsOffset + totalStringBytes);

  writeUInt32(buffer, 0x950412de, 0);
  writeUInt32(buffer, 0, 4);
  writeUInt32(buffer, count, 8);
  writeUInt32(buffer, originalsOffset, 12);
  writeUInt32(buffer, translationsOffset, 16);
  writeUInt32(buffer, 0, 20);
  writeUInt32(buffer, 0, 24);

  for (let i = 0; i < count; i += 1) {
    const value = originalBuffers[i];
    writeUInt32(buffer, value.length, originalsOffset + i * 8);
    writeUInt32(buffer, stringsOffset, originalsOffset + i * 8 + 4);
    value.copy(buffer, stringsOffset);
    stringsOffset += value.length + 1;
  }

  for (let i = 0; i < count; i += 1) {
    const value = translationBuffers[i];
    writeUInt32(buffer, value.length, translationsOffset + i * 8);
    writeUInt32(buffer, stringsOffset, translationsOffset + i * 8 + 4);
    value.copy(buffer, stringsOffset);
    stringsOffset += value.length + 1;
  }

  return buffer;
}

async function main() {
  const files = await listFiles(themeDir);
  const all = new Map();
  for (const file of files) {
    const source = await fs.readFile(file, 'utf8');
    const relative = path.relative(themeDir, file).replace(/\\/g, '/');
    const extracted = extractStrings(source, relative);
    for (const [msgid, refs] of extracted) {
      if (!all.has(msgid)) {
        all.set(msgid, []);
      }
      all.get(msgid).push(...refs);
    }
  }

  const entries = Array.from(all.entries()).sort((a, b) => a[0].localeCompare(b[0], 'zh-Hans-CN'));
  await fs.mkdir(languagesDir, { recursive: true });
  await fs.writeFile(path.join(languagesDir, `${textDomain}.pot`), buildPot(entries), 'utf8');

  for (const locale of ['zh_CN', 'en_US']) {
    const po = buildPo(entries, locale);
    const poPath = path.join(languagesDir, `${locale}.po`);
    const moPath = path.join(languagesDir, `${locale}.mo`);
    await fs.writeFile(poPath, po, 'utf8');
    await fs.writeFile(moPath, compileMo(parsePo(po)));
  }

  console.log(`Generated ${entries.length} gettext strings in ${path.relative(repoDir, languagesDir)}`);
}

await main();
