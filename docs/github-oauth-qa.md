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

Checklist:

- Configure Client ID and Client Secret in Appearance -> Yneko-Reimu Settings -> GitHub.
- Confirm the displayed callback URL matches the GitHub OAuth App Authorization callback URL.
- With auto-create disabled, verify an unlinked GitHub account reaches the no-linked-account error.
- Log in as an existing WordPress user and use Bind current account; verify the GitHub meta keys are saved.
- Reopen the comment login modal and verify GitHub login opens in a popup, closes after success, and refreshes the comment login state.
- Verify non-popup login redirects back to the original `redirect_to` URL after success.
- Verify an already-linked GitHub account logs in to the existing WordPress user.
- Verify admin settings never expose the client secret outside the password input value and that release packages do not contain local credentials.

## Current Real-App Status

The 2026-06-04 local QA environment has no real GitHub OAuth Client ID or Client Secret configured, and no OAuth credential environment variables were present. The real happy path is therefore still release-blocking until a staging callback URL and GitHub OAuth App credentials are available.

Do not create or push the `v0.1.15` tag as part of QA.
