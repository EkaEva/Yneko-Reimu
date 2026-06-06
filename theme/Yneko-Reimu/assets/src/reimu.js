import { createCore } from './reimu/core.js';
import { createLazyRuntimeLoader } from './reimu/runtime-loader.js';
import { createPjaxUtils } from './reimu/pjax-utils.js';

(function () {
  'use strict';

  var config = window.REIMU_CONFIG || {};
  var core = createCore(config);
  var storageGet = core.storageGet;
  var storageSet = core.storageSet;
  var storageRemove = core.storageRemove;
  var qs = core.qs;
  var qsa = core.qsa;
  var getAssetBaseUrl = core.getAssetBaseUrl;
  var escapeHtml = core.escapeHtml;
  var debounce = core.debounce;
  var dispatchReimuEvent = core.dispatchReimuEvent;
  var dispatchInputEvent = core.dispatchInputEvent;
  var trimSlashes = core.trimSlashes;
  var relativePathFromUrl = core.relativePathFromUrl;
  var languageFromUrl = core.languageFromUrl;
  var initCommentSelector = function () { return runCommentsRuntime('initCommentSelector'); };
  var initWordPressCommentForm = function () { return runCommentsRuntime('initWordPressCommentForm'); };
  var initLoadMore = function () { return runCommentsRuntime('initLoadMore'); };
  var initLoginModal = function () { return runCommentsRuntime('initLoginModal'); };
  var initProfileModal = function () { return runCommentsRuntime('initProfileModal'); };
  var setLoginModalOpen = function (open) {
    return loadCommentsRuntime().then(function (runtime) {
      if (runtime && typeof runtime.setLoginModalOpen === 'function') {
        runtime.setLoginModalOpen(open);
      }
    }).catch(function () {});
  };
  var refreshCommentLoginState = function () { return runCommentsRuntime('refreshCommentLoginState'); };
  var root = document.documentElement;
  var body = null;
  var tooltipTimer = null;
  var loaderHideTimer = null;
  var pjaxController = null;
  var pjaxRequestId = 0;
  var aplayerInstance = null;
  var scrollDirection = 0;
  var aplayerState = {
    index: 0,
    currentTime: 0,
    paused: true,
    volume: Number(config.aplayer && config.aplayer.volume || .7)
  };

  function t(key, fallback) {
    var i18n = config.i18n || {};
    return i18n[key] || fallback;
  }

  var confirmDialog = null;
  var confirmDialogState = null;

  function ensureConfirmDialog() {
    if (confirmDialog && document.body && document.body.contains(confirmDialog.root)) {
      return confirmDialog;
    }
    var modal = document.createElement('div');
    modal.className = 'reimu-confirm-modal';
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML = '<div class="reimu-confirm-modal__mask" data-reimu-confirm-cancel></div><div class="reimu-confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-confirm-title"><h2 id="reimu-confirm-title"></h2><p class="reimu-confirm-modal__message" data-reimu-confirm-message></p><div class="reimu-confirm-modal__actions"><button type="button" class="reimu-confirm-modal__cancel" data-reimu-confirm-cancel></button><button type="button" class="reimu-confirm-modal__ok" data-reimu-confirm-ok></button></div></div>';
    (document.body || document.documentElement).appendChild(modal);
    confirmDialog = {
      root: modal,
      title: qs('#reimu-confirm-title', modal),
      message: qs('[data-reimu-confirm-message]', modal),
      ok: qs('[data-reimu-confirm-ok]', modal),
      cancel: qs('.reimu-confirm-modal__cancel', modal)
    };
    qsa('[data-reimu-confirm-cancel]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        closeThemeConfirm(false);
      });
    });
    var ok = qs('[data-reimu-confirm-ok]', modal);
    if (ok) {
      ok.addEventListener('click', function () {
        closeThemeConfirm(true);
      });
    }
    return confirmDialog;
  }

  function closeThemeConfirm(confirmed) {
    var state = confirmDialogState;
    if (!state) {
      return;
    }
    confirmDialogState = null;
    document.removeEventListener('keydown', state.onKeydown);
    if (confirmDialog) {
      confirmDialog.root.classList.remove('show');
      confirmDialog.root.setAttribute('aria-hidden', 'true');
      confirmDialog.root.hidden = true;
    }
    if (body) {
      body.classList.remove('reimu-confirm-on');
    }
    if (state.previousFocus && state.previousFocus.focus) {
      state.previousFocus.focus();
    }
    state.resolve(!!confirmed);
  }

  function requestThemeConfirm(message, options) {
    if (!document.body || typeof Promise === 'undefined') {
      return Promise.resolve(typeof globalThis.confirm === 'function' ? globalThis.confirm(message) : false);
    }
    return new Promise(function (resolve) {
      if (confirmDialogState) {
        closeThemeConfirm(false);
      }
      var dialog = ensureConfirmDialog();
      options = options || {};
      if (dialog.title) {
        dialog.title.textContent = options.title || t('confirmTitle', '请确认');
      }
      if (dialog.message) {
        dialog.message.textContent = message || '';
      }
      if (dialog.cancel) {
        dialog.cancel.textContent = options.cancelText || t('confirmCancel', '取消');
      }
      if (dialog.ok) {
        dialog.ok.textContent = options.okText || t('confirmOk', '确定');
      }
      confirmDialogState = {
        resolve: resolve,
        previousFocus: document.activeElement,
        onKeydown: function (event) {
          if (event.key === 'Escape') {
            event.preventDefault();
            closeThemeConfirm(false);
          }
        }
      };
      dialog.root.hidden = false;
      dialog.root.setAttribute('aria-hidden', 'false');
      if (body) {
        body.classList.add('reimu-confirm-on');
      }
      window.setTimeout(function () {
        dialog.root.classList.add('show');
        if (dialog.ok) {
          dialog.ok.focus();
        }
      }, 0);
      document.addEventListener('keydown', confirmDialogState.onKeydown);
    });
  }

  var lazyRuntimeLoader = createLazyRuntimeLoader({
    qs: qs,
    getAssetBaseUrl: getAssetBaseUrl
  });

  function loadSearchRuntime() {
    return lazyRuntimeLoader.loadRuntime('search', {
      globalName: 'ReimuSearchRuntime',
      scriptId: 'yneko-reimu-search-runtime',
      scriptName: 'reimu-search.js',
      label: 'Search'
    });
  }
  function runSearchRuntime(method) {
    return loadSearchRuntime().then(function (runtime) {
      if (runtime && typeof runtime[method] === 'function') {
        runtime[method]();
      }
    }).catch(function (error) {
      if (window.console && window.console.warn) {
        window.console.warn('[Yneko-Reimu] search runtime skipped:', error);
      }
    });
  }

  function initSearch() {
    var wrapper = qs('.site-search');
    var popup = qs('.site-search .popup');
    var trigger = qs('#nav-search-btn, .popup-trigger');
    if (!trigger && !wrapper) {
      return;
    }
    if (wrapper && popup && !popup.classList.contains('show')) {
      wrapper.setAttribute('aria-hidden', 'true');
      wrapper.hidden = true;
      popup.inert = true;
    }
    if (window.ReimuSearchRuntime && typeof window.ReimuSearchRuntime.init === 'function') {
      window.ReimuSearchRuntime.init();
      return;
    }
    if (root.dataset.searchLazyReady) {
      return;
    }
    root.dataset.searchLazyReady = 'true';
    var openLazySearch = function (event) {
      if (event.reimuSearchHandled) {
        return;
      }
      var target = event.target && event.target.closest ? event.target.closest('#nav-search-btn, .popup-trigger') : null;
      if (!target) {
        return;
      }
      if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      event.reimuSearchHandled = true;
      runSearchRuntime('open');
    };
    document.addEventListener('click', openLazySearch);
    document.addEventListener('keydown', openLazySearch);
    var lazyCloseSearch = function () {
      runSearchRuntime('init').then(function () {
        if (typeof window.ReimuSearchClose === 'function' && window.ReimuSearchClose !== lazyCloseSearch) {
          window.ReimuSearchClose();
        }
      });
    };
    window.ReimuSearchClose = lazyCloseSearch;
  }

  function loadPhotoSwipeRuntime() {
    return lazyRuntimeLoader.loadRuntime('photoswipe', {
      globalName: 'ReimuPhotoSwipeRuntime',
      scriptId: 'yneko-reimu-photoswipe-runtime',
      scriptName: 'reimu-photoswipe.js',
      label: 'PhotoSwipe'
    });
  }
  function initPhotoSwipeRuntime() {
    if (!config.photoswipe) {
      return;
    }
    if (!qs('.article-entry img, .reimu-photoswipe-item')) {
      return;
    }
    if (window.REIMU_PHOTOSWIPE && typeof window.REIMU_PHOTOSWIPE.destroy === 'function') {
      try {
        window.REIMU_PHOTOSWIPE.destroy();
      } catch (error) {}
    }
    window.REIMU_PHOTOSWIPE = null;
    loadPhotoSwipeRuntime().then(function (runtime) {
      if (runtime && typeof runtime.init === 'function') {
        runtime.init();
      }
    }).catch(function (error) {
      if (window.console && window.console.warn) {
        window.console.warn('[Yneko-Reimu] PhotoSwipe runtime skipped:', error);
      }
    });
  }

  function loadShareRuntime() {
    return lazyRuntimeLoader.loadRuntime('share', {
      globalName: 'ReimuShareRuntime',
      scriptId: 'yneko-reimu-share-runtime',
      scriptName: 'reimu-share.js',
      label: 'Share'
    });
  }
  function initShare() {
    if (!qs('.share-wrapper')) {
      return;
    }
    loadShareRuntime().then(function (runtime) {
      if (runtime && typeof runtime.init === 'function') {
        runtime.init();
      }
    }).catch(function (error) {
      if (window.console && window.console.warn) {
        window.console.warn('[Yneko-Reimu] share runtime skipped:', error);
      }
    });
  }

  function hasCommentsProfileAnchors() {
    return !!qs('#comments, #respond, #reimu-login-modal, #reimu-profile-modal, [data-reimu-profile-open]');
  }

  function loadCommentsRuntime() {
    return lazyRuntimeLoader.loadRuntime('comments', {
      globalName: 'ReimuCommentsRuntime',
      scriptId: 'yneko-reimu-comments-runtime',
      scriptName: 'reimu-comments.js',
      label: 'Comments'
    });
  }
  function runCommentsRuntime(method) {
    if (!hasCommentsProfileAnchors() && !window.ReimuCommentsRuntime) {
      return Promise.resolve(false);
    }
    return loadCommentsRuntime().then(function (runtime) {
      if (runtime && typeof runtime[method] === 'function') {
        return runtime[method]();
      }
      return false;
    }).catch(function (error) {
      if (window.console && window.console.warn) {
        window.console.warn('[Yneko-Reimu] comments runtime skipped:', error);
      }
      return false;
    });
  }

  function getHeadingFromHash(hash) {
    if (!hash) {
      return null;
    }
    var id = hash.replace(/^#/, '');
    var target = document.getElementById(id);
    if (target) {
      return target;
    }
    try {
      return document.getElementById(decodeURIComponent(id));
    } catch (error) {
      return null;
    }
  }

  function scrollHeadingIntoView(target, behavior) {
    if (!target) {
      return false;
    }
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    var rect = target.getBoundingClientRect();
    var targetHeight = Math.min(rect.height || 0, viewportHeight);
    var centerOffset = Math.max(80, (viewportHeight - targetHeight) / 2);
    var top = Math.max(0, rect.top + window.scrollY - centerOffset);
    window.scrollTo({
      top: top,
      behavior: behavior || 'smooth'
    });
    return true;
  }

  function loaderTextForLanguage(language) {
    var loaderTexts = config.loaderTexts || {};
    if (loaderTexts[language]) {
      return loaderTexts[language];
    }
    return language === 'en_US' ? 'Loading...' : '未来有你...';
  }

  function syncLoaderLanguage(url) {
    var word = qs('#loader .loading-word');
    if (!word) {
      return;
    }
    var current = String(word.textContent || '').trim();
    var zhText = loaderTextForLanguage('zh_CN');
    var enText = loaderTextForLanguage('en_US');
    var knownDefault = !current || current === zhText || current === enText || current === '少女祈祷中...';
    if (!knownDefault) {
      return;
    }
    word.textContent = loaderTextForLanguage(languageFromUrl(url));
  }

  function setPageLoading(loading) {
    body = document.body || body;
    root.classList.toggle('reimu-page-loading', !!loading);
    if (body) {
      body.classList.toggle('reimu-page-loading', !!loading);
      body.setAttribute('aria-busy', loading ? 'true' : 'false');
    }
  }

  function showLoader(url) {
    var loader = qs('#loader');
    window.clearTimeout(loaderHideTimer);
    if (url) {
      syncLoaderLanguage(url);
    }
    setPageLoading(true);
    if (body) {
      body.style.overflow = 'hidden';
      body.classList.remove('mobile-nav-on');
      body.classList.remove('search-popup-on');
    }
    var mask = qs('#mask');
    if (mask) {
      mask.classList.add('hide');
      mask.dataset.mode = '';
    }
    var container = qs('#container');
    var header = qs('#header-nav');
    if (container) {
      container.style.marginRight = '';
    }
    if (header) {
      header.style.marginRight = '';
    }
    var search = qs('.site-search');
    var popup = qs('.site-search .popup');
    if (search) {
      search.classList.remove('active');
    }
    if (popup) {
      popup.classList.remove('show');
    }
    if (loader) {
      loader.classList.remove('loading');
      loader.setAttribute('aria-hidden', 'false');
    }
  }

  function hideLoader(delay) {
    var loader = qs('#loader');
    window.clearTimeout(loaderHideTimer);
    loaderHideTimer = window.setTimeout(function () {
      if (body) {
        body.style.overflow = 'auto';
        body.classList.remove('search-popup-on');
      }
      if (loader) {
        loader.classList.add('loading');
        loader.setAttribute('aria-hidden', 'true');
      }
      setPageLoading(false);
      revealViewportAos();
    }, Number(delay || 0));
  }

  function resolveTheme(choice) {
    if (choice === 'true' || choice === 'dark') {
      return 'dark';
    }
    if (choice === 'false' || choice === 'light') {
      return 'light';
    }
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(choice) {
    var resolved = resolveTheme(choice);
    root.setAttribute('data-theme', resolved);
    try {
      localStorage.setItem('dark_mode', choice);
    } catch (error) {}
    (body || document.body || root).dispatchEvent(new CustomEvent('reimu:theme-set', { detail: { isDark: resolved === 'dark' } }));
  }

  function initTheme() {
    var stored = null;
    try {
      stored = localStorage.getItem('dark_mode');
    } catch (error) {}
    var choice = stored || config.darkModeDefault || 'auto';
    applyTheme(choice);
    qsa('[data-theme-toggle]').forEach(function (button) {
      if (button.dataset.themeReady) {
        return;
      }
      button.dataset.themeReady = 'true';
      button.addEventListener('click', function () {
        var next = root.getAttribute('data-theme') === 'dark' ? 'false' : 'true';
        applyTheme(next);
      });
    });
  }

  function initLoader() {
    var loader = qs('#loader');
    if (!loader) {
      return;
    }
    var started = Date.now();
    function hide() {
      var wait = Math.max(0, 500 - (Date.now() - started));
      hideLoader(wait);
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', hide, { once: true });
    } else {
      hide();
    }
    if (loader.dataset.loaderReady) {
      return;
    }
    loader.dataset.loaderReady = 'true';
    loader.addEventListener('click', function () {
      hideLoader();
    });
  }

  function initNav() {
    var header = qs('#header-nav');
    var toggle = qs('#main-nav-toggle');
    var mask = qs('#mask');
    var mobileNav = qs('#mobile-nav');
    var lastY = window.scrollY;

    function setMobileNavOpen(open) {
      body = document.body || body;
      if (body) {
        body.classList.toggle('mobile-nav-on', !!open);
      }
      if (mobileNav) {
        mobileNav.setAttribute('aria-hidden', open ? 'false' : 'true');
        mobileNav.inert = !open;
      }
      if (toggle) {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
      if (mask) {
        mask.classList.toggle('hide', !open);
        if (!open) {
          mask.dataset.mode = '';
        }
      }
    }

    if (mobileNav && !body.classList.contains('mobile-nav-on')) {
      mobileNav.setAttribute('aria-hidden', 'true');
      mobileNav.inert = true;
    }

    if (toggle && !toggle.dataset.navReady) {
      toggle.dataset.navReady = 'true';
      toggle.setAttribute('aria-expanded', body.classList.contains('mobile-nav-on') ? 'true' : 'false');
      toggle.addEventListener('click', function () {
        setMobileNavOpen(!body.classList.contains('mobile-nav-on'));
      });
    }

    if (mask && !mask.dataset.navReady) {
      mask.dataset.navReady = 'true';
      mask.addEventListener('click', function () {
        if (body.classList.contains('search-popup-on')) {
          return;
        }
        setMobileNavOpen(false);
      });
    }

    if (!root.dataset.navScrollReady) {
      root.dataset.navScrollReady = 'true';
      window.addEventListener('scroll', function () {
        var current = window.scrollY;
        scrollDirection = current > lastY ? 1 : (current < lastY ? -1 : 0);
        var liveHeader = qs('#header-nav');
        if (liveHeader && config.navHide && current > 300 && current > lastY) {
          liveHeader.classList.add('header-nav-hidden');
        } else if (liveHeader) {
          liveHeader.classList.remove('header-nav-hidden');
        }
        lastY = current;

        var top = qs('.sidebar-top');
        if (top) {
          top.classList.toggle('show', current > 420);
        }
      }, { passive: true });
    }

    var topButton = qs('.sidebar-top');
    if (topButton && !topButton.dataset.topReady) {
      topButton.dataset.topReady = 'true';
      topButton.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  }

  function initLanguageDropdown() {
    var selected = qs('#select-selected');
    var selectedLabel = qs('#selected-lang');
    var list = qs('#select-items');
    if (!selected || !list) {
      return;
    }
    if (selected.dataset.languageReady) {
      return;
    }
    selected.dataset.languageReady = 'true';
    var options = qsa('li', list);
    selected.addEventListener('click', function () {
      var expanded = selected.getAttribute('aria-expanded') === 'true';
      list.classList.toggle('show');
      selected.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    });
    options.forEach(function (item) {
      item.addEventListener('click', function () {
        list.classList.remove('show');
        selected.setAttribute('aria-expanded', 'false');
        if (item.dataset.url && item.dataset.url !== window.location.href) {
          navigateTo(item.dataset.url);
        }
      });
    });
    if (!root.dataset.languageDelegated) {
      root.dataset.languageDelegated = 'true';
      document.addEventListener('click', function (event) {
        var liveList = qs('#select-items');
        var liveSelected = qs('#select-selected');
        if (!liveList || !liveSelected || event.target.closest('.custom-dropdown')) {
          return;
        }
        liveList.classList.remove('show');
        liveSelected.setAttribute('aria-expanded', 'false');
      });
    }
  }

  function initSidebarActive() {
    var langPrefix = (config.i18nPrefix || 'en').replace(/^\/+|\/+$/g, '');
    function normalizeNavPath(pathname) {
      var path = String(pathname || '/').replace(/\/+$/, '') || '/';
      if (langPrefix && path === '/' + langPrefix) {
        return '/';
      }
      if (langPrefix && path.indexOf('/' + langPrefix + '/') === 0) {
        path = path.slice(langPrefix.length + 1) || '/';
      }
      return path.replace(/\/+$/, '') || '/';
    }
    var current = normalizeNavPath(window.location.pathname);
    qsa('.sidebar-menu-link-wrap.link-active').forEach(function (wrap) {
      wrap.classList.remove('link-active');
    });
    qsa('.sidebar-menu-link-wrap').forEach(function (wrap) {
      var link = qs('.sidebar-menu-link-dummy', wrap);
      if (!link) {
        return;
      }
      var path = normalizeNavPath(link.pathname);
      if (path === current) {
        wrap.classList.add('link-active');
      }
    });
  }

  function showTooltip(text) {
    var tooltip = qs('#copy-tooltip');
    if (!tooltip) {
      return;
    }
    tooltip.textContent = text;
    tooltip.classList.add('show');
    window.clearTimeout(tooltipTimer);
    tooltipTimer = window.setTimeout(function () {
      tooltip.classList.remove('show');
    }, 1600);
  }

  function initCodeCopy() {
    var candidates = [];

    qsa('.article-entry pre').forEach(function (pre) {
      var source = getCodeEditorSource(pre);
      if (!source || !source.container || !source.container.parentNode) {
        return;
      }
      if (source.container.dataset.reimuCodeEditorReady) {
        return;
      }
      if (candidates.some(function (candidate) {
        return candidate.container === source.container;
      })) {
        return;
      }
      candidates.push(source);
    });

    candidates.forEach(function (source) {
      var text = normalizeCodeText(source.text);
      if (!text) {
        return;
      }
      var editor = createCodeEditor(text, source.lang, source.container);
      source.container.dataset.reimuCodeEditorReady = 'true';
      source.container.parentNode.replaceChild(editor, source.container);
    });
  }

  function getCodeEditorSource(pre) {
    if (!pre || pre.closest('.reimu-yml-editor')) {
      return null;
    }

    var highlight = pre.closest('.highlight');
    if (highlight && !highlight.classList.contains('reimu-yml-editor')) {
      var highlightCode = qs('td.code pre, .code pre, pre code', highlight) || pre;
      return {
        container: highlight,
        text: highlightCode.textContent || '',
        lang: detectCodeLanguage(highlight, highlightCode)
      };
    }

    var block = pre.closest('.wp-block-code');
    var code = qs('code', pre) || pre;

    return {
      container: block || pre,
      text: code.textContent || pre.textContent || '',
      lang: detectCodeLanguage(block || pre, code)
    };
  }

  function normalizeCodeText(text) {
    return String(text || '').replace(/\r\n?/g, '\n').replace(/\n$/, '');
  }

  function detectCodeLanguage(container, code) {
    var explicit = getExplicitCodeLanguage(container) || getExplicitCodeLanguage(code);
    if (explicit) {
      return explicit;
    }

    var text = normalizeCodeText(code && code.textContent ? code.textContent : '');
    var trimmed = text.trim();

    if ((trimmed.charAt(0) === '{' || trimmed.charAt(0) === '[') && isJsonLike(trimmed)) {
      return 'JSON';
    }
    if (/^\s*<([a-z][\w:-]*)(\s|>)/i.test(trimmed)) {
      return 'HTML';
    }
    if (/^\s*[-\w"']+:\s+/m.test(trimmed) || /^\s*-\s+[-\w"']+:\s+/m.test(trimmed)) {
      return 'YML';
    }
    if (/^\s*(git|npm|pnpm|yarn|docker|docker-compose|cargo|pip|python|cd)\b/m.test(trimmed)) {
      return 'BASH';
    }
    if (/[.#]?[\w-]+\s*\{[\s\S]*:\s*[^;]+;/.test(trimmed)) {
      return 'CSS';
    }

    return 'CODE';
  }

  function getExplicitCodeLanguage(element) {
    if (!element) {
      return '';
    }

    var lang = element.getAttribute('data-lang') || element.getAttribute('data-language');
    if (lang) {
      return normalizeLanguageLabel(lang);
    }

    var className = typeof element.className === 'string' ? element.className : '';
    var match = className.match(/(?:^|\s)(?:language|lang|brush|source)-([a-z0-9_+#.-]+)/i);
    if (match) {
      return normalizeLanguageLabel(match[1]);
    }
    match = className.match(/(?:^|\s)(bash|shell|sh|powershell|ps1|json|ya?ml|toml|ini|html?|xml|css|scss|sass|js|jsx|ts|tsx|php|py|python|rust|rs|md|markdown|dockerfile|sql)(?:\s|$)/i);
    return match ? normalizeLanguageLabel(match[1]) : '';
  }

  function normalizeLanguageLabel(lang) {
    var value = String(lang || '').replace(/^language-/, '').replace(/^lang-/, '').trim().toLowerCase();
    var labels = {
      yaml: 'YML',
      yml: 'YML',
      json: 'JSON',
      javascript: 'JS',
      js: 'JS',
      jsx: 'JSX',
      typescript: 'TS',
      ts: 'TS',
      tsx: 'TSX',
      shell: 'BASH',
      bash: 'BASH',
      sh: 'SH',
      powershell: 'PS1',
      ps1: 'PS1',
      python: 'PY',
      py: 'PY',
      rust: 'RS',
      rs: 'RS',
      markdown: 'MD',
      md: 'MD',
      dockerfile: 'DOCKER',
      html: 'HTML',
      htm: 'HTML',
      xml: 'XML',
      css: 'CSS',
      scss: 'SCSS',
      sass: 'SASS',
      php: 'PHP',
      toml: 'TOML',
      ini: 'INI',
      sql: 'SQL'
    };
    return labels[value] || value.toUpperCase();
  }

  function isJsonLike(text) {
    try {
      JSON.parse(text);
      return true;
    } catch (error) {
      return false;
    }
  }

  function createCodeEditor(text, lang, original) {
    var lines = text.split('\n');
    var editor = document.createElement('figure');
    var langLabel = normalizeLanguageLabel(lang || 'CODE') || 'CODE';
    var langClass = langLabel.toLowerCase().replace(/[^a-z0-9-]+/g, '-');
    var originalClass = original && typeof original.className === 'string' ? original.className : '';

    editor.className = ['highlight', langClass, 'reimu-yml-editor', 'reimu-code-editor', originalClass].filter(Boolean).join(' ');
    editor.dataset.copyText = text;
    editor.dataset.lang = langLabel;

    editor.innerHTML = [
      '<div class="code-figcaption">',
      '<div class="code-left-wrap"><div class="code-decoration"></div><div class="code-lang">',
      escapeHtml(langLabel),
      '</div></div>',
      '<div class="code-right-wrap">',
      '<button type="button" class="code-copy icon-copy" aria-label="',
      escapeHtml(config.copyText || t('copy', '复制')),
      '"></button>',
      '<button type="button" class="icon-chevron-down code-expand" aria-label="',
      escapeHtml(config.collapseText || t('collapseCode', '折叠代码')),
      '" aria-expanded="true"></button>',
      '</div></div>',
      '<div class="code-area"><table><tr><td class="gutter"><pre>',
      lines.map(function (line, index) {
        return '<span class="line">' + escapeHtml(index + 1) + '</span>';
      }).join('<br>'),
      '<br></pre></td><td class="code"><pre>',
      lines.map(function (line) {
        return '<span class="line">' + highlightCodeLine(line, langLabel) + '</span>';
      }).join('<br>'),
      '<br></pre></td></tr></table></div>'
    ].join('');

    return editor;
  }

  function highlightCodeLine(line, lang) {
    if (lang === 'YML') {
      return highlightYamlLine(line);
    }
    if (lang === 'JSON') {
      return highlightJsonLine(line);
    }
    return escapeHtml(line);
  }

  function highlightYamlLine(line) {
    var match = String(line || '').match(/^(\s*)(-\s+)?([A-Za-z0-9_-]+:)(\s*)(.*)$/);
    if (!match) {
      return escapeHtml(line);
    }

    var html = escapeHtml(match[1]);
    if (match[2]) {
      html += '<span class="bullet">-</span> ';
    }
    html += '<span class="attr">' + escapeHtml(match[3]) + '</span>';
    html += escapeHtml(match[4]);
    html += highlightYamlValue(match[5]);
    return html;
  }

  function highlightYamlValue(value) {
    var raw = String(value || '');
    if (!raw) {
      return '';
    }
    var trimmed = raw.trim();
    var prefix = raw.slice(0, raw.indexOf(trimmed));
    if (/^(true|false|null|yes|no)$/i.test(trimmed) || /^-?\d+(\.\d+)?$/.test(trimmed)) {
      return escapeHtml(prefix) + '<span class="literal">' + escapeHtml(trimmed) + '</span>';
    }
    if (/^#/.test(trimmed)) {
      return escapeHtml(prefix) + '<span class="comment">' + escapeHtml(trimmed) + '</span>';
    }
    return escapeHtml(prefix) + '<span class="string">' + escapeHtml(trimmed) + '</span>';
  }

  function highlightJsonLine(line) {
    return escapeHtml(line)
      .replace(/(&quot;[^&]*?&quot;)(\s*:)/g, '<span class="attr">$1</span>$2')
      .replace(/(:\s*)(&quot;.*?&quot;)/g, '$1<span class="string">$2</span>')
      .replace(/(:\s*)(true|false|null|-?\d+(?:\.\d+)?)/g, '$1<span class="literal">$2</span>');
  }

  function copyText(text, button) {
    var done = function () {
      if (button) {
        button.classList.add('icon-check');
        button.classList.remove('icon-copy');
        window.setTimeout(function () {
          button.classList.add('icon-copy');
          button.classList.remove('icon-check');
        }, 1000);
      }
      showTooltip(config.copiedText || t('copied', '复制成功 (*^▽^*)'));
    };
    var fail = function () {
      if (button) {
        button.classList.add('icon-times');
        button.classList.remove('icon-copy');
        window.setTimeout(function () {
          button.classList.add('icon-copy');
          button.classList.remove('icon-times');
        }, 1000);
      }
      showTooltip(config.failedText || t('copyFailed', '复制失败 (ﾟ⊿ﾟ)ﾂ'));
    };

    var copyWithTextarea = function () {
      var textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.setAttribute('readonly', 'readonly');
      textarea.style.position = 'fixed';
      textarea.style.top = '0';
      textarea.style.left = '0';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.focus();
      textarea.select();
      textarea.setSelectionRange(0, textarea.value.length);
      try {
        document.execCommand('copy');
      } catch (error) {
        textarea.remove();
        return false;
      }
      textarea.remove();
      return true;
    };

    if (copyWithTextarea()) {
      done();
      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done).catch(fail);
      return;
    }

    fail();
  }

  function initYmlEditors() {
    qsa('.reimu-yml-editor').forEach(function (editor) {
      if (editor.dataset.ymlReady) {
        return;
      }
      editor.dataset.ymlReady = 'true';

      var copy = qs('.code-copy', editor);
      if (copy) {
        copy.addEventListener('click', function () {
          var text = editor.getAttribute('data-copy-text');
          if (!text) {
            var code = qs('td.code', editor);
            text = code ? code.innerText : editor.innerText;
          }
          copyText(text, copy);
        });
      }

      var expand = qs('.code-expand', editor);
      if (expand) {
        expand.addEventListener('click', function () {
          var closed = editor.classList.toggle('code-closed');
          expand.setAttribute('aria-expanded', closed ? 'false' : 'true');
        });
      }
    });
  }

  function initHeatmap() {
    var element = qs('#heatmap');
    var heatmapConfig = window.REIMU_HEATMAP_CONFIG;
    if (!element || !heatmapConfig) {
      return;
    }
    if (element.dataset.heatmapReady) {
      return;
    }
    element.dataset.heatmapReady = 'true';

    var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var tooltip = qs('#heatmap-tooltip');
    var currentDate = new Date();
    var levelStandard = String(heatmapConfig.levelStandard || '1000,5000,10000').split(',').map(Number);
    var i18n = heatmapConfig.i18n || {};
    var lang = document.documentElement.lang || 'zh-CN';
    if (lang.toLowerCase().indexOf('zh') === 0 && !i18n[lang]) {
      lang = 'zh-CN';
    }
    if (lang === 'en-US' && !i18n[lang] && i18n.en) {
      lang = 'en';
    }
    if (!i18n[lang]) {
      lang = lang.toLowerCase().indexOf('zh') === 0 && i18n['zh-CN'] ? 'zh-CN' : 'en';
    }
    var isChinese = lang.toLowerCase().indexOf('zh') === 0;
    var text = i18n[lang] || {
      no_articles: '没有文章',
      words: '字',
      total_articles: '共 $1 篇文章, $2 字',
      no_writing_on: '{date} 没有写作',
      writing_on: '{posts} {words} 于 {date}',
      year_total: '{posts} {words} 于 {year}'
    };

    function unitText(num, singular, plural, zhUnit) {
      if (isChinese) {
        return String(num) + ' ' + zhUnit;
      }
      return String(num) + ' ' + (num === 1 ? singular : plural);
    }

    function templateText(template, values) {
      return String(template || '').replace(/\{([a-z]+)\}/gi, function (match, key) {
        return Object.prototype.hasOwnProperty.call(values, key) ? values[key] : match;
      });
    }

    function heatmapTotalText(posts, words) {
      if (isChinese) {
        return String(text.total_articles || '共 $1 篇文章, $2 字').replace('$1', posts).replace('$2', words);
      }

      return unitText(posts, 'post', 'posts', '篇文章') + ', ' + unitText(words, 'word', 'words', text.words || '字') + ' in total';
    }

    function getLevelFromWordCount(count) {
      if (count <= 0) {
        return 0;
      }
      if (count <= levelStandard[0]) {
        return 1;
      }
      if (count <= levelStandard[1]) {
        return 2;
      }
      if (count <= levelStandard[2]) {
        return 3;
      }
      return 4;
    }

    function formatDate(date) {
      return new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().split('T')[0];
    }

    function hideHeatmapTooltip() {
      if (tooltip) {
        tooltip.style.display = 'none';
      }
    }

    function transformArticlesData(articles) {
      var dailyStats = {};
      (articles || []).forEach(function (article) {
        if (article.virtual) {
          return;
        }
        var date = new Date(article.date);
        if (Number.isNaN(date.getTime()) || date > currentDate) {
          return;
        }
        var dayStr = formatDate(date);
        if (!dailyStats[dayStr]) {
          dailyStats[dayStr] = {
            count: 0,
            post: 0,
            articles: []
          };
        }
        dailyStats[dayStr].count += Number(article.wordcount || 0);
        dailyStats[dayStr].post += 1;
        dailyStats[dayStr].articles.push(article);
      });

      return Object.keys(dailyStats).map(function (dayStr) {
        var entry = dailyStats[dayStr];
        return {
          level: getLevelFromWordCount(entry.count),
          date: new Date(dayStr + 'T00:00:00').getTime(),
          count: entry.count,
          post: entry.post,
          articles: entry.articles
        };
      });
    }

    function completeContributionData(userData) {
      userData.sort(function (a, b) { return a.date - b.date; });
      var configuredYears = (heatmapConfig.articleStats || []).map(function (article) {
        return new Date(article.date).getFullYear();
      }).filter(function (year) {
        return !Number.isNaN(year);
      });
      var firstYear = configuredYears.length ? Math.min.apply(null, configuredYears) : currentDate.getFullYear();
      var startDate = new Date(firstYear, 0, 1);
      var allData = {};
      var userDataMap = {};

      userData.forEach(function (data) {
        var dataDate = new Date(data.date);
        var key = dataDate.getFullYear() + '-' + dataDate.getMonth() + '-' + dataDate.getDate();
        userDataMap[key] = data;
      });

      for (var year = startDate.getFullYear(); year <= currentDate.getFullYear(); year += 1) {
        var startYear = year === currentDate.getFullYear() ? new Date(new Date(currentDate).setDate(currentDate.getDate() - 365)) : new Date(year, 0, 1);
        var endYear = year === currentDate.getFullYear() ? new Date(currentDate.getTime() + 86400000) : new Date(year + 1, 0, 1);
        var yearData = [];
        for (var d = new Date(startYear); d < endYear; d.setDate(d.getDate() + 1)) {
          var key = d.getFullYear() + '-' + d.getMonth() + '-' + d.getDate();
          yearData.push(userDataMap[key] || {
            level: 0,
            date: new Date(d).getTime(),
            count: 0,
            post: 0,
            articles: []
          });
        }
        allData[year] = yearData;
      }

      return allData;
    }

    function tileClickHandler(event) {
      var tile = event.target;
      if (!tooltip || !tile.classList.contains('tile')) {
        return;
      }
      var date = new Date(Number(tile.dataset.date));
      var formattedDate = date.toLocaleDateString(isChinese ? 'zh-CN' : 'en-US');
      var articles = JSON.parse(tile.getAttribute('data-articles') || '[]');
      var totalWords = 0;
      var html = '<div class="tooltip-header"><div class="tooltip-header-content">' + escapeHtml(formattedDate) + ' (Level ' + escapeHtml(tile.dataset.level) + ')</div><div class="popup-btn-close tooltip-close"></div></div><div class="tooltip-content"><ul>';

      if (!articles.length) {
        html += '<li>' + escapeHtml(text.no_articles) + '</li>';
      } else {
        articles.forEach(function (article) {
          var words = Number(article.wordcount || 0);
          totalWords += words;
        html += '<li><a href="' + escapeHtml(article.url || '#') + '">' + escapeHtml(article.title || 'Untitled') + '</a> (' + escapeHtml(words) + ' ' + escapeHtml(text.words) + ')</li>';
        });
      }

      html += '</ul><div class="tooltip-footer">' + escapeHtml(heatmapTotalText(articles.length, totalWords)) + '</div></div>';
      tooltip.innerHTML = html;

      var rect = tile.getBoundingClientRect();
      tooltip.style.display = 'block';
      tooltip.style.visibility = 'hidden';
      tooltip.style.left = '0px';
      var tooltipWidth = tooltip.getBoundingClientRect().width;
      var left = rect.right + 10;
      if (left + tooltipWidth > window.innerWidth) {
        left = rect.left - tooltipWidth - 10;
      }
      tooltip.style.left = left + 'px';
      tooltip.style.top = rect.top + 'px';
      tooltip.style.visibility = 'visible';

      var closeBtn = qs('.tooltip-close', tooltip);
      if (closeBtn) {
        closeBtn.addEventListener('click', hideHeatmapTooltip);
      }
    }

    function createCalendar(contributionData) {
      element.innerHTML = '<div class="outer-box"><div class="year-select"></div><div class="inner-box"><div class="calendar-container"><span class="week">Mon</span><span class="week">Wed</span><span class="week">Fri</span><div class="legend">Less' + [0, 1, 2, 3, 4].map(function (level) { return '<i class="tile" data-level="' + level + '"></i>'; }).join('') + 'More</div><div class="tiles"></div></div></div></div>';

      var calendarContainer = qs('.calendar-container', element);
      var yearSelector = qs('.year-select', element);
      var tilesContainer = qs('.tiles', element);
      var allContributionData = completeContributionData(contributionData);
      var currentDisplayYear = currentDate.getFullYear();

      yearSelector.innerHTML = Object.keys(allContributionData).sort(function (a, b) { return b - a; }).map(function (year) {
        return '<div class="year-option ' + (Number(year) === currentDisplayYear ? 'active' : '') + '">' + year + '</div>';
      }).join('');

      function generateYearContributions(year) {
        tilesContainer.innerHTML = '';
        qsa('.month', calendarContainer).forEach(function (month) { month.remove(); });
        qsa('.total', calendarContainer).forEach(function (total) { total.remove(); });

        var data = allContributionData[year] || [];
        if (!data.length) {
          return;
        }
        var startRow = new Date(data[0].date).getDay();
        var latestMonth = -1;
        var lastGridColumn = -1;
        var totalStat = { count: 0, post: 0 };
        var monthFragment = document.createDocumentFragment();
        var tilesFragment = document.createDocumentFragment();

        data.forEach(function (c, index) {
          var date = new Date(c.date);
          var month = date.getMonth();
          totalStat.count += Number(c.count || 0);
          totalStat.post += Number(c.post || 0);

          if (date.getDay() === 0 && month !== latestMonth) {
            var gridColumn = 2 + Math.floor((index + startRow) / 7);
            if (gridColumn - lastGridColumn <= 1) {
              gridColumn += 2 - gridColumn + lastGridColumn;
            }
            lastGridColumn = gridColumn;
            latestMonth = month;
            var monthLabel = document.createElement('span');
            monthLabel.className = 'month';
            monthLabel.textContent = monthNames[month];
            monthLabel.style.gridColumn = gridColumn;
            monthFragment.appendChild(monthLabel);
          }

          var tile = document.createElement('i');
          tile.className = 'tile';
          tile.dataset.level = c.level;
          tile.dataset.date = c.date;
          tile.title = c.post
            ? templateText(text.writing_on || '{posts} {words} on {date}', {
              posts: unitText(c.post, 'post', 'posts', '篇文章'),
              words: unitText(c.count, 'word', 'words', text.words || '字'),
              date: formatDate(date)
            })
            : templateText(text.no_writing_on || 'No writing on {date}', { date: formatDate(date) });
          tile.setAttribute('data-articles', JSON.stringify(c.articles || []));
          tile.addEventListener('click', tileClickHandler);
          tilesFragment.appendChild(tile);
        });

        if (tilesFragment.firstElementChild) {
          tilesFragment.firstElementChild.style.gridRow = startRow + 1;
        }

        calendarContainer.appendChild(monthFragment);
        tilesContainer.appendChild(tilesFragment);
        calendarContainer.insertAdjacentHTML('beforeend', '<div class="total">' + escapeHtml(templateText(text.year_total || '{posts} {words} in {year}', {
          posts: unitText(totalStat.post, 'post', 'posts', '篇文章'),
          words: unitText(totalStat.count, 'word', 'words', text.words || '字'),
          year: year
        })) + '</div>');
      }

      yearSelector.addEventListener('click', function (event) {
        if (!event.target.classList.contains('year-option')) {
          return;
        }
        var active = qs('.active', yearSelector);
        if (active) {
          active.classList.remove('active');
        }
        event.target.classList.add('active');
        generateYearContributions(event.target.textContent);
      });

      generateYearContributions(currentDisplayYear);
    }

    createCalendar(transformArticlesData(heatmapConfig.articleStats || []));
  }

  function initToc() {
    var links = qsa('.sidebar-toc-wrapper .toc-link');
    if (!links.length) {
      return;
    }
    var headings = links.map(function (link) {
      return getHeadingFromHash(link.getAttribute('href'));
    }).filter(Boolean);
    var activeLock = null;
    var syncTocTimer = null;

    function syncTocScrollState() {
      qsa('.sidebar-toc-wrapper').forEach(function (wrapper) {
        wrapper.dataset.reimuTocScrollable = wrapper.scrollHeight > wrapper.clientHeight + 2 ? 'true' : 'false';
      });
    }

    function scheduleTocScrollSync(delay) {
      window.clearTimeout(syncTocTimer);
      syncTocTimer = window.setTimeout(syncTocScrollState, typeof delay === 'number' ? delay : 80);
    }

    qsa('.sidebar-toc-btn').forEach(function (button) {
      if (button.dataset.tocSwitchReady) {
        button.replaceWith(button.cloneNode(true));
      }
    });

    qsa('.sidebar-toc-btn').forEach(function (button) {
      button.dataset.tocSwitchReady = 'true';
      button.addEventListener('click', function () {
        if (button.classList.contains('current')) {
          return;
        }
        qsa('.sidebar-toc-btn').forEach(function (item) { item.classList.add('current'); });
        qsa('.sidebar-common-btn').forEach(function (item) { item.classList.remove('current'); });
        qsa('.sidebar-toc-sidebar').forEach(function (el) { el.classList.remove('hidden'); });
        qsa('.sidebar-common-sidebar').forEach(function (el) { el.classList.add('hidden'); });
        syncTocScrollState();
        scheduleTocScrollSync(40);
      });
    });

    qsa('.sidebar-common-btn').forEach(function (button) {
      if (button.dataset.tocSwitchReady) {
        button.replaceWith(button.cloneNode(true));
      }
    });

    qsa('.sidebar-common-btn').forEach(function (button) {
      button.dataset.tocSwitchReady = 'true';
      button.addEventListener('click', function () {
        if (button.classList.contains('current')) {
          return;
        }
        qsa('.sidebar-common-btn').forEach(function (item) { item.classList.add('current'); });
        qsa('.sidebar-toc-btn').forEach(function (item) { item.classList.remove('current'); });
        qsa('.sidebar-toc-sidebar').forEach(function (el) { el.classList.add('hidden'); });
        qsa('.sidebar-common-sidebar').forEach(function (el) { el.classList.remove('hidden'); });
        syncTocScrollState();
        scheduleTocScrollSync(40);
      });
    });

    links.forEach(function (link, index) {
      if (link.dataset.tocReady) {
        link.replaceWith(link.cloneNode(true));
      }
    });

    links = qsa('.sidebar-toc-wrapper .toc-link');
    headings = links.map(function (link) {
      return getHeadingFromHash(link.getAttribute('href'));
    }).filter(Boolean);

    links.forEach(function (link, index) {
      link.dataset.tocReady = 'true';
      link.addEventListener('click', function (event) {
        var heading = getHeadingFromHash(link.getAttribute('href'));
        if (!heading) {
          return;
        }
        event.preventDefault();
        activeLock = index;
        scrollHeadingIntoView(heading, 'smooth');
        if (link.closest('#mobile-nav')) {
          body.classList.remove('mobile-nav-on');
          var mask = qs('#mask');
          if (mask) {
            mask.classList.add('hide');
          }
        }
        window.setTimeout(function () {
          activateTocLink(link, index);
          activeLock = null;
          scheduleTocScrollSync(120);
        }, 420);
        scheduleTocScrollSync(40);
      });
    });

    function clearTocState() {
      qsa('.sidebar-toc-wrapper .active, .sidebar-toc-wrapper .current').forEach(function (element) {
        element.classList.remove('active', 'current');
      });
    }

    function activateTocLink(link, index) {
      var item = link.closest('.toc-item');
      var sidebar = link.closest('.sidebar-toc-sidebar');
      var wrapper = link.closest('.sidebar-toc-wrapper');

      if (!item || item.classList.contains('current')) {
        return;
      }

      clearTocState();
      link.classList.add('active');
      item.classList.add('active', 'current');

      var parent = item.parentElement;
      while (parent && parent !== sidebar) {
        if (parent.classList && parent.classList.contains('toc-item')) {
          parent.classList.add('active');
          var parentLink = parent.querySelector(':scope > .toc-link');
          if (parentLink) {
            parentLink.classList.add('active');
          }
        }
        parent = parent.parentElement;
      }

      if (sidebar && !sidebar.classList.contains('hidden') && wrapper) {
        syncTocScrollState();
        wrapper.scrollTo({
          top: wrapper.scrollTop + item.offsetTop - (wrapper.clientHeight / 2),
          behavior: 'smooth'
        });
      }
      scheduleTocScrollSync(340);
    }

    function findActiveIndex(entries) {
      if (!entries.length) {
        return 0;
      }
      var index = 0;
      var entry = entries[index];

      if (entry.boundingClientRect.top > 0) {
        index = headings.indexOf(entry.target);
        return index === 0 ? 0 : index - 1;
      }

      for (; index < entries.length; index += 1) {
        if (entries[index].boundingClientRect.top <= 0) {
          entry = entries[index];
        } else {
          return headings.indexOf(entry.target);
        }
      }

      return headings.indexOf(entry.target);
    }

    syncTocScrollState();
    window.addEventListener('resize', debounce(syncTocScrollState, 120));
    window.addEventListener('load', function () {
      scheduleTocScrollSync(120);
    });

    if (!('IntersectionObserver' in window)) {
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      if (activeLock !== null) {
        return;
      }

      var index = findActiveIndex(entries) + (scrollDirection > 0 ? 1 : 0);
      var active = links[index];
      if (active) {
        activateTocLink(active, index);
      }
    }, { rootMargin: '0px 0px -100% 0px', threshold: 0 });

    headings.forEach(function (heading) {
      observer.observe(heading);
    });

  }

  function initArticleAnchors() {
    qsa('.article-entry h1 > a:first-of-type, .article-entry h2 > a:first-of-type, .article-entry h3 > a:first-of-type, .article-entry h4 > a:first-of-type, .article-entry h5 > a:first-of-type, .article-entry h6 > a:first-of-type').forEach(function (anchor) {
      if (!anchor.classList.contains('paragraph-anchor')) {
        return;
      }
      anchor.innerHTML = config.anchorIcon === false ? '' : '&#xe635;';
    });
  }


  function initLazyload() {
    qsa('img.lazyload, .article-entry img').forEach(function (img) {
      var src = img.getAttribute('data-src');
      if (src && !img.getAttribute('src')) {
        img.setAttribute('src', src);
      }
      if (img.complete) {
        img.classList.add('lazyloaded');
      } else {
        img.addEventListener('load', function () {
          img.classList.add('lazyloaded');
        }, { once: true });
      }
    });
  }

  function initAos() {
    var items = qsa('[data-aos]');
    if (!items.length) {
      return;
    }
    if (!('IntersectionObserver' in window)) {
      items.forEach(function (item) { item.classList.add('aos-animate'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('aos-animate');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px 22% 0px', threshold: 0 });
    items.forEach(function (item) {
      if (item.classList.contains('aos-animate')) {
        return;
      }
      item.classList.add('aos-init');
      observer.observe(item);
    });
    window.requestAnimationFrame(revealViewportAos);
  }

  function revealViewportAos() {
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    var preload = Math.min(260, Math.max(120, viewportHeight * .24));
    qsa('[data-aos]').forEach(function (item) {
      if (item.classList.contains('aos-animate')) {
        return;
      }
      var rect = item.getBoundingClientRect();
      var itemPreload = item.classList.contains('post-wrap') ? Math.max(preload, viewportHeight * .45) : preload;
      if (rect.top < viewportHeight + itemPreload && rect.bottom > -itemPreload) {
        item.classList.add('aos-init');
        item.classList.add('aos-animate');
      }
    });
  }

  function getAPlayerAudio() {
    if (aplayerInstance && aplayerInstance.audio && typeof aplayerInstance.audio.play === 'function') {
      return aplayerInstance.audio;
    }
    if (aplayerInstance && aplayerInstance.audio && aplayerInstance.audio.audio && typeof aplayerInstance.audio.audio.play === 'function') {
      return aplayerInstance.audio.audio;
    }
    return qs('#aplayer audio') || qs('audio.aplayer-audio') || qs('audio');
  }

  function captureAPlayerState() {
    var audio = getAPlayerAudio();
    if (!audio) {
      return;
    }
    var list = aplayerInstance && aplayerInstance.list ? aplayerInstance.list : null;
    aplayerState = {
      index: list && typeof list.index === 'number' ? list.index : (aplayerState.index || 0),
      currentTime: Number(audio.currentTime || 0),
      paused: !!audio.paused,
      volume: Number(audio.volume || aplayerState.volume || .7)
    };
  }

  function restoreAPlayerState() {
    var audio = getAPlayerAudio();
    if (!audio || !aplayerInstance || !aplayerState) {
      return;
    }
    var state = aplayerState;
    try {
      if (aplayerInstance.list && typeof aplayerInstance.list.switch === 'function' && typeof state.index === 'number') {
        aplayerInstance.list.switch(state.index);
      }
    } catch (error) {}
    try {
      audio.volume = Number(state.volume || .7);
    } catch (error) {}
    var seek = function () {
      try {
        if (state.currentTime > 0) {
          if (typeof aplayerInstance.seek === 'function') {
            aplayerInstance.seek(state.currentTime);
          } else {
            audio.currentTime = state.currentTime;
          }
        }
      } catch (error) {}
      if (!state.paused) {
        var playPromise = typeof aplayerInstance.play === 'function' ? aplayerInstance.play() : audio.play();
        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.catch(function () {});
        }
      }
    };
    if (audio.readyState >= 1) {
      seek();
    } else {
      audio.addEventListener('loadedmetadata', seek, { once: true });
    }
  }

  function bindAPlayerState() {
    var audio = getAPlayerAudio();
    if (!audio) {
      return;
    }
    if (audio.dataset) {
      if (audio.dataset.reimuStateReady) {
        return;
      }
      audio.dataset.reimuStateReady = 'true';
    } else if (audio.reimuStateReady) {
      return;
    } else {
      audio.reimuStateReady = true;
    }
    ['play', 'pause', 'timeupdate', 'volumechange', 'ended'].forEach(function (eventName) {
      audio.addEventListener(eventName, captureAPlayerState);
    });
  }

  function getLiveAPlayer() {
    return qs('#aplayer.aplayer[data-aplayer-ready], #aplayer.aplayer[data-aplayerReady], #aplayer.aplayer');
  }

  function getAPlayerSlot(parent) {
    return qs('#aplayer, meting-js', parent || document);
  }

  function preserveAPlayer() {
    var player = getLiveAPlayer();
    if (!player || player.dataset.reimuPreserve !== 'true') {
      return null;
    }
    captureAPlayerState();
    return player;
  }

  function placeAPlayerInSlot(player, scope) {
    if (!player) {
      return;
    }
    var className = player.className || '';
    var targetSelector = '#aplayer';
    if (/\baplayer-after-widget\b/.test(className)) {
      targetSelector = '#aplayer.aplayer-after-widget, meting-js.aplayer-after-widget, #aplayer';
    } else if (/\baplayer-after-sidebar\b/.test(className)) {
      targetSelector = '#aplayer.aplayer-after-sidebar, meting-js.aplayer-after-sidebar, #aplayer';
    }
    var target = qs(targetSelector, scope || document) || getAPlayerSlot(scope || document);
    if (target && target !== player && target.parentNode) {
      target.parentNode.replaceChild(player, target);
    } else if (!target) {
      var container = qs('.sidebar-wrapper-container', scope || document) || qs('#sidebar', scope || document);
      if (container) {
        container.insertBefore(player, container.firstChild);
      }
    }
    player.style.position = '';
    player.style.top = '';
    player.style.left = '';
    player.style.width = '';
  }

  function mountAPlayerInFlow() {
    var player = getLiveAPlayer() || qs('#aplayer');
    var duplicatePlayers = qsa('#aplayer').filter(function (item) {
      return item !== player;
    });
    duplicatePlayers.forEach(function (duplicate) {
      if (!duplicate.parentNode) {
        return;
      }
      duplicate.remove();
    });
    qsa('[data-reimu-aplayer-anchor], #reimu-aplayer-portal').forEach(function (element) {
      element.remove();
    });
    if (!player) {
      return;
    }
    placeAPlayerInSlot(player);
  }

  function syncAPlayerScrollCursorZone(list, ol, zone) {
    if (!list || !ol || !zone) {
      return;
    }
    var canScroll = ol.scrollHeight - ol.clientHeight > 2;
    var isHidden = list.classList.contains('aplayer-list-hide') || ol.clientHeight < 2;
    zone.style.top = ol.offsetTop + 'px';
    zone.style.height = ol.clientHeight + 'px';
    zone.hidden = !canScroll || isHidden;
    var thumb = qs('.reimu-aplayer-scroll-thumb', zone);
    if (thumb && canScroll && !isHidden) {
      var ratio = ol.clientHeight / ol.scrollHeight;
      var thumbHeight = Math.max(24, ol.clientHeight * ratio);
      var travel = Math.max(0, ol.clientHeight - thumbHeight);
      var maxScroll = Math.max(1, ol.scrollHeight - ol.clientHeight);
      var thumbTop = travel * (ol.scrollTop / maxScroll);
      thumb.style.height = thumbHeight + 'px';
      thumb.style.transform = 'translate3d(0,' + thumbTop + 'px,0)';
    }
  }

  function scrollAPlayerListToPointer(ol, zone, clientY) {
    var rect = zone.getBoundingClientRect();
    var ratio = Math.max(0, Math.min(1, (clientY - rect.top) / (rect.height || 1)));
    var maxScroll = Math.max(0, ol.scrollHeight - ol.clientHeight);
    ol.scrollTop = maxScroll * ratio;
  }

  function setupAPlayerScrollCursorZone(player) {
    if (!player) {
      return;
    }
    var list = qs('.aplayer-list', player);
    var ol = qs('.aplayer-list ol', player);
    if (!list || !ol) {
      return;
    }
    var zone = qs('.reimu-aplayer-scroll-cursor-zone', list);
    if (!zone) {
      zone = document.createElement('div');
      zone.className = 'reimu-aplayer-scroll-cursor-zone';
      zone.setAttribute('aria-hidden', 'true');
      var thumb = document.createElement('span');
      thumb.className = 'reimu-aplayer-scroll-thumb';
      zone.appendChild(thumb);
      list.appendChild(zone);
    }
    var sync = function () {
      window.requestAnimationFrame(function () {
        syncAPlayerScrollCursorZone(list, ol, zone);
      });
    };
    sync();
    if (zone.dataset.reimuScrollCursorReady) {
      return;
    }
    zone.dataset.reimuScrollCursorReady = 'true';
    var dragging = false;
    var activePointerId = null;
    var stopAPlayerScrollCursorEvent = function (event) {
      event.stopPropagation();
    };
    var setHovering = function (enabled) {
      list.classList.toggle('reimu-scroll-zone-hover', !!enabled);
    };
    ['click', 'dblclick', 'mousedown', 'mouseup', 'mousemove'].forEach(function (eventName) {
      zone.addEventListener(eventName, stopAPlayerScrollCursorEvent);
    });
    var startHovering = function () {
      setHovering(true);
    };
    var stopHovering = function () {
      if (!dragging) {
        setHovering(false);
      }
    };
    zone.addEventListener('pointerenter', startHovering);
    zone.addEventListener('mouseenter', startHovering);
    zone.addEventListener('mouseover', startHovering);
    zone.addEventListener('mousemove', startHovering);
    zone.addEventListener('pointerleave', stopHovering);
    zone.addEventListener('mouseleave', stopHovering);
    zone.addEventListener('mouseout', function (event) {
      if (!zone.contains(event.relatedTarget)) {
        stopHovering();
      }
    });
    zone.addEventListener('wheel', function (event) {
      if (zone.hidden) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      ol.scrollTop += event.deltaY;
      sync();
    }, { passive: false });
    zone.addEventListener('pointerdown', function (event) {
      if (zone.hidden || event.button !== 0) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      dragging = true;
      activePointerId = event.pointerId;
      zone.classList.add('is-dragging');
      list.classList.add('reimu-scroll-zone-dragging');
      setHovering(true);
      if (typeof zone.setPointerCapture === 'function') {
        zone.setPointerCapture(event.pointerId);
      }
      scrollAPlayerListToPointer(ol, zone, event.clientY);
    });
    zone.addEventListener('pointermove', function (event) {
      if (!dragging || event.pointerId !== activePointerId) {
        return;
      }
      event.preventDefault();
      event.stopPropagation();
      scrollAPlayerListToPointer(ol, zone, event.clientY);
    });
    var stopDragging = function (event) {
      if (event && activePointerId !== null && event.pointerId !== activePointerId) {
        return;
      }
      if (event && event.type !== 'lostpointercapture') {
        event.preventDefault();
        event.stopPropagation();
      }
      dragging = false;
      activePointerId = null;
      zone.classList.remove('is-dragging');
      list.classList.remove('reimu-scroll-zone-dragging');
      setHovering(false);
    };
    zone.addEventListener('pointerup', stopDragging);
    zone.addEventListener('pointercancel', stopDragging);
    zone.addEventListener('lostpointercapture', stopDragging);
    ol.addEventListener('scroll', sync, { passive: true });
    list.addEventListener('transitionend', sync);
    window.addEventListener('resize', sync, { passive: true });
    if ('MutationObserver' in window) {
      var observer = new MutationObserver(sync);
      observer.observe(list, { attributes: true, attributeFilter: ['class', 'style'] });
      observer.observe(ol, { childList: true, subtree: true });
    }
  }

  function positionAPlayerVolumeControl(player) {
    if (!player) {
      return;
    }
    var time = qs('.aplayer-time', player);
    var volume = qs('.aplayer-volume-wrap', player);
    var menu = qs('.aplayer-icon-menu', player);
    if (!time || !volume || !menu || volume.nextElementSibling === menu) {
      return;
    }
    time.insertBefore(volume, menu);
  }

  function syncAPlayerLrcOverflow(player) {
    var lrc = qs('.aplayer-lrc', player);
    if (!lrc) {
      return;
    }
    qsa('.aplayer-lrc p', lrc).forEach(function (line) {
      if (line.dataset.reimuLrcWrapped !== 'true') {
        var text = line.textContent;
        line.textContent = '';
        var inner = document.createElement('span');
        inner.textContent = text;
        line.appendChild(inner);
        line.dataset.reimuLrcWrapped = 'true';
      }
      var content = line.firstElementChild;
      var isCurrent = line.classList.contains('aplayer-lrc-current');
      var isOverflow = !!(content && content.scrollWidth > line.clientWidth + 2);
      line.classList.toggle('reimu-lrc-overflow', isCurrent && isOverflow);
      if (content && isCurrent && isOverflow) {
        var distance = Math.max(0, content.scrollWidth - line.clientWidth + 18);
        var duration = Math.max(6, Math.min(14, distance / 14 + 4));
        line.style.setProperty('--reimu-lrc-marquee-distance', '-' + distance + 'px');
        line.style.setProperty('--reimu-lrc-marquee-duration', duration + 's');
      } else {
        line.style.removeProperty('--reimu-lrc-marquee-distance');
        line.style.removeProperty('--reimu-lrc-marquee-duration');
      }
    });
  }

  function patchAPlayerAccessibility(player) {
    if (!player) {
      return;
    }
    var labels = [
      ['.aplayer-icon-play', t('aplayerPlay', '播放')],
      ['.aplayer-icon-pause', t('aplayerPause', '暂停')],
      ['.aplayer-icon-back', t('aplayerPrevious', '上一首')],
      ['.aplayer-icon-forward', t('aplayerNext', '下一首')],
      ['.aplayer-icon-menu', t('aplayerPlaylist', '播放列表')],
      ['.aplayer-icon-lrc', t('aplayerLyrics', '歌词')],
      ['.aplayer-icon-volume-down', t('aplayerVolume', '音量')],
      ['.aplayer-icon-volume-up', t('aplayerVolume', '音量')],
      ['.aplayer-icon-order', t('aplayerOrder', '播放顺序')],
      ['.aplayer-icon-loop', t('aplayerLoop', '循环模式')],
      ['.aplayer-icon-miniswitcher', t('aplayerMini', '迷你模式')]
    ];
    labels.forEach(function (item) {
      qsa(item[0], player).forEach(function (button) {
        button.setAttribute('aria-label', item[1]);
        if (!button.getAttribute('title')) {
          button.setAttribute('title', item[1]);
        }
      });
    });
    qsa('.aplayer-volume-bar-wrap, .aplayer-bar-wrap', player).forEach(function (control) {
      if (!control.getAttribute('aria-label')) {
        control.setAttribute('aria-label', control.classList.contains('aplayer-volume-bar-wrap') ? t('aplayerVolume', '音量') : t('aplayerProgress', '播放进度'));
      }
    });
  }

  function bindAPlayerLayoutSync() {
    var player = getLiveAPlayer();
    if (!player) {
      return;
    }
    setupAPlayerScrollCursorZone(player);
    positionAPlayerVolumeControl(player);
    syncAPlayerLrcOverflow(player);
    patchAPlayerAccessibility(player);
    if (player.dataset.reimuLayoutReady) {
      return;
    }
    player.dataset.reimuPreserve = 'true';
    player.dataset.reimuLayoutReady = 'true';
    var list = qs('.aplayer-list', player);
    if (list && !list.classList.contains('aplayer-list-hide')) {
      list.classList.add('aplayer-list-hide');
    }
    window.setInterval(function () {
      syncAPlayerLrcOverflow(player);
    }, 800);
  }

  function renderMermaidBlocks() {
    if (!config.mermaid || !window.mermaid) {
      return;
    }
    window.mermaid.initialize({
      startOnLoad: false,
      theme: root.getAttribute('data-theme') === 'dark' ? 'dark' : 'default'
    });
    qsa('pre code.language-mermaid, code.language-mermaid').forEach(function (code) {
      var pre = code.closest('pre');
      var container = pre || code;
      if (container.dataset.mermaidReady) {
        return;
      }
      var div = document.createElement('div');
      div.className = 'mermaid';
      div.textContent = code.textContent || '';
      div.dataset.mermaidReady = 'pending';
      container.parentNode.replaceChild(div, container);
    });
    qsa('.mermaid').forEach(function (element) {
      if (element.dataset.mermaidReady === 'true') {
        return;
      }
      element.removeAttribute('data-processed');
    });
    try {
      if (typeof window.mermaid.run === 'function') {
        window.mermaid.run({ nodes: qsa('.mermaid') });
      } else if (typeof window.mermaid.init === 'function') {
        window.mermaid.init(undefined, qsa('.mermaid'));
      }
      qsa('.mermaid').forEach(function (element) {
        element.dataset.mermaidReady = 'true';
      });
    } catch (error) {}
  }

  function initExternalIntegrations() {
    if (config.katex && window.renderMathInElement) {
      window.renderMathInElement(document.body, {
        delimiters: [
          { left: '$$', right: '$$', display: true },
          { left: '$', right: '$', display: false },
          { left: '\\(', right: '\\)', display: false },
          { left: '\\[', right: '\\]', display: true }
        ],
        throwOnError: false
      });
    }

    if (config.photoswipe) {
      initPhotoSwipeRuntime();
    }

    renderMermaidBlocks();

    var existingPlayer = getLiveAPlayer();
    if (existingPlayer && existingPlayer.dataset.reimuPreserve === 'true') {
      mountAPlayerInFlow();
      bindAPlayerState();
      bindAPlayerLayoutSync();
      return;
    }

    var aplayerElement = qs('#aplayer');
    if (config.aplayer && window.APlayer && config.aplayer.audio && aplayerElement && !aplayerElement.dataset.aplayerReady) {
      aplayerElement.dataset.aplayerReady = 'true';
      try {
        aplayerInstance = new window.APlayer({
          container: aplayerElement,
          fixed: !!config.aplayer.fixed,
          autoplay: !!config.aplayer.autoplay,
          loop: config.aplayer.loop || 'all',
          order: config.aplayer.order || 'list',
          preload: config.aplayer.preload || 'metadata',
          volume: Number(config.aplayer.volume || .7),
          mutex: config.aplayer.mutex !== false,
          listFolded: !!config.aplayer.listFolded,
          listMaxHeight: config.aplayer.listMaxHeight || '320px',
          lrcType: Number(config.aplayer.lrcType || 0),
          audio: config.aplayer.audio
        });
        restoreAPlayerState();
        bindAPlayerState();
        mountAPlayerInFlow();
        bindAPlayerLayoutSync();
      } catch (error) {}
    } else if (aplayerInstance) {
      mountAPlayerInFlow();
      bindAPlayerState();
      bindAPlayerLayoutSync();
    }
  }

  function toggleSponsor(button) {
    var wrapper = button.closest('.sponsor-wrapper');
    if (!wrapper) {
      return;
    }

    var active = !button.classList.contains('active');
    button.classList.toggle('active', active);
    button.setAttribute('aria-expanded', active ? 'true' : 'false');
    var tip = qs('.sponsor-tip', wrapper);
    var qr = qs('.sponsor-qr', wrapper);
    if (tip) {
      tip.classList.toggle('active', active);
    }
    if (qr) {
      qr.style.setProperty('transition', 'none', 'important');
      qr.classList.toggle('active', active);
      qr.style.setProperty('height', active ? Math.max(qr.scrollHeight, 260) + 'px' : '0px', 'important');
      qr.style.setProperty('max-height', active ? Math.max(qr.scrollHeight, 360) + 'px' : '0px', 'important');
      qr.style.setProperty('opacity', active ? '1' : '0', 'important');
      qr.style.setProperty('transform', active ? 'translateY(0)' : 'translateY(-10px)', 'important');
      qsa('img', qr).forEach(function (img) {
        if (!img.complete) {
          img.addEventListener('load', function () {
            if (button.classList.contains('active')) {
              qr.style.setProperty('height', Math.max(qr.scrollHeight, 260) + 'px', 'important');
              qr.style.setProperty('max-height', Math.max(qr.scrollHeight, 360) + 'px', 'important');
            }
          }, { once: true });
        }
      });
    }
  }

  function initSponsor() {
    qsa('.sponsor-wrapper').forEach(function (wrapper) {
      var button = qs('.sponsor-button', wrapper);
      if (!button || button.dataset.sponsorReady) {
        return;
      }
      button.dataset.sponsorReady = 'true';

      button.addEventListener('click', function (event) {
        event.reimuSponsorHandled = true;
        toggleSponsor(button);
      });
      button.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          toggleSponsor(button);
        }
      });
    });

    if (!document.documentElement.dataset.sponsorDelegated) {
      document.documentElement.dataset.sponsorDelegated = 'true';
      document.addEventListener('click', function (event) {
        if (event.reimuSponsorHandled) {
          return;
        }
        var button = event.target.closest('.sponsor-button');
        if (!button) {
          return;
        }
        toggleSponsor(button);
      });
    }
  }


  function initFirework() {
    if (!config.firework || !window.firework || window.matchMedia('(max-width: 768px)').matches || root.dataset.fireworkReady) {
      return;
    }
    root.dataset.fireworkReady = 'true';
    window.firework({
      excludeElements: ['input', 'textarea', 'select', 'option', 'label', '.aplayer', '.search-popup', '#search-form', '[data-reimu-profile-open]', '.reimu-profile-modal', '.reimu-profile-modal *'],
      particles: [
        {
          shape: 'circle',
          move: ['emit'],
          easing: 'easeOutExpo',
          colors: ['var(--red-1)', 'var(--red-2)', 'var(--red-3)', 'var(--red-4)'],
          number: 20,
          duration: [1200, 1800],
          shapeOptions: { radius: [16, 32], alpha: [.3, .5] }
        },
        {
          shape: 'circle',
          move: ['diffuse'],
          easing: 'easeOutExpo',
          colors: ['var(--red-0)'],
          number: 1,
          duration: [1200, 1800],
          shapeOptions: { radius: 20, alpha: [.2, .5], lineWidth: 6 }
        }
      ]
    });
  }

  function initExternalLinks() {
    qsa('.article-entry a[href^="http"]').forEach(function (link) {
      if (link.hostname !== window.location.hostname) {
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
      }
    });
  }

  var pjaxUtils = createPjaxUtils({
    qs: qs,
    qsa: qsa,
    getConfig: function () {
      return config;
    },
    setConfig: function (nextConfig) {
      config = nextConfig || config;
    },
    initLoginModal: initLoginModal,
    initProfileModal: initProfileModal,
    setLoginModalOpen: setLoginModalOpen
  });
  var samePath = pjaxUtils.samePath;
  var isSamePageHashUrl = pjaxUtils.isSamePageHashUrl;
  var isAssetPath = pjaxUtils.isAssetPath;
  var shouldPjaxLink = pjaxUtils.shouldPjaxLink;
  var syncHeadMetadata = pjaxUtils.syncHeadMetadata;
  var syncInlineConfig = pjaxUtils.syncInlineConfig;
  var replayPjaxScripts = pjaxUtils.replayPjaxScripts;
  var getAuthModalState = pjaxUtils.getAuthModalState;
  var restoreAuthModalState = pjaxUtils.restoreAuthModalState;
  function scrollToHash(hash, options) {
    var target = getHeadingFromHash(hash);
    if (!target) {
      return false;
    }
    return scrollHeadingIntoView(target, options && options.instant ? 'auto' : 'smooth');
  }

  function hideHeatmapTooltip() {
    var tooltip = qs('#heatmap-tooltip');
    if (tooltip) {
      tooltip.style.display = 'none';
      tooltip.style.visibility = '';
      tooltip.innerHTML = '';
    }
  }

  function replaceElement(selector, nextDoc, options) {
    options = options || {};
    var current = qs(selector);
    var next = qs(selector, nextDoc);
    if (current && next) {
      if (options.classOnly) {
        current.className = next.className;
      } else {
        current.replaceWith(next.cloneNode(true));
      }
    } else if (!current && next && options.appendTo) {
      options.appendTo.appendChild(next.cloneNode(true));
    } else if (current && !next && !options.keepMissing) {
      current.remove();
    }
  }

  function capturePjaxState() {
    return {
      authModalState: getAuthModalState(),
      preservedAPlayer: preserveAPlayer()
    };
  }

  function detachPreservedPjaxState(state) {
    var preservedAPlayer = state && state.preservedAPlayer;
    if (preservedAPlayer && preservedAPlayer.parentNode) {
      preservedAPlayer.parentNode.removeChild(preservedAPlayer);
    }
  }

  function replacePjaxDom(nextDoc) {
    hideHeatmapTooltip();
    replaceElement('#main-nav', nextDoc);
    replaceElement('#sub-nav', nextDoc);
    replaceElement('#header > picture', nextDoc);
    replaceElement('#header > img:first-of-type', nextDoc);
    replaceElement('#header-title', nextDoc);
    replaceElement('#subtitle-wrap', nextDoc);
    replaceElement('#i18n-nav', nextDoc);
    replaceElement('#loader', nextDoc);
    replaceElement('#content', nextDoc, { classOnly: true });
    replaceElement('#main', nextDoc);
    replaceElement('#sidebar', nextDoc, { classOnly: true, keepMissing: true });
    replaceElement('.sidebar-wrapper-container', nextDoc);
    replaceElement('.sidebar-widget', nextDoc);
    replaceElement('#mobile-nav', nextDoc, { appendTo: qs('#container') || document.body });
    replaceElement('.site-search', nextDoc, { appendTo: qs('#container') || document.body });
    replaceElement('footer#footer', nextDoc);
    replaceElement('#reimu-login-modal', nextDoc, { appendTo: qs('#container') || document.body });
    replaceElement('#reimu-profile-modal', nextDoc, { appendTo: qs('#container') || document.body, keepMissing: true });

    if (!qs('#main')) {
      var currentWrap = qs('#wrap');
      var nextWrap = qs('#wrap', nextDoc);
      if (currentWrap && nextWrap) {
        currentWrap.replaceWith(nextWrap.cloneNode(true));
      }
    }

    [
      '#mask'
    ].forEach(function (selector) {
      var current = qs(selector);
      var next = qs(selector, nextDoc);
      if (current && next) {
        current.replaceWith(next.cloneNode(true));
      }
    });
  }

  function syncPjaxDocumentState(nextDoc) {
    document.body.className = nextDoc.body.className;
    document.body.classList.remove('mobile-nav-on');
    document.body.classList.add('reimu-page-loading');
    document.body.setAttribute('aria-busy', 'true');
    syncHeadMetadata(nextDoc);
    syncInlineConfig(nextDoc);
  }

  function restorePjaxState(state) {
    if (state && state.preservedAPlayer) {
      placeAPlayerInSlot(state.preservedAPlayer);
    }
    restoreAuthModalState(state ? state.authModalState : null);
  }

  function verifyPjaxPostNavigation() {
    initCommentsProfileRuntime();
    if (window.ReimuCommentsRuntime && typeof window.ReimuCommentsRuntime.syncConfig === 'function') {
      window.ReimuCommentsRuntime.syncConfig();
    }
  }

  function replacePageContent(nextDoc) {
    var pjaxState = capturePjaxState();
    detachPreservedPjaxState(pjaxState);
    replacePjaxDom(nextDoc);
    syncPjaxDocumentState(nextDoc);
    restorePjaxState(pjaxState);
  }

  function navigateTo(url, options) {
    options = options || {};
    if (!config.pjax || !window.fetch || !window.DOMParser || !window.history || !window.history.pushState) {
      window.location.href = url;
      return Promise.resolve(false);
    }

    var target;
    try {
      target = new URL(url, window.location.href);
    } catch (error) {
      window.location.href = url;
      return Promise.resolve(false);
    }

    if (target.origin !== window.location.origin || isAssetPath(target.pathname)) {
      window.location.href = target.href;
      return Promise.resolve(false);
    }

    if (isSamePageHashUrl(target)) {
      scrollToHash(target.hash);
      if (!options.popstate && target.href !== window.location.href) {
        try {
          window.history.pushState({ reimuPjax: true }, document.title, target.href);
        } catch (error) {}
      }
      return Promise.resolve(false);
    }

    if (pjaxController) {
      pjaxController.abort();
    }
    pjaxController = 'AbortController' in window ? new AbortController() : null;
    pjaxRequestId += 1;
    var requestId = pjaxRequestId;

    captureAPlayerState();
    showLoader(target);
    dispatchReimuEvent('reimu:before-navigate', { url: target.href });

    return fetch(target.href, {
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html,application/xhtml+xml'
      },
      signal: pjaxController ? pjaxController.signal : undefined
    }).then(function (response) {
      var type = response.headers.get('content-type') || '';
      if (!response.ok || type.indexOf('text/html') === -1) {
        throw new Error('Invalid PJAX response');
      }
      return response.text();
    }).then(function (html) {
      if (requestId !== pjaxRequestId) {
        return false;
      }
      var nextDoc = new DOMParser().parseFromString(html, 'text/html');
      if (!qs('#wrap', nextDoc)) {
        throw new Error('PJAX root not found');
      }
      captureAPlayerState();
      replacePageContent(nextDoc);
      replayPjaxScripts(nextDoc);
      document.title = nextDoc.title || document.title;
      if (!options.popstate) {
        window.history.pushState({ reimuPjax: true }, document.title, target.href);
      }
      if (target.hash) {
        var heading = getHeadingFromHash(target.hash);
        if (heading) {
          scrollHeadingIntoView(heading, 'auto');
        }
      } else {
        window.scrollTo({ top: 0, behavior: 'auto' });
      }
      initReimu();
      mountAPlayerInFlow();
      verifyPjaxPostNavigation();
      hideLoader(260);
      dispatchReimuEvent('reimu:after-navigate', { url: target.href });
      return true;
    }).catch(function (error) {
      if (error && error.name === 'AbortError') {
        return false;
      }
      dispatchReimuEvent('reimu:navigate-error', { url: target.href, error: error });
      window.location.href = target.href;
      return false;
    });
  }

  function initPjax() {
    if (!config.pjax || root.dataset.pjaxReady) {
      return;
    }
    root.dataset.pjaxReady = 'true';
    if (!window.history.state) {
      try {
        window.history.replaceState({ reimuPjax: true }, document.title, window.location.href);
      } catch (error) {}
    }
    document.addEventListener('click', function (event) {
      var anchor = event.target.closest ? event.target.closest('a[href]') : null;
      if (anchor && anchor.href) {
        var url;
        try {
          url = new URL(anchor.href, window.location.href);
        } catch (error) {
          url = null;
        }
        if (isSamePageHashUrl(url)) {
          event.preventDefault();
          scrollToHash(url.hash);
          if (url.href !== window.location.href) {
            try {
              window.history.pushState({ reimuPjax: true }, document.title, url.href);
            } catch (error) {}
          }
          return;
        }
      }
      if (!shouldPjaxLink(anchor, event)) {
        return;
      }
      event.preventDefault();
      navigateTo(anchor.href);
    }, true);
    window.addEventListener('popstate', function () {
      navigateTo(window.location.href, { popstate: true });
    });
  }

  function initCommentsProfileRuntime() {
    return runCommentsRuntime('init');
  }

  function initReimu() {
    body = document.body;
    [
      initTheme,
      initLoader,
      initLazyload,
      initAos,
      initNav,
      initLanguageDropdown,
      initSidebarActive,
      initCodeCopy,
      initYmlEditors,
      initHeatmap,
      initArticleAnchors,
      initToc,
      initSearch,
      initFirework,
      initExternalIntegrations,
      initCommentsProfileRuntime,
      initSponsor,
      initShare,
      initExternalLinks,
      initPjax
    ].forEach(function (init) {
      try {
        init();
      } catch (error) {
        if (window.console && window.console.warn) {
          window.console.warn('[Yneko-Reimu] skipped init:', init.name, error);
        }
      }
    });
  }

  function destroyReimu() {
    if (pjaxController) {
      pjaxController.abort();
      pjaxController = null;
    }
    window.clearTimeout(loaderHideTimer);
    setPageLoading(false);
  }

  window.ReimuWP = {
    init: initReimu,
    destroy: destroyReimu,
    navigate: navigateTo,
    showLoader: showLoader,
    hideLoader: hideLoader,
    showTooltip: showTooltip,
    revealViewportAos: revealViewportAos,
    confirm: requestThemeConfirm
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReimu);
  } else {
    initReimu();
  }
}());
