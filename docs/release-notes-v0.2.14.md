# Yneko-Reimu v0.2.14

## 中文

v0.2.14 是 v0.2.13 之后的认证器两步验证补丁版本，修复个人资料弹窗中重新生成认证器密钥后，扫码输入的新验证码被旧密钥校验导致提示错误的问题。

### 主要更新

- 修复个人资料认证器设置流程：当用户生成新的待确认 TOTP 密钥并提交 6 位验证码时，优先校验新的 pending secret，而不是已有的启用密钥。
- 保留 v0.2.13 的弹窗简化体验：认证器两步验证已经启用后，普通资料保存仍不需要再次输入验证码。
- 为 comments/profile contract gate 增加 pending secret 校验优先级保护，避免后续维护再次回归。

### 说明

- 本版本只修复认证器保存流程，不改变 AJAX action、nonce name、user meta key、表单字段、弹窗 DOM anchor、`window.ReimuCommentsRuntime` 或 classic script 加载方式。
- v0.2.13 的复杂度清理、个人资料状态文案优化和 release ZIP 边界保持不变。

## English

v0.2.14 is an authenticator 2FA patch release after v0.2.13. It fixes the profile modal flow where a newly generated authenticator secret could still be validated against the previously enabled secret, causing the freshly scanned 6-digit code to fail.

### Highlights

- Fixed the profile authenticator setup flow so a submitted 6-digit code verifies the newly generated pending TOTP secret before the existing enabled secret.
- Kept the v0.2.13 profile-modal simplification: once authenticator 2FA is enabled, ordinary profile saves still do not require re-entering a code.
- Added a comments/profile contract guard for the pending-secret verification priority to prevent future regressions.

### Notes

- This patch only changes the authenticator save flow. AJAX actions, nonce names, user meta keys, form fields, modal DOM anchors, `window.ReimuCommentsRuntime`, and classic script loading remain compatible.
- The v0.2.13 complexity cleanup, profile status wording polish, and release ZIP boundary remain unchanged.
