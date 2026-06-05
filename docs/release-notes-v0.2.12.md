# Yneko-Reimu v0.2.12

## 中文

v0.2.12 是一次 Customizer 外观自定义版本。它把第一批高自定义能力放进 `Yneko-Reimu 视觉预览`：视觉资产、排版与布局密度，以及分组恢复默认。同时，本版本正式收录此前已合入的根路径 favicon 兼容修复。

### 主要更新

- 增加根路径 favicon 兼容输出：在保留 SVG Site Icon 和 PNG/JPG fallback 的同时，为 `/favicon.ico`、`/favicon-32x32.png`、`/favicon-192x192.png`、`/apple-touch-icon.png` 等路径返回真实图标响应，方便 Bing、Edge 和旧客户端识别站点图标。
- 新增 Customizer “视觉资产”设置，可替换默认、链接/按钮、文本输入、加载/忙碌四类鼠标指针。
- 新增加载动画图片、中文文案、英文文案、图片尺寸和旋转控制。旧的 `yneko_reimu_preloader_text` 仍作为中文文案 fallback。
- 新增回到顶部图标和赞助按钮图标自定义，默认留空时继续使用主题内置太极图。
- 新增 “排版与布局密度” 设置，可调整正文/标题/代码字体、基础字号、文章字号、文章行高、页面最大宽度、文章阅读宽度、三档布局密度、卡片/图片圆角和阴影强度。
- 新增 Customizer “恢复默认” section，按视觉资产、排版与布局密度、预览图片、内容显示四组恢复低风险外观项。
- 恢复默认操作会先弹确认，再立即更新右侧预览；只有点击 Customizer 的“发布”后才会保存。发布后主题会清理对应 `theme_mod`，让后续默认值调整可以自然生效。
- 更新 Customizer、配置面、enqueue、包边界和发布文档的 contract gates，保护新的设置和构建产物。

### 说明

- 默认安装的视觉表现保持不变。新的图片、字体、密度和恢复默认设置都是新增的 Customizer `theme_mod`。
- `Yneko-Reimu 设置` 仍负责功能开关和高风险配置；本次没有改评论、登录、上传审核、OAuth、个人资料保存、第三方服务或功能开关流程。
- 公开设置 key、hook、URL、模板路径、脚本/样式 handle、`window.REIMU_CONFIG` 结构、classic script 加载方式和 release ZIP 边界保持兼容。
- 本版本发布前完成了本地静态验证、PHPCS、npm audit、完整 PHP 语法检查、构建、运行时烟测、打包和 ZIP 边界校验。

## English

v0.2.12 is a Customizer appearance release. It adds the first high-customization surfaces to `Yneko-Reimu Visual Preview`: visual assets, typography/layout density, and grouped restore-default controls. It also formally includes the previously merged root favicon compatibility fix.

### Highlights

- Added root favicon compatibility responses while keeping the existing SVG Site Icon and PNG/JPG fallback behavior. `/favicon.ico`, `/favicon-32x32.png`, `/favicon-192x192.png`, `/apple-touch-icon.png`, and related paths now return real icon/image responses for Bing, Edge, and legacy clients.
- Added Customizer visual asset controls for the default, pointer/link, text-input, and progress/busy cursor slots.
- Added loader controls for the center image, Chinese text, English text, image size, and rotation. The legacy `yneko_reimu_preloader_text` value remains a Chinese loader-text fallback.
- Added Customizer controls for the back-to-top icon and sponsor button icon, with bundled Taichi artwork as the empty-field fallback.
- Added typography and layout-density controls for body/heading/code fonts, base font size, article font size, article line height, content width, article reading width, three density modes, card/image radius, and shadow strength.
- Added a Customizer restore-defaults section with grouped reset buttons for visual assets, typography/layout density, preview images, and content display.
- Restore actions confirm first, update the live preview immediately, and only persist after the Customizer Publish action. On publish, affected `theme_mod` values are removed so future code defaults can apply naturally.
- Updated Customizer, config-surface, enqueue, package-boundary, and release documentation contract gates for the new settings and build output.

### Notes

- Fresh installs keep the same default appearance. The new image, typography, density, and restore-default settings are additive Customizer `theme_mod` values.
- `Yneko-Reimu Settings` continues to own feature toggles and high-risk configuration. This release does not change comment, login, upload review, OAuth, profile-save, third-party service, or feature-toggle flows.
- Public setting keys, hooks, URLs, template paths, script/style handles, the `window.REIMU_CONFIG` shape, classic script loading, and the release ZIP boundary remain compatible.
- This release passed local static validation, PHPCS, npm audit, full PHP syntax linting, build, runtime smoke tests, packaging, and ZIP boundary validation before release.
