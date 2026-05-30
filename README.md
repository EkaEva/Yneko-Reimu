<p align="center">
  <img src="theme/Yneko-Reimu/assets/images/avatar.svg" alt="Yneko-Reimu" width="120" height="120">
</p>

<h1 align="center">Yneko-Reimu</h1>

<p align="center">
  一个面向 WordPress 的 Reimu 风格经典主题。
</p>

![Yneko-Reimu 主题演示封面](theme/Yneko-Reimu/screenshot.png)

Yneko-Reimu 是一个面向 WordPress 的经典主题，目标是在 WordPress 内容系统中复刻并延展 [D-Sketon/hexo-theme-reimu](https://github.com/D-Sketon/hexo-theme-reimu) 的视觉与交互体验。

本项目不是 hexo-theme-reimu 的官方 WordPress 版本，而是一个学习、复刻与二次开发项目。主题保留了 Reimu 风格的顶部导航、头图、文章卡片、侧栏作者卡、归档、友链、项目页、搜索弹窗、加载动画、暗色模式、音乐播放器、评论视觉、代码块样式和自定义鼠标指针等体验，并将它们适配到 WordPress 模板、Customizer、媒体库和后台设置中。

## 项目来源

Yneko-Reimu 的整体设计语言、页面结构、交互动效与部分前端样式参考并改编自 [hexo-theme-reimu](https://github.com/D-Sketon/hexo-theme-reimu)。

- 原主题作者：D-Sketon
- 原主题仓库：[https://github.com/D-Sketon/hexo-theme-reimu](https://github.com/D-Sketon/hexo-theme-reimu)
- 原主题许可证：MIT License
- 原主题官网 / 演示站：[https://d-sketon.github.io/](https://d-sketon.github.io/)

Yneko-Reimu 在此基础上完成了 WordPress 主题化，包括 PHP 模板、WordPress 查询、评论系统、后台设置、媒体库配置、项目页 GitHub 拉取、PJAX/软导航适配、本地搜索索引、主题打包与发布清理等工作。

## 鼠标指针来源

主题当前内置的莉莉概念鼠标指针素材来源于 B 站作者「天羊EdSky」的相关设计，主题中将其整理为静态 PNG 光标状态并接入 WordPress 前台。

- 鼠标指针作者：天羊EdSky
- 作者主页：[https://space.bilibili.com/16573583](https://space.bilibili.com/16573583)
- 主题内用途：前台自定义 cursor，包括默认、链接、文本、加载、不可用、帮助、移动、拖拽与 resize 等状态

如果你要二次发布、商用或替换这些光标素材，请先确认素材原作者允许的使用范围。更完整的版权说明见 [NOTICE.md](NOTICE.md)。

## 功能概览

- Reimu 风格首页：顶部导航、头图、文章卡片、置顶文章、分类胶囊。
- Reimu 风格侧栏：作者头像、站点统计、社交入口、菜单按钮、标签云。
- 归档 / 关于 / 友链 / 项目虚拟页面：未创建真实页面时自动提供主题页面。
- GitHub 项目页：根据后台配置的 GitHub 主页拉取用户项目和 Star 项目。
- 本地 JSON 搜索：支持生成 WordPress 本地搜索索引。
- PJAX / 软导航：减少站内切换时的整页刷新断档。
- 音乐播放器：基于 APlayer，曲目、歌词和封面从 WordPress 媒体库配置。
- WordPress 原生评论视觉增强：保留原生评论提交、审核、回复、分页能力。
- GitHub OAuth 登录：主题内置登录模块，可在后台配置 Client ID / Secret。
- 自定义鼠标指针：桌面端使用莉莉概念光标 PNG，移动端自动回退。
- 代码块编辑器样式：三色圆点、文件类型标识、行号、复制、折叠。
- 暗色模式、阅读量、本地访客统计、回到顶部、加载动画、鼠标点击特效。
- 404 专属页：满屏背景、无底栏、不可滚动。

## 运行环境

- WordPress：建议 6.0 及以上
- PHP：8.0 及以上
- 浏览器：现代 Chromium / Firefox / Safari
- 主题类型：Classic Theme + PHP 模板 + theme.json
- 构建环境：Node.js，仅开发和打包时需要；线上使用 ZIP 不需要 Node

## 安装主题

### 方式一：后台上传 ZIP

1. 下载 Release 附件或本地打包得到 `releases/Yneko-Reimu.zip`。
2. 进入 WordPress 后台。
3. 打开 `外观 -> 主题 -> 添加新主题 -> 上传主题`。
4. 上传 `releases/Yneko-Reimu.zip`。
5. 安装并启用主题。

### 方式二：手动放入主题目录

1. 将仓库中的主题源码目录复制到 WordPress 的主题目录：

```text
theme/Yneko-Reimu -> wp-content/themes/Yneko-Reimu
```

2. 进入后台 `外观 -> 主题`。
3. 启用 `Yneko-Reimu`。

## 首次配置

主题启用后，建议先完成两个后台入口的配置。

### 1. 外观 -> Yneko-Reimu 设置

这里保存的是站点数据型配置，内容会进入 WordPress 数据库，不会写入主题源码。

#### 站点资料

- 站点头像：用于站点图标、默认 logo、分享图标兜底等站点级图片。
- 作者头像：用于前台侧栏作者卡、页面角色图、友链和项目缺省图。
- 游客评论头像：用于未登录用户评论时显示的默认头像。
- GitHub 主页链接：统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。
- 赞助二维码：留空则不显示赞助二维码；配置后可在页面底部或短代码中显示。

#### GitHub 登录

主题内置 GitHub OAuth 登录，不需要额外安装独立插件。

1. 在 GitHub 创建 OAuth App。
2. 在 WordPress 后台复制主题显示的 Callback URL。
3. 将 Callback URL 填入 GitHub OAuth App 的 `Authorization callback URL`。
4. 回到 `外观 -> Yneko-Reimu 设置`，填写：
   - Client ID
   - Client Secret
   - Callback URL 覆盖项，可留空使用默认地址
   - 是否允许自动创建用户
5. 保存后，评论登录弹窗中会出现 GitHub 登录入口。

注意：Client Secret 只保存在 WordPress 数据库中，不应写入主题源码或提交到 GitHub。

#### 友链列表

友链支持新增、编辑和删除，每条包含：

- 名称
- 链接
- 描述
- 头像

主题默认提供三条来源相关示例友链：主题作者、hexo-theme-reimu 原作者、鼠标指针作者。用户可以自行删除或修改。

#### 音乐列表

音乐播放器默认没有曲目。请先将音频、歌词和封面上传到 WordPress 媒体库，再在设置页新增曲目。

每首曲目包含：

- 歌名
- 作者
- 音频 URL
- 封面 URL
- LRC 歌词 URL
- 主题色

未配置音乐时，前台不会加载播放器。

### 2. 外观 -> 自定义 -> Yneko-Reimu 主题设置

这里保存的是主题视觉和布局配置。

常用配置包括：

- Reimu 复刻预设
- 顶部导航文字和链接
- 语言入口
- 首页分类胶囊标题、链接和封面
- 播放器位置
- 默认 Banner 图片
- 默认卡片封面
- 默认头像
- 搜索弹窗背景图
- 侧栏位置
- 暗色模式
- 自定义鼠标指针
- PJAX
- 本地搜索
- 评论集成开关
- 页脚信息
- 鼠标点击特效

## 推荐页面

主题内置几个 Reimu 风格虚拟页面。如果站点中不存在对应 slug 的真实页面，主题会自动显示虚拟页面。

| 路径 | 用途 |
| --- | --- |
| `/about/` | 关于页 |
| `/archives/` | 归档页 |
| `/friend/` | 友链页 |
| `/projects/` | GitHub 项目页 |

如果你创建了同名 WordPress 页面，主题会优先显示真实页面正文，并保留主题页面样式。

## 本地搜索配置

主题提供本地搜索索引接口，默认地址为：

```text
/search.json
```

启用后，搜索弹窗会优先使用本地 JSON 搜索文章标题、摘要和正文。你也可以在 Customizer 中配置其它搜索 JSON 地址。

## 评论说明

评论功能默认使用 WordPress 原生评论系统。主题只是重写前台视觉，不替换数据库，也不强依赖第三方评论服务。

保留能力：

- 游客昵称 / 邮箱 / 网址
- 登录用户评论
- 评论审核
- 嵌套回复
- 评论分页
- 加载更多
- GitHub 登录入口，可选

主题不会伪装成 Waline，只是参考 Reimu 演示站中 Waline 评论组件的视觉形式做 WordPress 原生等效实现。

## 媒体与个人数据

为了方便发布到 GitHub，主题源码不应包含你的个人内容和敏感信息。

不建议提交到仓库的内容：

- GitHub OAuth Client Secret
- 数据库 SQL
- `.wpress` 备份
- 个人文章正文
- 个人音乐文件
- 歌词文件
- 赞助二维码
- 本地 WordPress 上传目录
- 本地备份目录

这些内容应该保存在 WordPress 数据库和媒体库中，通过后台配置引用。

## 开发与构建

安装依赖后可运行：

```bash
npm run check:js
npm run build
npm run package
```

脚本说明：

- `npm run check:js`：检查前端 JS 语法。
- `npm run build`：复制前端脚本、合并 CSS，并生成光标 PNG。
- `npm run package`：按白名单生成 `releases/Yneko-Reimu.zip`。

如果需要生成带版本号的发布包，可以直接调用打包脚本：

```bash
pwsh tools/package-theme.ps1 -Version v0.1.0
```

生成结果：

```text
releases/Yneko-Reimu-v0.1.0.zip
```

构建产物位于：

```text
theme/Yneko-Reimu/assets/dist/
```

主要源码位置：

```text
theme/Yneko-Reimu/assets/src/reimu.js
theme/Yneko-Reimu/assets/src/reimu-upstream.css
theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css
theme/Yneko-Reimu/inc/
theme/Yneko-Reimu/template-parts/
```

打包脚本会从 `theme/Yneko-Reimu/` 按白名单复制主题运行文件，并排除开发源文件、上游源码镜像、构建工具、本地媒体和不应发布的个人内容。上传 WordPress 的是 `releases/Yneko-Reimu.zip`，不是 GitHub 仓库根目录的 ZIP。

## GitHub Actions 自动打包

仓库内置了 `.github/workflows/release-package.yml`。当你向 GitHub 推送版本 tag 时会自动触发构建，例如：

```bash
git tag v0.1.0
git push origin v0.1.0
```

Action 会执行：

```bash
npm run check:js
npm run build
pwsh tools/package-theme.ps1 -Version v0.1.0
```

随后生成并上传：

```text
Yneko-Reimu-v0.1.0.zip
```

如果同名 GitHub Release 不存在，Action 会根据 tag 创建 Release；如果 Release 已存在，则会把 ZIP 上传到该 Release。也可以在 GitHub Actions 页面手动运行该 workflow，输入版本号后生成同名 artifact。

推荐 tag 命名使用 `vX.Y.Z`，例如 `v0.1.0`、`v0.1.1`。如果手动输入 `0.1.0`，打包脚本会自动补成 `v0.1.0`。

## 目录结构

```text
Yneko-Reimu/
├─ theme/
│  └─ Yneko-Reimu/
│     ├─ assets/
│     │  ├─ dist/           # 前台构建产物，进入发布 ZIP
│     │  ├─ images/         # 主题必要图片和光标
│     │  └─ src/            # 开发用前端源码，不进入发布 ZIP
│     ├─ inc/               # PHP 功能模块
│     ├─ template-parts/    # 模板片段
│     ├─ 404.php
│     ├─ index.php
│     ├─ single.php
│     ├─ page.php
│     ├─ style.css
│     └─ theme.json
├─ tools/                   # 仓库级构建和打包脚本
├─ releases/                # 本地打包输出，默认不提交
├─ package.json             # 仓库根统一 npm 入口
├─ LICENSE
├─ NOTICE.md
└─ README.md
```

## 发布前检查

发布到 GitHub 前建议检查：

```bash
npm run check:js
npm run build
npm run package
```

同时确认仓库或 ZIP 中不包含：

- `wp-local/`
- `backups/`
- 数据库文件
- OAuth Secret
- 个人音乐
- 赞助二维码
- 未授权素材

## License

Yneko-Reimu 使用 MIT License 发布，详见 [LICENSE](LICENSE)。

本主题包含对 [hexo-theme-reimu](https://github.com/D-Sketon/hexo-theme-reimu) 的参考、移植和改编。原主题由 D-Sketon 创作并以 MIT License 发布。

主题中包含的莉莉概念鼠标指针素材归原作者「天羊EdSky」所有。该素材的具体使用边界请以原作者发布说明为准。详细来源和版权声明见 [NOTICE.md](NOTICE.md)。

## 致谢

- [D-Sketon](https://github.com/D-Sketon)：感谢原作者创作 hexo-theme-reimu，并以开源方式分享如此完整而有辨识度的主题。
- [hexo-theme-reimu](https://github.com/D-Sketon/hexo-theme-reimu)：Yneko-Reimu 的主要设计与交互来源。
- [天羊EdSky](https://space.bilibili.com/16573583)：感谢莉莉概念鼠标指针素材的创作。
