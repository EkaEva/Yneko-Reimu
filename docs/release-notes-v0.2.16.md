# Yneko-Reimu v0.2.16

## 中文

v0.2.16 聚焦后台区块编辑器的技术/说明型文章写作体验。它把线上文章里常用的设置说明表格、代码窗口、TIP/INFO/WARNING 提示块和技术笔记段落结构整理成原生 block patterns 与 block style variations，让后台可以一键插入并获得更接近前台的主题化预览。

### 主要更新

- 新增 Yneko-Reimu 区块样板：`yneko-reimu/settings-table`、`yneko-reimu/code-window`、`yneko-reimu/tip-notice`、`yneko-reimu/info-notice`、`yneko-reimu/warning-notice`、`yneko-reimu/technical-note`。
- 保留既有区块样板 `yneko-reimu/article-intro` 和 `yneko-reimu/two-column-note`，继续兼容已经写入文章或编辑器收藏中的样板入口。
- 新增原生区块样式：`is-style-reimu-field-table`、`is-style-reimu-code-window`、`is-style-reimu-notice-tip`、`is-style-reimu-notice-info`、`is-style-reimu-notice-warning`。
- 新增后台区块编辑器专用样式 `assets/dist/reimu-editor.css`，仅在 block editor 加载，不引入前台动画、PJAX、评论、搜索或媒体运行时。
- 补充前台 `wp-block-table` 兼容样式，使后台插入的 Reimu 设置说明表格在前台继续获得移动端横向滚动、字段列宽和内联代码 pill 样式。
- 新增 `check:editor` contract gate，保护新增 pattern slug、block style class、`yneko-reimu-editor` 后台样式 handle、构建输出和前台表格样式边界。
- 更新版本元数据、运行时 readme、README 发布示例、发布流程文档、翻译文件和 package boundary 检查到 v0.2.16。

### 说明

- 本版本不新增 React 自定义区块构建链，不改变 Classic Hybrid 前台脚本加载方式。
- 不修改现有设置 key、AJAX action、nonce、post meta key、模板路径、URL、前台 script/style handle、`window.REIMU_CONFIG` 或 `window.ReimuWP`。
- 新增公开可见的 pattern slug 和 block style class 从 v0.2.16 起视为编辑器接口，后续版本应保持兼容或提供迁移说明。
- Mermaid 继续通过现有代码块写作方式与功能开关处理，本版本不新增独立 Mermaid 自定义区块。
- 发布前已完成自动检查、包边界校验和 WordPress 后台插入/保存/前台预览手动 QA。

## English

v0.2.16 improves the block-editor writing flow for technical and documentation-style posts. It turns common settings tables, code windows, TIP/INFO/WARNING notices, and technical-note sections into native WordPress block patterns and block style variations, giving authors one-click insertion and closer back-end previews.

### Highlights

- Added Yneko-Reimu block patterns: `yneko-reimu/settings-table`, `yneko-reimu/code-window`, `yneko-reimu/tip-notice`, `yneko-reimu/info-notice`, `yneko-reimu/warning-notice`, and `yneko-reimu/technical-note`.
- Preserved the existing `yneko-reimu/article-intro` and `yneko-reimu/two-column-note` pattern slugs for editor compatibility.
- Added native block styles: `is-style-reimu-field-table`, `is-style-reimu-code-window`, `is-style-reimu-notice-tip`, `is-style-reimu-notice-info`, and `is-style-reimu-notice-warning`.
- Added the editor-only `assets/dist/reimu-editor.css` stylesheet, loaded only in the block editor and without front-end animation, PJAX, comments, search, or media runtimes.
- Added front-end `wp-block-table` compatibility selectors so editor-created Reimu settings tables keep horizontal scrolling, field-table layout, and inline code pill styling.
- Added the `check:editor` contract gate to protect the new pattern slugs, block style classes, editor stylesheet handle, build output, and front-end table style boundary.
- Updated version metadata, runtime readme, README release examples, release docs, translation files, and package-boundary checks for v0.2.16.

### Notes

- This release does not introduce a React custom-block build chain and does not change Classic Hybrid front-end script loading.
- Existing setting keys, AJAX actions, nonces, post meta keys, template paths, URLs, front-end script/style handles, `window.REIMU_CONFIG`, and `window.ReimuWP` remain unchanged.
- The new public pattern slugs and block style classes are editor interfaces from v0.2.16 onward and should remain compatible unless a future migration note is added.
- Mermaid remains handled through the existing code-block writing flow and feature toggle; this release does not add a dedicated Mermaid custom block.
- Automated checks, package-boundary validation, and WordPress manual QA for inserting, saving, and previewing the new writing components were completed before release.
