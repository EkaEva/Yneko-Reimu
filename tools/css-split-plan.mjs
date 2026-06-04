export const cssSplitPlan = [
  {
    feature: 'comments-profile',
    owner: 'assets/src/reimu-comments.css',
    currentLoading: 'global reimu-comments.css',
    targetLoading: 'global comments/profile stylesheet',
    targetOutput: 'assets/dist/reimu-comments.css',
    trigger: '#comments, #respond, #reimu-login-modal, #reimu-profile-modal, or [data-reimu-profile-open]',
    gate: 'footer.php renders the login/profile modal shell globally; comments/profile AJAX runtime remains in main reimu.js',
    maxBytes: 52 * 1024,
    selectors: [
      '#comments',
      '.reimu-comment-form',
      '.reimu-comment-upload-row',
      '.reimu-comment-current-user',
      '.reimu-profile-form'
    ],
    notes: 'Stylesheet-only split. It stays globally enqueued because login/profile modal markup can be present outside singular comment contexts; all AJAX/profile/comment runtime handlers remain in the main classic script.'
  },
  {
    feature: 'aplayer',
    owner: 'assets/src/reimu-player.css',
    currentLoading: 'conditional reimu-player.css plus conditional vendor CSS',
    targetLoading: 'feature-gated stylesheet',
    targetOutput: 'assets/dist/reimu-player.css',
    trigger: '#aplayer or meting-js when the player feature is enabled',
    gate: 'window.REIMU_CONFIG.aplayer.audio and yneko_reimu_player_enabled()',
    maxBytes: 20 * 1024,
    selectors: [
      '#sidebar > #aplayer',
      '.sidebar-wrapper-container > #aplayer',
      '.aplayer-lrc',
      '.reimu-aplayer-scroll-thumb'
    ],
    notes: 'Good first CSS split candidate because the PHP enqueue path already knows when APlayer is enabled.'
  },
  {
    feature: 'code-content',
    owner: 'assets/src/reimu-code.css',
    currentLoading: 'content-context reimu-code.css',
    targetLoading: 'content-context stylesheet',
    targetOutput: 'assets/dist/reimu-code.css',
    trigger: '.reimu-yml-editor, .highlight.reimu-code-editor, wp-block-code, or Mermaid blocks in article content',
    gate: 'is_singular() or virtual page context',
    maxBytes: 24 * 1024,
    selectors: [
      '.reimu-yml-editor',
      '.code-figcaption',
      '.article-entry .wp-block-code.reimu-yml-editor',
      '.reimu-virtual-page .highlight',
      '.article-entry .mermaid'
    ],
    notes: 'YML/code editor, virtual-page highlight, and Mermaid enhancement styles now load in singular/virtual content contexts while generic article typography stays in the main CSS.'
  },
  {
    feature: 'photoswipe',
    owner: 'assets/src/reimu-photoswipe.css',
    currentLoading: 'conditional reimu-photoswipe.css plus conditional vendor CSS and lazy PhotoSwipe JS',
    targetLoading: 'feature-gated stylesheet',
    targetOutput: 'assets/dist/reimu-photoswipe.css',
    trigger: '.article-entry img or .reimu-photoswipe-item on singular content',
    gate: 'window.REIMU_CONFIG.photoswipe and PhotoSwipe DOM presence',
    maxBytes: 12 * 1024,
    selectors: [
      '.article-entry .reimu-photoswipe-item',
      '.reimu-photoswipe-on',
      '.reimu-photoswipe-overlay',
      '.reimu-photoswipe-stage',
      '.reimu-photoswipe-nav'
    ],
    notes: 'Theme overlay/image enhancement styles now load only when the PhotoSwipe feature gate is enabled.'
  },
  {
    feature: 'share',
    owner: 'assets/src/reimu-share.css',
    currentLoading: 'global reimu-share.css plus lazy share JS',
    targetLoading: 'global share enhancement stylesheet',
    targetOutput: 'assets/dist/reimu-share.css',
    trigger: '.share-wrapper present after full load or PJAX content replacement',
    gate: 'share markup can appear after PJAX navigation, while share runtime still loads on .share-wrapper presence',
    maxBytes: 14 * 1024,
    selectors: [
      '.share-wrapper',
      '.share-link',
      '.share-icon',
      '.share-weixin',
      '.share-weixin-qr'
    ],
    notes: 'Article share layout and Weixin popup enhancement styles stay globally enqueued so PJAX navigation into posts or virtual pages cannot render share/footer markup before the stylesheet exists.'
  },
  {
    feature: 'search',
    owner: 'assets/src/reimu-search.css',
    currentLoading: 'global reimu-search.css plus lazy search JS',
    targetLoading: 'global search enhancement stylesheet',
    targetOutput: 'assets/dist/reimu-search.css',
    trigger: '.site-search template, search result form, or #nav-search-btn click',
    gate: '.site-search is rendered globally and search runtime loads on interaction',
    maxBytes: 16 * 1024,
    selectors: [
      'body.search-popup-on',
      '.reimu-search-form',
      '#reimu-search-input',
      '.reimu-hit-type',
      '.site-search .reimu-bg'
    ],
    notes: 'Readable search-page and popup enhancement rules now live in a small global stylesheet; compressed base popup layout remains in the main CSS until a safer upstream snapshot split exists.'
  }
];

export function cssSplitSummary() {
  return cssSplitPlan.map((entry) => `[css-split] ${entry.feature}: ${entry.currentLoading}; trigger=${entry.trigger}; target=${entry.targetLoading}; output=${entry.targetOutput}; budget=${(entry.maxBytes / 1024).toFixed(0)} KB.`);
}
