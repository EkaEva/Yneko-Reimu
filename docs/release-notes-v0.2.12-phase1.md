# Yneko-Reimu v0.2.12 Staged Notes

These are staged development checkpoints, not a tagged public release.

## Phase 1 Highlights

- Keeps the already merged favicon root compatibility fix in the v0.2.12 change set.
- Adds Customizer-managed visual asset controls for cursor slots, the loader center image/text, the back-to-top decoration, and the sponsor decoration.
- Preserves the existing Yneko-Reimu Settings feature toggles for custom cursor and the preloader.

## Phase 2 Highlights

- Adds Customizer-managed typography and layout density controls for fonts, reading width, page width, spacing density, card/image radius, and shadow strength.
- Emits typography/layout choices as inline CSS variables after the main stylesheet so saved values can override the built Reimu defaults without changing public markup.
- Keeps defaults aligned with the existing theme appearance and does not enqueue new remote font resources.

## Compatibility

- Existing sites keep their current visual behavior when the new Customizer fields are empty.
- The old `yneko_reimu_preloader_text` value remains a fallback for the new Chinese loader text setting.
- Typography/layout controls are additive `theme_mod` values; no existing setting, hook, script handle, template path, or front-end runtime contract is renamed.
- No v0.2.12 tag or GitHub Release is created for these staged checkpoints.
