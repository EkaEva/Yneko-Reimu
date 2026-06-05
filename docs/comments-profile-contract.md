# Comments and Profile Contract

This document protects the highest-risk front-end area before future modularity or lazy-loading work. It is a development contract, not a new public API.

## Scope

The comments/profile runtime covers:

- login, registration, lost-password, logout, and GitHub auth popup triggers.
- profile fetch, save, email verification, TOTP generation, avatar upload, status acknowledgement, and review-status polling.
- comment submit, upload, upload discard, like, edit, delete, sorting, load-more, reply form movement, toolbar, media preview, and GIF picker.
- login-state DOM refresh after login, logout, profile save, and PJAX navigation.

The PHP entrypoint remains `inc/comments.php`. Internal comments/profile modules live under `inc/comments/`:

- `context.php` owns canonical comment post IDs, virtual project comment carriers, and AJAX language context.
- `badges.php` owns public profile/GitHub URL helpers plus comment user badge/tag normalization, review preparation, payloads, and rendered badge HTML.
- `avatars.php` owns user avatar/profile avatar helpers, avatar upload handling, avatar frames, and user-facing review-status payloads.
- `admin.php` owns administrator avatar and user badge/tag review actions.
- `uploads.php` is the upload module entrypoint.
- `uploads/media.php` owns upload settings, paths, media URL parsing, temporary review meta, and upload status helpers.
- `uploads/helpers.php` owns shared upload validation, cleanup-token, request-file, and attachment-registration helpers.
- `uploads/library.php` owns GIF/upload library queries, pending temporary upload discovery, and comment lookup by temporary URL.
- `uploads/ajax.php` owns front-end upload and discard AJAX handlers.
- `uploads/lifecycle.php` owns temporary upload promotion, comment-status/delete cleanup, expiration cleanup, and scheduling.
- `uploads/filters.php` owns GIF-only approval, duplicate-simple-comment prevention, media-count enforcement, and pending-upload moderation filters.
- `uploads/admin.php` owns administrator GIF-library uploads, Media Library upload filtering, and comment upload review actions.
- `modals.php` owns request-free login/profile modal rendering.
- `auth.php` owns login-state, login, logout, registration, and lost-password AJAX handlers plus adjacent auth helpers.
- `profile-save.php` owns internal profile-save input parsing, validation, avatar, tag, email, password, and TOTP helper steps.
- `profile.php` owns profile payload, profile fetch/save/status/email/TOTP/avatar AJAX handlers, and adjacent TOTP/profile helpers.
- `mutations.php` owns comment like, submit, edit, delete, visible-comment helpers, and comment review-status sync hooks.
- `rendering.php` owns comment form helpers, current-user identity HTML, comment avatar/author/Markdown rendering, UA/IP badges, the `wp_list_comments()` callback, and external comment panel output.

These files are implementation boundaries, not public APIs. Keep the existing function names, hooks, IDs, classes, request fields, response payloads, and data attributes stable when moving implementation details between these internal files.

## Public Surface To Preserve

Do not rename or remove these front-end config keys without a compatibility plan:

- `window.REIMU_CONFIG.login.ajaxUrl`
- `window.REIMU_CONFIG.login.nonce`
- `window.REIMU_CONFIG.login.registerNonce`
- `window.REIMU_CONFIG.login.registerCodeNonce`
- `window.REIMU_CONFIG.login.lostNonce`
- `window.REIMU_CONFIG.login.lostCodeNonce`
- `window.REIMU_CONFIG.login.profileNonce`
- `window.REIMU_CONFIG.login.logoutNonce`
- `window.REIMU_CONFIG.commentUploads.enabled`
- `window.REIMU_CONFIG.commentUploads.imageEnabled`
- `window.REIMU_CONFIG.commentUploads.gifEnabled`
- `window.REIMU_CONFIG.commentUploads.isLoggedIn`
- `window.REIMU_CONFIG.commentUploads.nonce`
- `window.REIMU_CONFIG.commentUploads.gifs`
- `window.REIMU_CONFIG.comments.nonce`

Do not rename or remove these AJAX actions:

- `yneko_reimu_login`
- `yneko_reimu_register_code`
- `yneko_reimu_register`
- `yneko_reimu_lostpassword_code`
- `yneko_reimu_lostpassword`
- `yneko_reimu_logout`
- `yneko_reimu_login_state`
- `yneko_reimu_profile_get`
- `yneko_reimu_profile_status_ack`
- `yneko_reimu_profile_email_code`
- `yneko_reimu_profile_totp_generate`
- `yneko_reimu_profile_avatar_upload`
- `yneko_reimu_profile_save`
- `yneko_reimu_comment_upload`
- `yneko_reimu_comment_upload_discard`
- `yneko_reimu_submit_comment`
- `yneko_reimu_comment_like`
- `yneko_reimu_edit_comment`
- `yneko_reimu_delete_comment`

Do not change the meaning of these DOM contracts without a compatibility plan:

