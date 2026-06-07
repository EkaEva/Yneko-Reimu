# Yneko-Reimu v0.2.18

## 中文

v0.2.18 是一个主题更新检测可观测性修复版本。它不改变 GitHub Release 分发规则，也不新增前台功能；重点是让管理员在后台看清楚更新检测到底查到了什么。

### 主要更新

- 在 `常规设置 -> 主题更新` 增加只读状态区，显示当前版本、GitHub 最新正式版本、目标 ZIP 附件名、附件是否找到、上次检测时间、缓存过期时间和失败原因。
- 将 GitHub Release updater 的缓存升级为结构化状态，成功和失败都会留下可解释记录。
- 增加管理员内部工具按钮：`立即重新检测` 会清缓存并同步请求 GitHub，`清除缓存` 会清理主题检测缓存和 WordPress theme update transient。
- WordPress 原生主题更新流程保持不变；只有检测到正式 Release 且存在 `Yneko-Reimu-vX.Y.Z.zip` 附件，并且远端版本高于当前版本时，才写入主题更新列表。

### 说明

- 本版本不新增前台接口、公开 script/style handle、设置 key、AJAX action、post meta key、hook、URL 或模板路径。
- 新增的后台 action 是管理员设置页内部工具，受 `manage_options` 和 nonce 保护。
- 版本 metadata 会在正式 release prep 阶段统一更新。

## English

v0.2.18 is a theme updater observability fix. It does not change the GitHub Release distribution model or add front-end features; it makes the admin update check state visible and explainable.

### Highlights

- Added a read-only status area under `General -> Theme updates` showing the installed version, latest stable GitHub version, expected ZIP asset, asset availability, last check time, cache expiry, and failure reason.
- Upgraded the GitHub Release updater cache to a structured status payload so successful and failed checks both leave useful diagnostics.
- Added admin-only internal actions: `Check now` clears cache and synchronously requests GitHub, while `Clear cache` deletes the theme updater cache and WordPress theme update transient.
- The WordPress native theme update flow is unchanged; the updater writes into the theme update list only when a stable Release has the expected `Yneko-Reimu-vX.Y.Z.zip` asset and the remote version is newer than the installed version.

### Notes

- This release does not add front-end APIs, public script/style handles, setting keys, AJAX actions, post meta keys, hooks, URLs, or template paths.
- The new admin actions are internal settings-page tooling protected by `manage_options` and nonces.
- Version metadata is updated only during final release prep.
