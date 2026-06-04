# Email and TOTP QA

This checklist covers registration email codes, password-reset email codes, profile email changes, authenticator TOTP setup, and two-factor login. It complements `npm run check:i18n-messages`, which guards high-impact English messages but does not exercise WordPress mail or login state.

## Static Contract

Run:

```bash
npm run check:i18n-messages
```

The check verifies that high-impact auth/profile/email/TOTP feedback strings and verification email templates have non-empty English translations after gettext files are regenerated.

## Local Stubbed QA

The local WordPress QA pass can stub `wp_mail` with the `pre_wp_mail` filter and run the AJAX handlers directly inside WordPress. Helper scripts used for this QA must stay under `wp-local/` or another ignored path and must not be committed.

Verified paths:

- Registration rejects invalid nickname/email input.
- Registration sends a six-digit email code.
- Registration code resend is rate-limited by cooldown.
- Registration rejects an incorrect code.
- Registration succeeds with the captured code.
- Lost-password code request rejects invalid email input.
- Lost-password request for an unknown email returns the generic success message and does not send mail.
- Lost-password request for a known email sends a six-digit code.
- Lost-password reset rejects weak passwords.
- Lost-password reset rejects an incorrect code.
- Lost-password reset succeeds with the captured code.
- Profile email-code request rejects the current email address.
- Profile email-code request sends a six-digit code to a new email.
- Profile save rejects an incorrect email code.
- Profile save changes the email with the captured code.
- TOTP save rejects enabling two-factor authentication before generating a secret.
- TOTP secret generation returns a secret and `otpauth://totp/` URI.
- TOTP save rejects an incorrect authenticator code.
- TOTP save succeeds with the current generated authenticator code.
- Login with a 2FA-enabled account requires a two-factor code.
- Login rejects an incorrect two-factor code.
- Login succeeds with the current generated two-factor code.

## Staging / Manual QA

Use a staging site with real SMTP or a mail-capture service before release.

Checklist:

- Confirm registration, password reset, and profile email messages have non-empty subject and body in the active locale.
- Confirm each email contains exactly one usable six-digit code and communicates the five-minute expiry.
- Confirm email-code resend timers and cooldown messages match the browser UI state.
- Confirm password reset does not reveal whether an email address exists.
- Confirm profile email changes require the code sent to the new email address.
- Confirm the profile modal displays TOTP secret generation, QR setup, wrong-code feedback, and successful enablement.
- Confirm an authenticator app can scan/use the `otpauth://totp/` URI.
- Confirm the login modal transitions into the two-factor step and accepts a valid current TOTP code.
- Confirm cleanup removes test users, pending TOTP secrets, and captured mail data.

## Local Real SMTP Evidence

The 2026-06-04 local browser QA pass used Mailpit on the same Docker network as WordPress and a local-only SMTP mu-plugin to route real `wp_mail()` messages into the capture service.

Verified:

- Registration, password-reset, and profile-email messages were delivered through SMTP.
- Each captured verification message had a non-empty subject, body, six-digit code, and five-minute expiry text.
- Registration, password-reset, and profile-email code buttons entered the resend countdown state in the browser.
- Profile modal displayed the current email, new-email/code controls, TOTP toggle, generated secret, and visible QR image.
- TOTP save enabled two-factor authentication, and the login modal required and accepted the current authenticator code.

Do not create or push the `v0.2.1` tag as part of QA.

## Production Mail Troubleshooting

The theme sends registration, password-reset, and profile-email verification codes through WordPress `wp_mail()`. It does not bypass WP Mail SMTP or use its own SMTP transport.

When a user reports that a code email was not received:

- If the modal shows `Failed to send the verification email. Please try again later.` or the Chinese equivalent, `wp_mail()` returned `false`; check WP Mail SMTP connection/authentication and server logs first.
- If the modal shows the success/countdown state, the theme reached `wp_mail()` successfully. Check WP Mail SMTP email logs or send a WP Mail SMTP test email to confirm whether the plugin accepted and delivered the message.
- If WP Mail SMTP logs the message as sent but the recipient does not receive it, check spam, provider suppression/bounce logs, DNS authentication, rate limits, and whether the destination mailbox rejects the sender.
- If only one form fails, compare the recipient address and plugin log entry for that form. Registration sends to the new email, password reset sends to the existing account email, and profile email change sends to the new email.