- `#comments`
- `#respond`
- `#reimu-login-modal`
- `#reimu-profile-modal`
- `.reimu-comment-form`
- `.comment-list`
- `.reimu-comment-login-link`
- `[data-reimu-profile-open]`
- `[data-reimu-ajax-logout]`
- `[data-reimu-github-popup]`
- `[data-reimu-auth-popup]`
- `[data-comment-tool]`
- `[data-comment-popover]`
- `[data-comment-upload-button]`
- `[data-comment-upload-input]`
- `[data-comment-gif-library]`
- `[data-comment-like]`
- `[data-comment-edit]`
- `[data-comment-delete]`
- `[data-comment-sort]`
- `[data-profile-avatar-changed]`
- `[data-profile-tags-message]`

## Runtime Invariants

- `window.ReimuWP.init()` must remain safe to call after PJAX navigation and after login-state DOM replacement.
- `syncInlineConfig()` must continue updating runtime config before feature reinitialization.
- Login-state refresh must rebind login modal, profile modal, logout buttons, login triggers, comment upload rows, comment likes, comment owner actions, and the WordPress reply form behavior.
- Profile saves must not treat GitHub-provided avatar URLs as user-changed avatars unless the user edits the avatar URL or selects a new avatar file.
- Comment upload tokens must resolve to the final Markdown value before AJAX submit.
- Discarding unsubmitted comment uploads must keep using the existing upload nonce and cleanup payload.
- Comment submit must continue to support top-level comments and threaded replies without a full page reload.
- Comment like/edit/delete must keep using per-comment nonce attributes.
- Review-status polling must stay lightweight and must not periodically replace the full `#comments` section.

## Allowed Next Splits

These are acceptable low-risk moves if they keep the same public `assets/dist/reimu.js` behavior:

- Move pure DOM helpers into source-only modules.
- Move request-free render helpers into source-only modules.
- Move local validation helpers into source-only modules.
- Move lazy runtime loaders only for self-contained, non-auth visual features.

These require a dedicated manual QA pass before and after the change:

- Moving request handlers across files without changing the existing actions, nonces, payload fields, or JSON shapes.
- Introducing a lazy `reimu-comments.js` or `reimu-profile.js` runtime.
- Changing how login-state DOM is replaced or rebound.
- Changing profile payload structure, comment item markup, upload review state, or polling behavior.

## Manual QA Checklist

Run this checklist on a local WordPress site when changing comments/profile request handlers, runtime boundaries, DOM replacement, or PJAX rebind behavior.

### Guest Auth

- Open the login modal from the comment area.
- Submit invalid login credentials and confirm the error is shown in the modal.
- Request a registration email code when registration is enabled.
- Register with mismatched or invalid fields and confirm field errors do not leak extra account information.
- Request a lost-password email code and submit a password reset.
- Confirm the GitHub popup trigger still opens the expected OAuth URL when configured.

### Logged-In Profile

- Open the profile modal from the comment identity area.
- Refresh profile payload and confirm display name, URL, email, avatar, badges, and TOTP state render correctly.
- Change only display name or website, save, and confirm visible comment identity refreshes without a full page reload.
- Select an avatar file, confirm local validation and dirty state, then save.
- Edit the avatar URL manually, confirm `avatar_changed=1`, then save.
- Keep a GitHub-provided avatar unchanged and confirm it is not submitted as a custom avatar.
- Request an email verification code and confirm cooldown UI works.
- Generate TOTP settings and confirm the result appears without closing the modal.
- Add, remove, enable, and disable custom comment tags while the selected tag cap remains enforced.
- Trigger a reserved or duplicate tag error and confirm the related row is marked.

### Comments

- Submit a top-level text comment through AJAX.
- Reply to an existing comment through AJAX and confirm the reply appears under the parent.
- Submit an empty comment and confirm the front-end guard remains.
- Upload one image or GIF, confirm preview insertion, then submit.
- Replace an unsubmitted media item and confirm the discarded upload cleanup request is sent.
- Like and unlike a comment as a guest and as a logged-in user when supported.
- Edit and delete an owned comment as a logged-in user.
- Confirm comment sorting and load-more controls still work after submit, edit, delete, and PJAX navigation.

### Review Status

- Submit avatar, custom tag, image, and GIF changes that require review.
- Approve and reject each pending item from the admin settings page.
- Confirm front-end status notices update through lightweight polling.
- Confirm approved profile/tag/comment state appears after refresh or the expected lightweight update.
- Confirm rejected notices do not expose sensitive admin details.

### PJAX And Rebind

- Navigate from a post with comments to another post with comments.
- Open login/profile modals after navigation.
- Submit a comment after navigation.
- Open comment tool popovers after navigation.
- Confirm `window.ReimuWP.init()` does not duplicate click handlers, status timers, or upload row bindings.

## Verification Commands

The static contract gate is:

```bash
npm run check:comments-profile
```

It checks high-risk AJAX action names, nonce creation and verification, `window.REIMU_CONFIG` keys, front-end request payload fields, DOM selector anchors, source module boundaries, internal PHP modal/upload modules, and CSS selectors. Keep this gate in the same change when intentionally changing any contract listed above.

For source-only changes:

```bash
npm run check:js
npm run build
npm run check:size
```

For request-handler, PHP, or packaging changes:

```bash
npm run check
npm audit --audit-level=moderate
npm run package
npm run check:package
```

When PHP and Composer are available:

```bash
composer install
npm run lint:php
```

Also run a full `php -l` pass over `theme/Yneko-Reimu/**/*.php` when PHP code changed.
