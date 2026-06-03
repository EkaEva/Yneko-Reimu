export const featureLoadingPlan = [
  {
    feature: 'search',
    owner: 'assets/src/reimu-search.js and assets/src/reimu/search.js',
    status: 'lazy-runtime',
    currentLoading: 'lazy classic runtime',
    targetLoading: 'interaction',
    trigger: '#nav-search-btn click or keyboard shortcut',
    gate: 'window.REIMU_CONFIG.search',
    notes: 'Main reimu.js keeps a small trigger loader; search logic ships as assets/dist/reimu-search.js.'
  },
  {
    feature: 'share',
    owner: 'assets/src/reimu-share.js and assets/src/reimu/share.js',
    status: 'lazy-runtime',
    currentLoading: 'lazy classic runtime plus lazy qrcode.js',
    targetLoading: 'interaction',
    trigger: '.share-wrapper present; qrcode.js on Weixin click',
    gate: '.reimu-post-share',
    notes: 'Main reimu.js loads share runtime only on pages with share wrappers; QR dependency remains click-triggered.'
  },
  {
    feature: 'comments-profile',
    owner: 'assets/src/reimu.js',
    status: 'candidate',
    currentLoading: 'main-bundle',
    targetLoading: 'page-context',
    trigger: '#comments, #respond, #reimu-profile-modal, or [data-reimu-profile-open]',
    gate: 'window.REIMU_CONFIG.login and window.REIMU_CONFIG.comments',
    notes: 'Preserve existing AJAX actions, nonces, payloads, and window.ReimuWP rebind behavior.'
  },
  {
    feature: 'aplayer',
    owner: 'assets/src/reimu.js and inc/enqueue.php',
    status: 'condition-loaded',
    currentLoading: 'conditional vendor dependency plus main-bundle initializer',
    targetLoading: 'page-context-or-visibility',
    trigger: '#aplayer or meting-js entering viewport or user interaction',
    gate: 'window.REIMU_CONFIG.aplayer.audio',
    notes: 'APlayer vendor files are only enqueued when configured audio exists.'
  },
  {
    feature: 'photoswipe',
    owner: 'assets/src/reimu-photoswipe.js and assets/src/reimu/photoswipe.js',
    status: 'lazy-runtime',
    currentLoading: 'optional vendor CSS plus lazy classic runtime',
    targetLoading: 'page-context',
    trigger: '.article-entry img or .reimu-photoswipe-item on singular content',
    gate: 'window.REIMU_CONFIG.photoswipe',
    notes: 'Main reimu.js only loads the runtime when PhotoSwipe is enabled and article images exist.'
  },
  {
    feature: 'mermaid',
    owner: 'assets/src/reimu.js and inc/enqueue.php',
    status: 'condition-loaded',
    currentLoading: 'conditional vendor dependency plus main-bundle initializer',
    targetLoading: 'content-context',
    trigger: 'pre code.language-mermaid, code.language-mermaid, or .mermaid',
    gate: 'window.REIMU_CONFIG.mermaid',
    notes: 'Vendor script is currently enqueued only when the feature switch is enabled.'
  },
  {
    feature: 'katex',
    owner: 'assets/src/reimu.js and inc/enqueue.php',
    status: 'condition-loaded',
    currentLoading: 'conditional vendor dependency plus main-bundle initializer',
    targetLoading: 'content-context',
    trigger: 'math delimiters in .article-entry',
    gate: 'window.REIMU_CONFIG.katex',
    notes: 'Vendor CSS and scripts are currently enqueued only when the feature switch is enabled.'
  }
];

export function featureLoadingSummary() {
  return featureLoadingPlan.map((entry) => {
    const printableStatus = entry.status.replace(/-/g, ' ');
    return `[loading] ${entry.feature}: ${printableStatus}; trigger=${entry.trigger}; target=${entry.targetLoading}.`;
  });
}
