# GitHub OAuth QA

This checklist covers the GitHub OAuth login and account-binding flow. It complements `npm run check:github-oauth`, which is a static contract gate and does not replace real callback testing.

## Static Contract

Run:

```bash
npm run check:github-oauth
```

The check guards public login actions, callback and bind URLs, bind nonce, settings keys, legacy option/meta fallback, GitHub API scope/endpoints, popup message type, and high-impact OAuth error strings.

## Local Error-Path QA

The local WordPress QA pass can exercise callback error paths without a real GitHub OAuth App by stubbing WordPress HTTP requests inside a local-only helper script.

Verified paths:

- Missing `code` / `state` callback returns HTTP 400 with `Missing GitHub OAuth response.`.
- Login start with empty OAuth settings returns HTTP 403 with `GitHub login is not configured.`.
- Callback with an expired or missing state transient returns HTTP 403 with `GitHub login state expired. Please try again.`.
- Configured OAuth start redirects to `https://github.com/login/oauth/authorize` with `read:user user:email`, `state`, `redirect_uri`, and `allow_signup=true`.
- Fake token exchange without an access token returns `GitHub did not return an access token.` and consumes the state transient.
- Stubbed GitHub API HTTP failure returns `GitHub API request failed.`.
- Stubbed invalid profile returns `GitHub profile is missing required fields.`.
- Auto-create disabled with no linked WordPress account returns `No WordPress account is linked to this GitHub account.`.
- Auto-create enabled with an email already owned by a WordPress user returns the existing-email bind guidance.
- Binding a GitHub ID already linked to another WordPress user returns the already-linked-account message.

Local helper scripts used for this QA must stay under `wp-local/` or another ignored path and must not be committed.

## Staging / Real App QA

Use a real GitHub OAuth App only on a staging site or local tunnel whose callback URL exactly matches the theme setting or default login callback URL.

Required inputs:

- GitHub OAuth App Client ID.
- GitHub OAuth App Client Secret.
- A browser-accessible site URL that GitHub can call back to, usually a staging domain or HTTPS tunnel.
- Authorization callback URL registered in the GitHub OAuth App:
  `https://staging.example.com/wp-login.php?action=yneko_reimu_github_callback`
  or the equivalent local tunnel URL.

Checklist:

- Configure Client ID and Client Secret in Appearance -> Yneko-Reimu Settings -> GitHub.
- Confirm the displayed callback URL matches the GitHub OAuth App Authorization callback URL.
- With auto-create disabled, verify an unlinked GitHub account reaches the no-linked-account error.
- Log in as an existing WordPress user and use Bind current account; verify the GitHub meta keys are saved.
- Reopen the comment login modal and verify GitHub login opens in a popup, closes after success, and refreshes the comment login state.
- Verify non-popup login redirects back to the original `redirect_to` URL after success.
- Verify an already-linked GitHub account logs in to the existing WordPress user.
- Verify admin settings never expose the client secret outside the password input value and that release packages do not contain local credentials.

Observable success signals:

- GitHub authorization URL includes `client_id`, `redirect_uri`, `scope=read:user user:email`, `state`, and `allow_signup=true`.
- The callback consumes the state transient and stores current `_yneko_reimu_github_*` user meta after a successful bind or auto-created login.
- Popup flow posts message type `yneko-reimu-github-login` to the opener, closes the popup, and refreshes the comment login/profile UI.
- Non-popup flow lands back on the original `redirect_to` URL and shows the expected logged-in comment/profile UI.
- Reusing the linked GitHub account logs in to the same WordPress user without creating a duplicate account.

## 2026-06-04 Local Real-App QA

Real GitHub OAuth happy-path QA was completed in the local WordPress environment with a real GitHub OAuth App configured through local-only helpers. The Client Secret was used only in local WordPress settings and is not recorded in repository files, public docs, or release packages.

Local setup:

- A local-only nginx proxy exposed WordPress at `http://localhost:8080`.
- The registered Authorization callback URL was `http://localhost:8080/wp-login.php?action=yneko_github_callback`.
- WordPress `home` and `siteurl` were temporarily set to `http://localhost:8080` for the QA pass.
- GitHub OAuth settings were configured locally with Client ID, Client Secret, callback URL, and auto-create enabled.
- Helper scripts stayed under ignored `wp-local/` and must not be committed.

Verified paths:

- OAuth start redirects to GitHub with the expected `client_id`, `redirect_uri`, `scope=read:user user:email`, generated `state`, and `allow_signup=true`.
- Non-popup login completes GitHub authorization, returns to `http://localhost:8080/yneko-qa-post/`, and creates/logs in the GitHub-backed WordPress user.
- Account binding succeeds for the existing local `qauser` account after clearing current and legacy GitHub meta from the auto-created user.
- The final bind state stores the GitHub ID and login on `qauser`; no duplicate WordPress user remains linked to the same GitHub account.
- Popup login from the comment login modal opens and closes without leaving an extra browser tab, posts the success message to the opener, closes the modal, refreshes the page state, and shows the logged-in profile UI.
- A later non-popup login with the already-linked GitHub account logs in to the same existing WordPress user and redirects back to the original post URL.

Compatibility notes:

- The legacy callback action `yneko_github_callback` remains supported.
- Current and legacy GitHub user meta keys were both considered during bind-conflict cleanup.
- No settings keys, login actions, nonce names, meta keys, template paths, front-end globals, or OAuth runtime behavior were changed for this QA pass.

## Current Real-App Status

The real GitHub OAuth happy path is verified locally as of 2026-06-04. Before a public release tag, run the final package/check sequence again and confirm the local-only OAuth helpers and credentials are absent from Git status and the release ZIP.

Do not create or push the `v0.2.1` tag as part of QA.
