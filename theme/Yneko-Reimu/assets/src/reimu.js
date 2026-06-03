import { createCore } from './reimu/core.js';
import { createSearchModule } from './reimu/search.js';
import { createShareModule } from './reimu/share.js';

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
  var searchModule = createSearchModule({
    config: config,
    t: t,
    qs: qs,
    qsa: qsa,
    escapeHtml: escapeHtml,
    debounce: debounce,
    dispatchReimuEvent: dispatchReimuEvent,
    getBody: function () { return body; },
    setBody: function (value) { body = value; }
  });
  var shareModule = createShareModule({
    qs: qs,
    qsa: qsa,
    getAssetBaseUrl: getAssetBaseUrl
  });
  var initSearch = searchModule.initSearch;
  var initShare = shareModule.initShare;

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

  function preparePhotoSwipeImages() {
    qsa('.article-entry img').forEach(function (img) {
      if (img.dataset.reimuPhotoswipeReady || img.closest('a')) {
        return;
      }
      var src = img.currentSrc || img.getAttribute('src') || img.getAttribute('data-src');
      if (!src || /^data:/i.test(src)) {
        return;
      }
      var link = document.createElement('a');
      link.className = 'reimu-photoswipe-item';
      link.href = src;
      link.setAttribute('target', '_blank');
      link.setAttribute('rel', 'noopener');
      link.setAttribute('data-pswp-src', src);
      link.setAttribute('data-no-pjax', '');
      if (img.naturalWidth && img.naturalHeight) {
        link.setAttribute('data-pswp-width', img.naturalWidth);
        link.setAttribute('data-pswp-height', img.naturalHeight);
      }
      img.dataset.reimuPhotoswipeReady = 'true';
      img.parentNode.insertBefore(link, img);
      link.appendChild(img);
    });

    qsa('.article-entry a[href] > img').forEach(function (img) {
      var link = img.closest('a[href]');
      if (!link || link.dataset.reimuPhotoswipeReady) {
        return;
      }
      var href = link.getAttribute('href') || '';
      if (!/\.(?:avif|gif|jpe?g|png|webp)(?:[?#].*)?$/i.test(href)) {
        return;
      }
      link.classList.add('reimu-photoswipe-item');
      link.setAttribute('data-pswp-src', href);
      link.setAttribute('data-no-pjax', '');
      if (img.naturalWidth && img.naturalHeight) {
        link.setAttribute('data-pswp-width', img.naturalWidth);
        link.setAttribute('data-pswp-height', img.naturalHeight);
      }
      link.dataset.reimuPhotoswipeReady = 'true';
    });

    qsa('.reimu-photoswipe-item').forEach(function (link) {
      if (link.dataset.reimuPhotoswipeClickReady) {
        return;
      }
      link.dataset.reimuPhotoswipeClickReady = 'true';
      link.addEventListener('click', function (event) {
        var items = qsa('.reimu-photoswipe-item').map(function (item) {
          var image = qs('img', item);
          var src = item.getAttribute('data-pswp-src') || item.getAttribute('href') || (image && (image.currentSrc || image.src)) || '';
          var width = Number(item.getAttribute('data-pswp-width') || (image && image.naturalWidth) || 1600);
          var height = Number(item.getAttribute('data-pswp-height') || (image && image.naturalHeight) || 1000);
          return {
            src: src,
            width: width,
            height: height,
            alt: image ? image.getAttribute('alt') || '' : ''
          };
        }).filter(function (item) {
          return item.src;
        });
        var index = qsa('.reimu-photoswipe-item').indexOf(link);
        if (!items.length || index < 0) {
          return;
        }
        event.preventDefault();
        openPhotoSwipeOverlay(items, index);
      });
    });
  }

  function openPhotoSwipeOverlay(items, index) {
    closePhotoSwipeOverlay();
    var current = Math.max(0, Math.min(index, items.length - 1));
    var overlay = document.createElement('div');
    overlay.className = 'reimu-photoswipe-overlay pswp pswp--open';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', t('imagePreview', '图片预览'));
    overlay.innerHTML = '<button type="button" class="reimu-photoswipe-close popup-btn-close" aria-label="' + escapeHtml(t('closePreview', '关闭预览')) + '"></button><button type="button" class="reimu-photoswipe-nav reimu-photoswipe-prev" aria-label="' + escapeHtml(t('previousImage', '上一张图片')) + '">‹</button><figure class="reimu-photoswipe-stage"><img alt=""><figcaption></figcaption></figure><button type="button" class="reimu-photoswipe-nav reimu-photoswipe-next" aria-label="' + escapeHtml(t('nextImage', '下一张图片')) + '">›</button>';
    document.body.appendChild(overlay);
    var image = qs('img', overlay);
    var caption = qs('figcaption', overlay);
    var prev = qs('.reimu-photoswipe-prev', overlay);
    var next = qs('.reimu-photoswipe-next', overlay);

    function render() {
      var item = items[current];
      image.src = item.src;
      image.alt = item.alt || '';
      caption.textContent = item.alt || '';
      caption.hidden = !item.alt;
      prev.hidden = items.length < 2;
      next.hidden = items.length < 2;
    }

    function move(step) {
      current = (current + step + items.length) % items.length;
      render();
    }

    overlay.addEventListener('click', function (event) {
      if (event.target === overlay || event.target.closest('.reimu-photoswipe-close')) {
        closePhotoSwipeOverlay();
      } else if (event.target.closest('.reimu-photoswipe-prev')) {
        move(-1);
      } else if (event.target.closest('.reimu-photoswipe-next')) {
        move(1);
      }
    });
    overlay.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closePhotoSwipeOverlay();
      } else if (event.key === 'ArrowLeft') {
        move(-1);
      } else if (event.key === 'ArrowRight') {
        move(1);
      }
    });
    render();
    window.REIMU_PHOTOSWIPE = { destroy: closePhotoSwipeOverlay };
    document.body.classList.add('reimu-photoswipe-on');
    overlay.tabIndex = -1;
    overlay.focus();
  }

  function closePhotoSwipeOverlay() {
    var overlay = qs('.reimu-photoswipe-overlay');
    if (overlay && overlay.parentNode) {
      overlay.parentNode.removeChild(overlay);
    }
    if (document.body) {
      document.body.classList.remove('reimu-photoswipe-on');
    }
    window.REIMU_PHOTOSWIPE = null;
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
      if (window.REIMU_PHOTOSWIPE && typeof window.REIMU_PHOTOSWIPE.destroy === 'function') {
        try {
          window.REIMU_PHOTOSWIPE.destroy();
        } catch (error) {}
      }
      window.REIMU_PHOTOSWIPE = null;
      preparePhotoSwipeImages();
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

  function initCommentSelector() {
    var selectors = qsa('#comments .selector-item[data-selector]');
    if (!selectors.length) {
      return;
    }
    selectors.forEach(function (selector, index) {
      if (selector.dataset.commentReady) {
        return;
      }
      selector.dataset.commentReady = 'true';
      selector.addEventListener('click', function () {
        selectors.forEach(function (item) { item.classList.remove('active'); });
        qsa('.comment-panel').forEach(function (panel) { panel.classList.remove('active'); });
        selector.classList.add('active');
        var panel = qs('#comment-panel-' + selector.getAttribute('data-selector'));
        if (panel) {
          panel.classList.add('active');
        }
      });
      if (index === 0) {
        selector.click();
      }
    });
  }

  function insertIntoTextarea(textarea, text) {
    if (!textarea || !text) {
      return;
    }
    var value = textarea.value || '';
    var start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : value.length;
    var end = typeof textarea.selectionEnd === 'number' ? textarea.selectionEnd : start;
    var before = value.slice(0, start);
    var after = value.slice(end);
    textarea.value = before + text + after;
    var position = start + text.length;
    textarea.focus();
    textarea.setSelectionRange(position, position);
    dispatchInputEvent(textarea);
  }

  function commentMediaStore(textarea) {
    if (!textarea._reimuCommentMedia) {
      textarea._reimuCommentMedia = {
        index: 0,
        items: {}
      };
    }
    return textarea._reimuCommentMedia;
  }

  function commentMediaToken(textarea, url, type, options) {
    var store = commentMediaStore(textarea);
    store.index += 1;
    var kind = type === 'gif' ? 'GIF' : 'IMAGE';
    var token = '[' + kind + ':' + store.index + ']';
    store.items[token] = {
      url: url,
      type: type === 'gif' ? 'gif' : 'image',
      cleanupKey: options && options.cleanupKey ? String(options.cleanupKey) : '',
      uploaded: !!(options && options.uploaded)
    };
    return token;
  }

  function resolveCommentMediaTokens(value, textarea) {
    var store = textarea && textarea._reimuCommentMedia ? textarea._reimuCommentMedia.items : {};
    return String(value || '').replace(/\[(GIF|IMAGE):(\d+)\]/g, function (token, kind) {
      var item = store[token];
      if (!item || !item.url) {
        return token;
      }
      return '![' + (kind === 'GIF' ? 'GIF' : 'image') + '](' + item.url + ')';
    });
  }

  function commentMediaRegex() {
    return /\[(GIF|IMAGE):(\d+)\]|!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/gi;
  }

  function commentMediaEntries(value) {
    var entries = [];
    String(value || '').replace(commentMediaRegex(), function (match, tokenKind, tokenIndex, alt, url, offset) {
      var type = tokenKind ? String(tokenKind).toLowerCase() : (/gif/i.test(alt || '') || /\.gif(?:[?#]|$)/i.test(url || '') ? 'gif' : 'image');
      entries.push({
        text: match,
        type: type === 'gif' ? 'gif' : 'image',
        offset: offset,
        length: match.length
      });
      return match;
    });
    return entries;
  }

  function cleanupUnsubmittedCommentMedia(item) {
    if (!item || !item.uploaded || !item.cleanupKey || !item.url || !config.login || !config.login.ajaxUrl) {
      return;
    }
    var uploads = config.commentUploads || {};
    if (!uploads.nonce) {
      return;
    }
    var formData = new FormData();
    formData.append('action', 'yneko_reimu_comment_upload_discard');
    formData.append('nonce', uploads.nonce || '');
    formData.append('url', item.url || '');
    formData.append('cleanup_key', item.cleanupKey || '');
    fetch(config.login.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    }).catch(function () {});
  }

  function commentMediaReplaceMessage(entries) {
    var hasGif = entries.some(function (entry) {
      return entry.type === 'gif';
    });
    var hasImage = entries.some(function (entry) {
      return entry.type === 'image';
    });
    if (hasGif && !hasImage) {
      return t('commentMediaReplaceGifConfirm', '是否清空当前表情并重新添加？');
    }
    if (hasImage && !hasGif) {
      return t('commentMediaReplaceImageConfirm', '是否清空当前图片并重新添加？');
    }
    return t('commentMediaReplaceAllConfirm', '是否清空当前图片和表情并重新添加？');
  }

  function removeCommentMediaFromTextarea(textarea) {
    if (!textarea) {
      return;
    }
    var value = textarea.value || '';
    var entries = commentMediaEntries(value);
    if (!entries.length) {
      return;
    }
    var start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : value.length;
    var store = textarea._reimuCommentMedia ? textarea._reimuCommentMedia.items : null;
    if (store) {
      entries.forEach(function (entry) {
        if (/^\[(?:GIF|IMAGE):\d+\]$/i.test(entry.text)) {
          cleanupUnsubmittedCommentMedia(store[entry.text]);
          delete store[entry.text];
        }
      });
    }
    var cleaned = value
      .replace(commentMediaRegex(), '\n')
      .replace(/[ \t]*\n[ \t]*/g, '\n')
      .replace(/\n{3,}/g, '\n\n')
      .trim();
    textarea.value = cleaned;
    if (textarea.setSelectionRange) {
      var position = Math.min(start, cleaned.length);
      textarea.setSelectionRange(position, position);
    }
    dispatchInputEvent(textarea);
  }

  function confirmCommentMediaReplace(textarea) {
    var entries = commentMediaEntries(textarea ? textarea.value : '');
    return !entries.length || window.confirm(commentMediaReplaceMessage(entries));
  }

  function prepareCommentMediaInsert(textarea, confirmedReplace) {
    var entries = commentMediaEntries(textarea ? textarea.value : '');
    if (!entries.length) {
      return true;
    }
    if (!confirmedReplace && !window.confirm(commentMediaReplaceMessage(entries))) {
      return false;
    }
    removeCommentMediaFromTextarea(textarea);
    return true;
  }

  function commentMediaLimitOk(value) {
    return commentMediaEntries(value).length <= 1;
  }

  function commentTextForCount(value) {
    return String(value || '')
      .replace(/\[(?:GIF|IMAGE):\d+\]/g, '')
      .replace(/!\[[^\]]*\]\((?:https?:)?\/\/[^)\s]+\)/gi, '')
      .replace(/\[[^\]]+\]\((?:https?:)?\/\/[^)\s]+\)/gi, function (_, label) {
        return label || '';
      })
      .trim();
  }

  function markdownToHtml(text) {
    var blocks = [];
    var source = String(text || '').replace(/\r\n?/g, '\n').replace(/```\s*(?:[a-z0-9_-]+)?[^\n]*\n?([\s\S]*?)```/gi, function (_, code) {
      var key = '%%REIMU_COMMENT_CODE_' + blocks.length + '%%';
      blocks.push({
        key: key,
        html: '<pre><code>' + escapeHtml(String(code || '').replace(/\n$/, '')) + '</code></pre>'
      });
      return '\n' + key + '\n';
    });

    var html = escapeHtml(source)
      .replace(/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/gi, function (_, alt, url) {
        return '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(alt) + '" loading="lazy" decoding="async">';
      })
      .replace(/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/gi, function (_, label, url) {
        return '<a href="' + escapeHtml(url) + '" rel="nofollow noopener noreferrer" target="_blank">' + escapeHtml(label) + '</a>';
      })
      .replace(/`([^`]+)`/g, '<code>$1</code>')
      .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

    html = html.split(/\n{2,}/).map(function (block) {
      var trimmed = block.trim();
      if (!trimmed) {
        return '';
      }
      if (/^%%REIMU_COMMENT_CODE_\d+%%$/.test(trimmed)) {
        return trimmed;
      }
      return '<p>' + trimmed.replace(/\n/g, '<br>') + '</p>';
    }).join('');

    blocks.forEach(function (block) {
      html = html.replace(block.key, block.html);
    });

    return html || '<p class="reimu-comment-preview-empty">' + escapeHtml(t('commentPreviewEmpty', '还没有内容。')) + '</p>';
  }

  function closeCommentPopovers(form, except) {
    qsa('.reimu-comment-popover', form).forEach(function (popover) {
      if (popover !== except) {
        popover.hidden = true;
      }
    });
    qsa('[data-comment-tool]', form).forEach(function (button) {
      if (!except || button.getAttribute('data-comment-tool') !== except.getAttribute('data-comment-popover')) {
        button.classList.remove('active');
        button.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function setCommentToolState(form, name, active) {
    var button = qs('[data-comment-tool="' + name + '"]', form);
    if (button) {
      button.classList.toggle('active', !!active);
      button.setAttribute('aria-expanded', active ? 'true' : 'false');
    }
  }

  function toggleCommentPopover(form, name) {
    var popover = qs('[data-comment-popover="' + name + '"]', form);
    var button = qs('[data-comment-tool="' + name + '"]', form);
    if (!popover || !button) {
      return;
    }
    var shouldOpen = popover.hidden;
    closeCommentPopovers(form, shouldOpen ? popover : null);
    popover.hidden = !shouldOpen;
    button.classList.toggle('active', shouldOpen);
    button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    if (shouldOpen) {
      var status = qs('[data-comment-upload-status="' + name + '"]', form);
      if (status) {
        status.textContent = '';
      }
    }
  }

  function initCommentPopoverOutsideClose() {
    if (document.documentElement.dataset.commentPopoverOutsideReady) {
      return;
    }
    document.documentElement.dataset.commentPopoverOutsideReady = 'true';
    document.addEventListener('click', function (event) {
      var target = event.target;
      if (!target || !target.closest) {
        return;
      }
      if (target.closest('.reimu-comment-popover') || target.closest('[data-comment-tool]')) {
        return;
      }
      qsa('.reimu-comment-form').forEach(function (form) {
        closeCommentPopovers(form);
      });
    });
  }

  function updateCommentPreview(form, textarea) {
    var preview = qs('[data-comment-preview-panel] .reimu-comment-preview-content', form);
    if (preview) {
      preview.innerHTML = markdownToHtml(resolveCommentMediaTokens(textarea.value, textarea));
    }
  }

  function insertCommentMedia(textarea, url, type, options) {
    if (!prepareCommentMediaInsert(textarea, options && options.confirmedReplace)) {
      return false;
    }
    insertIntoTextarea(textarea, commentMediaToken(textarea, url, type, options || {}));
    return true;
  }

  function initCommentGifLibrary(form, textarea) {
    var library = qs('[data-comment-gif-library]', form);
    var uploads = config.commentUploads || {};
    var gifs = Array.isArray(uploads.gifs) ? uploads.gifs : [];
    if (!library || library.dataset.gifLibraryReady) {
      return;
    }
    library.dataset.gifLibraryReady = 'true';

    if (!gifs.length) {
      library.innerHTML = '<p class="reimu-comment-gif-empty">' + escapeHtml(t('commentGifEmpty', '暂无可选 GIF。')) + '</p>';
      return;
    }

    library.innerHTML = gifs.map(function (item) {
      return '<button type="button" class="reimu-comment-gif-item" data-comment-gif-url="' + escapeHtml(item.url || '') + '" title="' + escapeHtml(item.title || 'GIF') + '"><img src="' + escapeHtml(item.url || '') + '" alt="GIF" loading="lazy" decoding="async"></button>';
    }).join('');

    qsa('[data-comment-gif-url]', library).forEach(function (button) {
      button.addEventListener('click', function () {
        var url = button.getAttribute('data-comment-gif-url') || '';
        if (url) {
          if (insertCommentMedia(textarea, url, 'gif')) {
            closeCommentPopovers(form);
          }
        }
      });
    });
  }

  function initCommentUploadRows(form, textarea) {
    function uploadState(type) {
      var uploads = config.commentUploads || {};
      var enabledKey = type === 'gif' ? 'gifEnabled' : 'imageEnabled';
      return {
        uploads: uploads,
        enabled: !!(uploads.enabled && uploads[enabledKey]),
        canUpload: !!(uploads.enabled && uploads[enabledKey] && uploads.isLoggedIn && uploads.nonce && config.login && config.login.ajaxUrl)
      };
    }

    qsa('[data-comment-upload-row]', form).forEach(function (row) {
      var type = row.getAttribute('data-comment-upload-row') === 'gif' ? 'gif' : 'image';
      row.hidden = !uploadState(type).canUpload;
    });
    qsa('[data-comment-upload-login]', form).forEach(function (note) {
      var type = note.getAttribute('data-comment-upload-login') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      note.hidden = state.canUpload || !state.enabled;
      note.textContent = state.enabled ? t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。') : t(type === 'gif' ? 'commentUploadGifDisabled' : 'commentUploadImageDisabled', type === 'gif' ? '评论 GIF 上传已关闭。' : '评论图片上传已关闭。');
    });
    qsa('[data-comment-upload-button]', form).forEach(function (button) {
      var type = button.getAttribute('data-comment-upload-button') === 'gif' ? 'gif' : 'image';
      button.hidden = !uploadState(type).enabled;
    });
    qsa('[data-comment-upload-input]', form).forEach(function (input) {
      var type = input.getAttribute('data-comment-upload-input') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      input.disabled = !state.canUpload;
      if (!state.canUpload) {
        input.value = '';
      }
    });
    qsa('[data-comment-upload-status]', form).forEach(function (status) {
      var type = status.getAttribute('data-comment-upload-status') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      if (!state.enabled || state.canUpload) {
        status.textContent = '';
      }
    });

    qsa('[data-comment-upload-button]', form).forEach(function (button) {
      if (button.dataset.commentUploadReady) {
        return;
      }
      button.dataset.commentUploadReady = 'true';
      var type = button.getAttribute('data-comment-upload-button') === 'gif' ? 'gif' : 'image';
      var input = qs('[data-comment-upload-input="' + type + '"]', form);
      if (input && !input.dataset.commentUploadInputReady) {
        input.dataset.commentUploadInputReady = 'true';
        input.addEventListener('change', function () {
          if (input.files && input.files[0]) {
            if (!uploadState(type).canUpload) {
              input.value = '';
              showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
              initCommentUploadRows(form, textarea);
              return;
            }
            var replaceConfirmed = false;
            if (commentMediaEntries(textarea ? textarea.value : '').length) {
              if (!confirmCommentMediaReplace(textarea)) {
                input.value = '';
                return;
              }
              replaceConfirmed = true;
            }
            uploadCommentFile(type, input, button, textarea, form, replaceConfirmed);
          }
        });
      }
      button.addEventListener('click', function () {
        var state = uploadState(type);
        if (!state.enabled) {
          showTooltip(t(type === 'gif' ? 'commentUploadGifDisabled' : 'commentUploadImageDisabled', type === 'gif' ? '评论 GIF 上传已关闭。' : '评论图片上传已关闭。'));
          initCommentUploadRows(form, textarea);
          return;
        }
        if (!state.canUpload) {
          showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
          initCommentUploadRows(form, textarea);
          return;
        }
        if (input) {
          input.click();
        }
      });
    });

    function uploadCommentFile(type, input, button, textarea, form, replaceConfirmed) {
      var state = uploadState(type);
      if (!state.canUpload) {
        showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
        return;
      }

      var status = qs('[data-comment-upload-status="' + type + '"]', form);
      if (!input || !input.files || !input.files[0]) {
        showTooltip(t('commentUploadChoose', '请先选择文件。'));
        return;
      }

      var formData = new FormData();
      formData.append('action', 'yneko_reimu_comment_upload');
      formData.append('nonce', state.uploads.nonce || '');
      formData.append('type', type);
      formData.append('file', input.files[0]);
      button.disabled = true;
      if (status) {
        status.textContent = t('commentUploadUploading', '上传中...');
      }

      fetch(config.login.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      }).then(function (response) {
        return response.json().catch(function () {
          return { success: false, data: { message: t('commentUploadFailed', '上传失败。') } };
        });
      }).then(function (payload) {
        if (!payload || !payload.success || !payload.data || !payload.data.url) {
          var message = payload && payload.data && payload.data.message ? payload.data.message : t('commentUploadFailed', '上传失败。');
          if (status) {
            status.textContent = message;
          }
          showTooltip(message);
          return;
        }
        insertCommentMedia(textarea, payload.data.url, payload.data.type || type, {
          confirmedReplace: !!replaceConfirmed,
          cleanupKey: payload.data.cleanupKey || '',
          uploaded: !!payload.data.cleanupKey
        });
        input.value = '';
        if (status) {
          status.textContent = Object.prototype.hasOwnProperty.call(payload.data, 'message') ? payload.data.message : t('commentUploadDone', '已插入评论。');
        }
        if (payload.data.requiresReview && payload.data.message) {
          showTooltip(payload.data.message);
        }
      }).catch(function () {
        if (status) {
          status.textContent = t('commentUploadFailed', '上传失败。');
        }
        showTooltip(t('commentUploadFailed', '上传失败。'));
      }).finally(function () {
        button.disabled = false;
      });
    }
  }

  function initCommentTools(form) {
    var textarea = qs('textarea[name="comment"]', form);
    if (!textarea) {
      return;
    }

    qsa('[data-comment-tool]', form).forEach(function (button) {
      if (button.dataset.commentToolReady) {
        return;
      }
      button.dataset.commentToolReady = 'true';
      button.setAttribute('aria-expanded', 'false');
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var tool = button.getAttribute('data-comment-tool');
        if (tool === 'preview') {
          var panel = qs('[data-comment-preview-panel]', form);
          var shouldOpen = panel ? panel.hidden : false;
          if (panel) {
            panel.hidden = !shouldOpen;
            panel.classList.toggle('is-open', shouldOpen);
          }
          updateCommentPreview(form, textarea);
          closeCommentPopovers(form);
          setCommentToolState(form, 'preview', shouldOpen);
          return;
        }
        toggleCommentPopover(form, tool);
        var input = qs('[data-comment-popover="' + tool + '"] input', form);
        if (input && !input.hidden) {
          window.setTimeout(function () {
            input.focus();
          }, 0);
        }
      });
    });

    initCommentGifLibrary(form, textarea);
    initCommentUploadRows(form, textarea);
    initCommentPopoverOutsideClose();
    textarea.addEventListener('input', function () {
      var preview = qs('[data-comment-preview-panel]', form);
      if (preview && !preview.hidden) {
        updateCommentPreview(form, textarea);
      }
    });

    qsa('[data-comment-insert]', form).forEach(function (button) {
      if (button.dataset.commentInsertReady) {
        return;
      }
      button.dataset.commentInsertReady = 'true';
      button.addEventListener('click', function () {
        insertIntoTextarea(textarea, button.getAttribute('data-comment-insert') || '');
        closeCommentPopovers(form);
      });
    });

    qsa('[data-comment-url-insert]', form).forEach(function (button) {
      if (button.dataset.commentUrlReady) {
        return;
      }
      button.dataset.commentUrlReady = 'true';
      button.addEventListener('click', function () {
        var type = button.getAttribute('data-comment-url-insert');
        var input = qs('[data-comment-url-input="' + type + '"]', form);
        var url = input ? String(input.value || '').trim() : '';
        if (!/^https?:\/\//i.test(url)) {
          showTooltip(t('invalidImageUrl', '请输入 http(s) 图片地址'));
          if (input) {
            input.focus();
          }
          return;
        }
        if (insertCommentMedia(textarea, url, type)) {
          if (input) {
            input.value = '';
          }
          closeCommentPopovers(form);
        }
      });
    });

    textarea.addEventListener('input', function () {
      var preview = qs('[data-comment-popover="preview"]:not([hidden]) .reimu-comment-preview-content', form);
      if (preview) {
        preview.innerHTML = markdownToHtml(resolveCommentMediaTokens(textarea.value, textarea));
      }
    });
  }

  function commentHotScore(item) {
    if (!item) {
      return 0;
    }
    var parentLikes = Number(item.dataset.commentLikes || 0);
    var replyItems = qsa('.children .reimu-comment', item);
    var replyLikes = 0;
    var latestTime = Number(item.dataset.commentTime || 0);
    replyItems.forEach(function (reply) {
      replyLikes += Number(reply.dataset.commentLikes || 0);
      latestTime = Math.max(latestTime, Number(reply.dataset.commentTime || 0));
    });
    var activeTime = latestTime > 0 ? latestTime : Number(item.dataset.commentTime || 0);
    var ageHours = activeTime > 0 ? Math.max(0, (Date.now() / 1000 - activeTime) / 3600) : 0;
    var baseScore = parentLikes * 4 + replyLikes * 2 + replyItems.length * 3 + 1;
    return baseScore / Math.pow(1 + ageHours / 72, 0.35);
  }

  function commentLatestActivityTime(item) {
    if (!item) {
      return 0;
    }
    var latestTime = Number(item.dataset.commentTime || 0);
    qsa('.children .reimu-comment', item).forEach(function (reply) {
      latestTime = Math.max(latestTime, Number(reply.dataset.commentTime || 0));
    });
    return latestTime;
  }

  function getLoadMoreItems(rootElement) {
    if (!rootElement) {
      return [];
    }
    return qsa(':scope > [data-reimu-loadmore-item]', rootElement).concat(qsa(':scope > .reimu-comment', rootElement));
  }

  function syncLoadMoreRoot(rootElement) {
    if (!rootElement) {
      return;
    }
    var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
    var visible = Math.max(batch, Number(rootElement.getAttribute('data-reimu-loadmore-visible') || batch));
    var items = getLoadMoreItems(rootElement);
    var targetId = rootElement.id ? '#' + rootElement.id : '';
    var button = targetId ? qs('[data-reimu-loadmore-target="' + targetId + '"]') : null;
    var buttonWrap = button ? button.closest('.reimu-load-more-wrap') : null;
    items.forEach(function (item, index) {
      var hidden = index >= visible;
      item.hidden = hidden;
      item.classList.toggle('reimu-loadmore-hidden', hidden);
    });
    if (button) {
      var hasMore = visible < items.length;
      button.hidden = !hasMore;
      button.disabled = !hasMore;
      button.classList.toggle('is-end', !hasMore);
      button.textContent = button.dataset.labelMore || t('loadMore', '加载更多...');
      button.setAttribute('aria-disabled', hasMore ? 'false' : 'true');
      if (buttonWrap) {
        buttonWrap.hidden = !hasMore;
      }
    }
  }

  function initLoadMore() {
    qsa('[data-reimu-loadmore-root]').forEach(function (rootElement) {
      var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
      if (!rootElement.getAttribute('data-reimu-loadmore-visible')) {
        rootElement.setAttribute('data-reimu-loadmore-visible', String(batch));
      }
      syncLoadMoreRoot(rootElement);
    });

    qsa('[data-reimu-loadmore-target]').forEach(function (button) {
      if (button.dataset.loadmoreReady) {
        return;
      }
      button.dataset.loadmoreReady = 'true';
      button.addEventListener('click', function () {
        if (button.disabled || button.classList.contains('is-end')) {
          return;
        }
        var rootElement = qs(button.getAttribute('data-reimu-loadmore-target'));
        if (!rootElement) {
          return;
        }
        var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
        var visible = Math.max(batch, Number(rootElement.getAttribute('data-reimu-loadmore-visible') || batch));
        rootElement.setAttribute('data-reimu-loadmore-visible', String(visible + batch));
        syncLoadMoreRoot(rootElement);
        revealViewportAos();
      });
    });
  }

  function sortCommentList(mode) {
    var list = qs('#comments .reimu-comment-list');
    if (!list) {
      return;
    }
    var items = qsa(':scope > .reimu-comment', list);
    items.sort(function (a, b) {
      if (mode === 'hot') {
        return commentHotScore(b) - commentHotScore(a) || commentLatestActivityTime(b) - commentLatestActivityTime(a) || Number(a.dataset.commentTime || 0) - Number(b.dataset.commentTime || 0);
      }
      if (mode === 'desc') {
        return Number(b.dataset.commentTime || 0) - Number(a.dataset.commentTime || 0);
      }
      return Number(a.dataset.commentTime || 0) - Number(b.dataset.commentTime || 0);
    });
    items.forEach(function (item) {
      list.appendChild(item);
    });
    syncLoadMoreRoot(list);
  }

  function getActiveCommentSortMode() {
    var active = qs('#comments [data-comment-sort].active');
    return active ? (active.getAttribute('data-comment-sort') || 'asc') : 'asc';
  }

  function updateCommentCount(count, label) {
    var title = qs('#comments .reimu-comment-count');
    if (!title || typeof count === 'undefined') {
      return;
    }
    title.textContent = label || (String(count) + ' ' + (config.language === 'en_US' ? 'Comments' : '评论'));
  }

  function clearCommentForm(form) {
    var textarea = qs('textarea[name="comment"]', form);
    if (textarea) {
      textarea.value = '';
      if (textarea._reimuCommentMedia) {
        textarea._reimuCommentMedia = {
          index: 0,
          items: {}
        };
      }
      dispatchInputEvent(textarea);
    }
    qsa('[data-comment-upload-status]', form).forEach(function (status) {
      status.textContent = '';
    });
    var preview = qs('[data-comment-preview-panel]', form);
    if (preview) {
      preview.hidden = true;
      preview.classList.remove('is-open');
    }
    setCommentToolState(form, 'preview', false);
    closeCommentPopovers(form);
  }

  function appendSubmittedComment(html, approved, parentId) {
    var list = qs('#reimu-comment-list');
    if (!list || !html) {
      return null;
    }
    var template = document.createElement('template');
    template.innerHTML = html.trim();
    var item = template.content.firstElementChild;
    if (!item) {
      return null;
    }
    if (!approved) {
      item.classList.add('reimu-comment-pending');
    }
    var parent = parentId ? qs('#comment-' + parentId) : null;
    if (parent) {
      var children = qs(':scope > .children', parent);
      if (!children) {
        children = document.createElement('ol');
        children.className = 'children';
        parent.appendChild(children);
      }
      children.appendChild(item);
    } else {
      list.hidden = false;
      list.appendChild(item);
    }
    var empty = qs('#comments .reimu-comment-empty');
    if (empty) {
      empty.hidden = true;
    }
    if (!parent) {
      sortCommentList(getActiveCommentSortMode());
    }
    initCommentLikes();
    initCommentOwnerActions();
    initWordPressCommentForm();
    syncLoadMoreRoot(list);
    return item;
  }

  function initAjaxCommentSubmit(form) {
    if (form.dataset.ajaxCommentReady) {
      return;
    }
    form.dataset.ajaxCommentReady = 'true';
    form.addEventListener('submit', function (event) {
      if (!config.login || !config.login.ajaxUrl || !config.comments || !config.comments.nonce) {
        return;
      }
      event.preventDefault();
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea && !textarea.value.trim()) {
        showTooltip(t('commentEmpty', '还没有评论，来抢一张小板凳吧。'));
        textarea.focus();
        return;
      }
      if (textarea && !commentMediaLimitOk(textarea.value)) {
        showTooltip(t('commentMediaLimitOne', '一条评论最多只能添加一张图片或一个 GIF。'));
        textarea.focus();
        return;
      }
      var submit = qs('.reimu-comment-submit', form) || qs('[type="submit"]', form);
      if (submit && submit.disabled) {
        return;
      }
      var originalText = submit ? submit.textContent : '';
      var formData = new FormData(form);
      if (textarea) {
        formData.set('comment', resolveCommentMediaTokens(textarea.value, textarea));
      }
      formData.append('action', 'yneko_reimu_submit_comment');
      formData.append('nonce', config.comments.nonce || '');
      if (submit) {
        submit.disabled = true;
        submit.classList.add('is-loading');
        submit.setAttribute('aria-busy', 'true');
        submit.setAttribute('data-original-text', originalText);
      }
      fetch(config.login.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      }).then(function (response) {
        var contentType = response.headers && response.headers.get ? response.headers.get('content-type') || '' : '';
        if (contentType.indexOf('application/json') === -1) {
          return response.text().then(function (text) {
            var message = String(text || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            return { success: false, data: { message: message || t('commentSubmitFailed', '评论提交失败。') } };
          });
        }
        return response.json().catch(function () {
          return { success: false, data: { message: t('commentSubmitFailed', '评论提交失败。') } };
        });
      }).then(function (payload) {
        if (!payload || !payload.success || !payload.data) {
          var message = payload && payload.data && payload.data.message ? payload.data.message : t('commentSubmitFailed', '评论提交失败。');
          showTooltip(message);
          return;
        }
        var item = appendSubmittedComment(payload.data.html || '', !!payload.data.approved, Number(payload.data.parent_id || 0));
        updateCommentCount(payload.data.count, payload.data.count_label);
        clearCommentForm(form);
        var liveRespond = qs('#respond');
        if (liveRespond && liveRespond.classList.contains('reimu-respond-inline')) {
          var cancel = qs('[data-reimu-cancel-reply]', liveRespond) || qs('#cancel-comment-reply-link');
          if (cancel) {
            cancel.click();
          }
        }
        showTooltip(payload.data.message || (!!payload.data.approved ? t('commentSubmitSuccess', '评论已发布。') : t('commentSubmitPending', '评论已提交，正在等待审核。')));
        if (!payload.data.approved) {
          var profileModal = qs('#reimu-profile-modal');
          if (profileModal && profileModal._reimuSetInlineProfileStatuses) {
            profileModal._reimuSetInlineProfileStatuses([{ text: t('commentsPending', '评论审核中'), state: 'pending', status: 'pending', type: 'comments', count: 1 }]);
          } else if (profileModal && profileModal._reimuSetInlineProfileStatus) {
            profileModal._reimuSetInlineProfileStatus(t('commentsPending', '评论审核中'), 'pending', 'comments', 1);
          }
          if (profileModal && profileModal._reimuStartProfileStatusPolling) {
            profileModal._reimuStartProfileStatusPolling();
          }
        }
        if (item && item.scrollIntoView) {
          window.setTimeout(function () {
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }, 80);
        }
      }).catch(function () {
        showTooltip(t('commentSubmitFailed', '评论提交失败。'));
      }).finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.classList.remove('is-loading');
          submit.removeAttribute('aria-busy');
          submit.textContent = submit.getAttribute('data-original-text') || originalText;
          submit.removeAttribute('data-original-text');
        }
      });
    });
  }

  function getLikedComments() {
    try {
      return JSON.parse(localStorage.getItem('reimu_comment_likes') || '{}') || {};
    } catch (error) {
      return {};
    }
  }

  function setLikedComments(likes) {
    try {
      localStorage.setItem('reimu_comment_likes', JSON.stringify(likes || {}));
    } catch (error) {}
  }

  function initCommentLikes() {
    var likes = getLikedComments();
    qsa('#comments [data-comment-like]').forEach(function (button) {
      var id = button.getAttribute('data-comment-like');
      if (!id) {
        return;
      }
      var commentItem = button.closest('.reimu-comment');
      var hasServerLikedState = commentItem && typeof commentItem.dataset.commentLiked !== 'undefined';
      var serverLiked = hasServerLikedState && commentItem.dataset.commentLiked === '1';
      var liked = hasServerLikedState ? serverLiked : !!likes[id];
      button.classList.toggle('liked', liked);
      button.setAttribute('aria-pressed', liked ? 'true' : 'false');
      if (serverLiked) {
        likes[id] = 1;
      } else if (hasServerLikedState) {
        delete likes[id];
      }
      if (button.dataset.commentLikeReady) {
        return;
      }
      button.dataset.commentLikeReady = 'true';
      button.addEventListener('click', function () {
        if (!config.login || !config.login.ajaxUrl || button.disabled) {
          return;
        }
        var currentLikes = getLikedComments();
        var currentLiked = !!currentLikes[id];
        var formData = new FormData();
        formData.append('action', 'yneko_reimu_comment_like');
        formData.append('comment_id', id);
        formData.append('liked', currentLiked ? '1' : '0');
        formData.append('nonce', button.getAttribute('data-like-nonce') || '');
        button.disabled = true;
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json();
        }).then(function (payload) {
          if (!payload || !payload.success || !payload.data) {
            return;
          }
          var nextLiked = !!payload.data.liked;
          var count = qs('[data-like-count]', button);
          if (count) {
            count.textContent = String(payload.data.count || 0);
          }
          var commentItem = button.closest('.reimu-comment');
          if (commentItem) {
            commentItem.dataset.commentLikes = String(payload.data.count || 0);
            commentItem.dataset.commentLiked = nextLiked ? '1' : '0';
          }
          currentLikes[id] = nextLiked ? 1 : undefined;
          if (!nextLiked) {
            delete currentLikes[id];
          }
          setLikedComments(currentLikes);
          button.classList.toggle('liked', nextLiked);
          button.setAttribute('aria-pressed', nextLiked ? 'true' : 'false');
          if (getActiveCommentSortMode() === 'hot') {
            sortCommentList('hot');
          }
        }).catch(function () {}).finally(function () {
          button.disabled = false;
        });
      });
    });
  }

  function resetCommentEdit(commentItem) {
    if (!commentItem) {
      return;
    }
    var form = qs('.reimu-comment-edit-form', commentItem);
    var text = qs('.comment-text', commentItem);
    if (form) {
      form.remove();
    }
    if (text) {
      text.hidden = false;
    }
  }

  function initCommentOwnerActions() {
    qsa('#comments [data-comment-edit]').forEach(function (button) {
      if (button.dataset.commentEditReady) {
        return;
      }
      button.dataset.commentEditReady = 'true';
      button.addEventListener('click', function () {
        var commentItem = button.closest('.reimu-comment');
        var commentId = button.getAttribute('data-comment-edit') || '';
        var text = commentItem ? qs('.comment-text', commentItem) : null;
        if (!commentItem || !commentId || !text || qs('.reimu-comment-edit-form', commentItem)) {
          return;
        }
        var raw = text.getAttribute('data-comment-raw') || text.textContent || '';
        text.hidden = true;
        var form = document.createElement('form');
        form.className = 'reimu-comment-edit-form';
        form.innerHTML = '<textarea class="reimu-comment-edit-textarea" rows="4"></textarea><div class="reimu-comment-edit-actions"><button type="submit" class="reimu-comment-edit-save">' + escapeHtml(t('commentEditSave', '保存')) + '</button><button type="button" class="reimu-comment-edit-cancel">' + escapeHtml(t('commentEditCancel', '取消')) + '</button></div>';
        var textarea = qs('textarea', form);
        if (textarea) {
          textarea.value = raw;
        }
        text.insertAdjacentElement('afterend', form);
        if (textarea && textarea.focus) {
          textarea.focus();
        }
        qs('.reimu-comment-edit-cancel', form).addEventListener('click', function () {
          resetCommentEdit(commentItem);
        });
        form.addEventListener('submit', function (event) {
          event.preventDefault();
          if (!config.login || !config.login.ajaxUrl || !textarea || button.disabled) {
            return;
          }
          var next = textarea.value.trim();
          if (!next) {
            showTooltip(t('commentEmpty', '还没有评论，来抢一张小板凳吧。'));
            textarea.focus();
            return;
          }
          if (!commentMediaLimitOk(next)) {
            showTooltip(t('commentMediaLimitOne', '一条评论最多只能添加一张图片或一个 GIF。'));
            textarea.focus();
            return;
          }
          var submit = qs('[type="submit"]', form);
          var formData = new FormData();
          formData.append('action', 'yneko_reimu_edit_comment');
          formData.append('comment_id', commentId);
          formData.append('comment', next);
          formData.append('nonce', button.getAttribute('data-comment-manage-nonce') || '');
          button.disabled = true;
          if (submit) {
            submit.disabled = true;
          }
          fetch(config.login.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          }).then(function (response) {
            return response.json().catch(function () {
              return { success: false, data: { message: t('commentEditFailed', '评论更新失败。') } };
            });
          }).then(function (payload) {
            if (!payload || !payload.success || !payload.data) {
              showTooltip(payload && payload.data && payload.data.message ? payload.data.message : t('commentEditFailed', '评论更新失败。'));
              return;
            }
            text.innerHTML = payload.data.html || '';
            text.setAttribute('data-comment-raw', payload.data.raw || next);
            resetCommentEdit(commentItem);
            showTooltip(payload.data.message || t('commentSubmitSuccess', '评论已发布。'));
          }).catch(function () {
            showTooltip(t('commentEditFailed', '评论更新失败。'));
          }).finally(function () {
            button.disabled = false;
            if (submit) {
              submit.disabled = false;
            }
          });
        });
      });
    });

    qsa('#comments [data-comment-delete]').forEach(function (button) {
      if (button.dataset.commentDeleteReady) {
        return;
      }
      button.dataset.commentDeleteReady = 'true';
      button.addEventListener('click', function () {
        var commentItem = button.closest('.reimu-comment');
        var commentId = button.getAttribute('data-comment-delete') || '';
        if (!commentItem || !commentId || button.disabled || !window.confirm(t('commentDeleteConfirm', '确定删除这条评论吗？'))) {
          return;
        }
        var formData = new FormData();
        formData.append('action', 'yneko_reimu_delete_comment');
        formData.append('comment_id', commentId);
        formData.append('nonce', button.getAttribute('data-comment-manage-nonce') || '');
        button.disabled = true;
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: t('commentDeleteFailed', '评论删除失败。') } };
          });
        }).then(function (payload) {
          if (!payload || !payload.success || !payload.data) {
            showTooltip(payload && payload.data && payload.data.message ? payload.data.message : t('commentDeleteFailed', '评论删除失败。'));
            return;
          }
          var parentList = commentItem.parentNode;
          commentItem.remove();
          updateCommentCount(payload.data.count, payload.data.count_label);
          if (parentList && parentList.classList && parentList.classList.contains('children') && !parentList.children.length) {
            parentList.remove();
          }
          var rootList = qs('#comments .reimu-comment-list');
          if (rootList) {
            syncLoadMoreRoot(rootList);
          }
          showTooltip(payload.data.message || t('commentDeleteFailed', '评论删除失败。'));
        }).catch(function () {
          showTooltip(t('commentDeleteFailed', '评论删除失败。'));
        }).finally(function () {
          button.disabled = false;
        });
      });
    });
  }

  function initCommentSorting() {
    qsa('#comments [data-comment-sort]').forEach(function (button) {
      if (button.dataset.commentSortReady) {
        return;
      }
      button.dataset.commentSortReady = 'true';
      button.addEventListener('click', function () {
        qsa('#comments [data-comment-sort]').forEach(function (item) {
          item.classList.remove('active');
        });
        button.classList.add('active');
        sortCommentList(button.getAttribute('data-comment-sort') || 'asc');
      });
    });
  }

  function setLoginModalOpen(open) {
    var modal = qs('#reimu-login-modal');
    if (!modal) {
      return;
    }
    modal.classList.toggle('show', !!open);
    modal.setAttribute('aria-hidden', open ? 'false' : 'true');
    modal.hidden = !open;
    modal.inert = !open;
    if (body) {
      body.classList.toggle('reimu-login-on', !!open);
    }
    if (open) {
      initLoginModal();
      var activePanel = qs('[data-login-panel].is-active', modal);
      var isLoginPanel = !activePanel || activePanel.getAttribute('data-login-panel') === 'login';
      if (!isLoginPanel && modal._reimuSetLoginPanel) {
        modal._reimuSetLoginPanel('login');
      } else if (modal._reimuClearAuthMessages) {
        modal._reimuClearAuthMessages();
      }
      var input = qs('#reimu-login-user', modal);
      if (input && input.focus) {
        window.setTimeout(function () {
          input.focus();
        }, 80);
      }
    }
  }

  function initLoginModal() {
    var modal = qs('#reimu-login-modal');
    if (!modal || modal.dataset.loginModalReady) {
      return;
    }
    modal.dataset.loginModalReady = 'true';
    if (!modal.classList.contains('show')) {
      modal.hidden = true;
      modal.inert = true;
    }
    var form = qs('[data-reimu-login-form]', modal);
    var registerForm = qs('[data-reimu-register-form]', modal);
    var lostForm = qs('[data-reimu-lost-form]', modal);
    var message = qs('[data-login-message]', modal);
    var submit = qs('.reimu-login-submit', modal);
    var registerCodeTimer = null;
    var lostCodeTimer = null;

    function clearAuthMessages() {
      qsa('.reimu-login-message', modal).forEach(function (item) {
        item.textContent = '';
        item.classList.remove('error', 'success');
      });
      qsa('[data-login-2fa]', modal).forEach(function (item) {
        item.hidden = true;
        var input = qs('[name="two_factor_code"]', item);
        if (input) {
          input.value = '';
        }
      });
      qsa('[data-password-toggle]', modal).forEach(function (button) {
        var wrap = button.closest('.reimu-login-password-row');
        var input = wrap ? qs('input', wrap) : null;
        if (input) {
          input.type = 'password';
        }
        button.classList.remove('is-visible');
        button.setAttribute('aria-label', t('showPassword', '显示密码'));
      });
    }
    modal._reimuClearAuthMessages = clearAuthMessages;

    function setPanel(name) {
      name = name || 'login';
      if (name === 'register' && !registerForm) {
        name = 'login';
      }
      if (name === 'lost' && !lostForm) {
        name = 'login';
      }
      qsa('[data-login-panel]', modal).forEach(function (panel) {
        var active = panel.getAttribute('data-login-panel') === name;
        panel.hidden = !active;
        panel.classList.toggle('is-active', active);
      });
      clearAuthMessages();
      var login2fa = qs('[data-login-2fa]', modal);
      if (login2fa && name !== 'login') {
        login2fa.hidden = true;
      }
      var title = qs('#reimu-login-title', modal);
      var desc = qs('.reimu-login-modal__desc', modal);
      if (title) {
        title.textContent = name === 'register' ? t('register', '注册') : (name === 'lost' ? t('lostPassword', '忘记密码？') : t('login', '登录'));
      }
      if (desc) {
        desc.textContent = name === 'register'
          ? t('registerDesc', '验证邮箱后即可创建账号。')
          : (name === 'lost' ? t('lostPasswordDesc', '验证邮箱后即可重置密码。') : desc.getAttribute('data-login-desc') || desc.textContent);
        desc.hidden = name === 'login' && !(desc.getAttribute('data-login-desc') || '').trim();
      }
      if (name === 'login') {
        var social = qs('.reimu-login-social', modal);
        if (social) {
          social.hidden = false;
        }
      } else {
        var socialPanel = qs('.reimu-login-social', modal);
        if (socialPanel) {
          socialPanel.hidden = true;
        }
      }
    }
    modal._reimuSetLoginPanel = setPanel;

    var descNode = qs('.reimu-login-modal__desc', modal);
    if (descNode && !descNode.getAttribute('data-login-desc')) {
      descNode.setAttribute('data-login-desc', descNode.textContent);
    }

    qsa('[data-login-close]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setLoginModalOpen(false);
        setPanel('login');
      });
    });

    qsa('[data-login-panel-trigger]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setPanel(button.getAttribute('data-login-panel-trigger') || 'login');
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('show')) {
        setLoginModalOpen(false);
        setPanel('login');
      }
    });

    if (form) {
      var savedEmail = storageGet('yneko_reimu_login_email');
      var loginEmailInput = qs('[name="log"]', form);
      var rememberInput = qs('[name="rememberme"]', form);
      if (savedEmail && loginEmailInput && !loginEmailInput.value) {
        loginEmailInput.value = savedEmail;
      }
      if (savedEmail && rememberInput) {
        rememberInput.checked = true;
      }
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!config.login || !config.login.ajaxUrl) {
          return;
        }
        function requestLogin(retried) {
          var formData = new FormData(form);
          formData.append('action', 'yneko_reimu_login');
          formData.append('nonce', config.login.nonce || '');
          return fetch(config.login.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          }).then(function (response) {
            return response.json().catch(function () {
              return { success: false, data: { message: config.login.failedText || t('loginFailed', '登录失败。') } };
            });
          }).then(function (payload) {
            if (!retried && payload && !payload.success && payload.data && payload.data.loginNonce && config.login) {
              config.login.nonce = payload.data.loginNonce;
              return requestLogin(true);
            }
            return payload;
          });
        }
        if (message) {
          message.textContent = config.login.loadingText || t('loginLoading', '登录中...');
          message.classList.remove('error', 'success');
        }
        if (submit) {
          submit.disabled = true;
        }
        requestLogin(false).then(function (payload) {
          var twoFactor = qs('[data-login-2fa]', form);
          if (payload && payload.data && payload.data.loginNonce && config.login) {
            config.login.nonce = payload.data.loginNonce;
          }
          if (payload && payload.success) {
            var submittedEmail = qs('[name="log"]', form);
            var submittedRemember = qs('[name="rememberme"]', form);
            if (submittedRemember && submittedRemember.checked && submittedEmail) {
              storageSet('yneko_reimu_login_email', submittedEmail.value || '');
            } else {
              storageRemove('yneko_reimu_login_email');
            }
            if (message) {
              message.textContent = config.login.successText || t('loginSuccess', '登录成功。');
              message.classList.add('success');
            }
            refreshCommentLoginState().then(function (updated) {
              if (updated) {
                if (form) {
                  form.reset();
                }
                var twoFactor = qs('[data-login-2fa]', form);
                if (twoFactor) {
                  twoFactor.hidden = true;
                }
                setLoginModalOpen(false);
                return;
              }
              window.setTimeout(function () {
                window.location.reload();
              }, 380);
            });
            return;
          }
          var text = payload && payload.data && payload.data.message ? payload.data.message : (config.login.failedText || t('loginFailed', '登录失败。'));
          if (payload && payload.data && payload.data.requires2fa) {
            if (twoFactor) {
              twoFactor.hidden = false;
              var twoFactorInput = qs('[name="two_factor_code"]', twoFactor);
              if (twoFactorInput && twoFactorInput.focus) {
                twoFactorInput.focus();
              }
            }
          } else if (twoFactor) {
            twoFactor.hidden = true;
            var hiddenInput = qs('[name="two_factor_code"]', twoFactor);
            if (hiddenInput) {
              hiddenInput.value = '';
            }
          }
          if (message) {
            message.textContent = text;
            message.classList.add('error');
          }
        }).catch(function () {
          if (message) {
            message.textContent = config.login.failedText || t('loginFailed', '登录失败。');
            message.classList.add('error');
          }
        }).finally(function () {
          if (submit) {
            submit.disabled = false;
          }
        });
      });
    }

    qsa('[data-password-toggle]', modal).forEach(function (button) {
      if (button.dataset.passwordToggleReady) {
        return;
      }
      button.dataset.passwordToggleReady = 'true';
      button.addEventListener('click', function () {
        var wrap = button.closest('.reimu-login-password-row');
        var input = wrap ? qs('input', wrap) : null;
        if (!input) {
          return;
        }
        var visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.classList.toggle('is-visible', !visible);
        button.setAttribute('aria-label', !visible ? t('hidePassword', '隐藏密码') : t('showPassword', '显示密码'));
      });
    });

    function setCodeCountdown(button, seconds, timerSetter) {
      var remaining = Number(seconds || 60);
      var timer = null;
      if (!button) {
        return;
      }
      button.disabled = true;
      function render() {
        button.textContent = String(t('registerCodeWait', '%s 秒后重发')).replace('%s', remaining);
        remaining -= 1;
        if (remaining < 0) {
          window.clearInterval(timer);
          timerSetter(null);
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        }
      }
      render();
      timer = window.setInterval(render, 1000);
      timerSetter(timer);
    }

    function bindCodeButton(authForm, selector, action, nonceKey, fields, messageSelector, timerName) {
      if (!authForm) {
        return;
      }
      var button = qs(selector, authForm);
      var authMessage = qs(messageSelector, authForm);
      if (!button || button.dataset.registerCodeReady) {
        return;
      }
      button.dataset.registerCodeReady = 'true';
      button.setAttribute('data-label', button.textContent);
      button.addEventListener('click', function () {
        if (!config.login || !config.login.ajaxUrl || button.disabled) {
          return;
        }
        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', config.login[nonceKey] || '');
        formData.append('redirect_to', window.location.href || '');
        fields.forEach(function (fieldName) {
          var field = qs('[name="' + fieldName + '"]', authForm);
          formData.append(fieldName, field ? field.value || '' : '');
        });
        button.disabled = true;
        button.textContent = t('registerCodeSending', '发送中...');
        if (authMessage) {
          authMessage.textContent = '';
          authMessage.classList.remove('error', 'success');
        }
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          var text = payload && payload.data && payload.data.message ? payload.data.message : t('registerCodeSent', '验证码已发送，请检查您的邮箱。');
          if (authMessage) {
            authMessage.innerHTML = text;
            authMessage.classList.toggle('success', !!(payload && payload.success));
            authMessage.classList.toggle('error', !(payload && payload.success));
          }
          if (payload && payload.success) {
            if (timerName === 'lost') {
              window.clearInterval(lostCodeTimer);
              setCodeCountdown(button, 60, function (timer) {
                lostCodeTimer = timer;
              });
            } else {
              window.clearInterval(registerCodeTimer);
              setCodeCountdown(button, 60, function (timer) {
                registerCodeTimer = timer;
              });
            }
            return;
          }
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        }).catch(function () {
          if (authMessage) {
            authMessage.textContent = config.login.failedText || t('loginFailed', '操作失败。');
            authMessage.classList.add('error');
          }
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        });
      });
    }

    function bindSimpleAuthForm(authForm, action, nonceKey, messageSelector) {
      if (!authForm) {
        return;
      }
      var authMessage = qs(messageSelector, authForm);
      var authSubmit = qs('[type="submit"]', authForm);
      authForm.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!config.login || !config.login.ajaxUrl) {
          return;
        }
        var formData = new FormData(authForm);
        formData.append('action', action);
        formData.append('nonce', config.login[nonceKey] || '');
        formData.append('redirect_to', window.location.href || '');
        if (authMessage) {
          var loadingText = authForm.getAttribute('data-loading-text') || '';
          if (!loadingText) {
            loadingText = action === 'yneko_reimu_register'
              ? t('registerLoading', '注册中...')
              : (action === 'yneko_reimu_lostpassword' ? t('resetLoading', '重置中...') : (config.login.loadingText || t('loginLoading', '处理中...')));
          }
          authMessage.textContent = loadingText;
          authMessage.classList.remove('error', 'success');
        }
        if (authSubmit) {
          authSubmit.disabled = true;
        }
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          var text = payload && payload.data && payload.data.message ? payload.data.message : (config.login.failedText || t('loginFailed', '操作失败。'));
          if (authMessage) {
            authMessage.innerHTML = text;
            authMessage.classList.toggle('success', !!(payload && payload.success));
            authMessage.classList.toggle('error', !(payload && payload.success));
          }
          if (payload && payload.success && (action === 'yneko_reimu_register' || action === 'yneko_reimu_lostpassword')) {
            var registeredEmail = qs('[name="user_email"]', authForm);
            var registeredPassword = qs('[name="user_password"]', authForm);
            var registeredEmailValue = registeredEmail ? registeredEmail.value || '' : '';
            var registeredPasswordValue = registeredPassword ? registeredPassword.value || '' : '';
            var loginEmail = qs('[name="log"]', form);
            var loginPassword = qs('[name="pwd"]', form);
            if (action === 'yneko_reimu_register') {
              if (loginEmail) {
                loginEmail.value = registeredEmailValue;
              }
              if (loginPassword) {
                loginPassword.value = registeredPasswordValue;
              }
            }
            authForm.reset();
            window.setTimeout(function () {
              setPanel('login');
              if (action === 'yneko_reimu_register') {
                if (loginEmail) {
                  loginEmail.value = registeredEmailValue;
                }
                if (loginPassword) {
                  loginPassword.value = registeredPasswordValue;
                }
              }
            }, 900);
          }
        }).catch(function () {
          if (authMessage) {
            authMessage.textContent = config.login.failedText || t('loginFailed', '操作失败。');
            authMessage.classList.add('error');
          }
        }).finally(function () {
          if (authSubmit) {
            authSubmit.disabled = false;
          }
        });
      });
    }

    bindCodeButton(registerForm, '[data-register-code-send]', 'yneko_reimu_register_code', 'registerCodeNonce', ['display_name', 'user_email'], '[data-register-message]', 'register');
    bindCodeButton(lostForm, '[data-lost-code-send]', 'yneko_reimu_lostpassword_code', 'lostCodeNonce', ['user_login'], '[data-lost-message]', 'lost');
    bindSimpleAuthForm(registerForm, 'yneko_reimu_register', 'registerNonce', '[data-register-message]');
    bindSimpleAuthForm(lostForm, 'yneko_reimu_lostpassword', 'lostNonce', '[data-lost-message]');
  }

  function initProfileModal() {
    var modal = qs('#reimu-profile-modal');
    if (!modal || modal.dataset.profileReady) {
      return;
    }
    modal.dataset.profileReady = 'true';
    var form = qs('[data-reimu-profile-form]', modal);
    var message = qs('[data-profile-message]', modal);
    var emailTimer = null;
    var profileStatusTimer = null;
    var profileAvatarChanged = false;
    var profileAvatarOriginalUrl = '';

    function setOpen(open) {
      modal.classList.toggle('show', !!open);
      modal.setAttribute('aria-hidden', open ? 'false' : 'true');
      modal.hidden = !open;
      modal.inert = !open;
      if (body) {
        body.classList.toggle('reimu-login-on', !!open);
      }
      if (open) {
        if (message) {
          message.textContent = '';
          message.classList.remove('error', 'success');
        }
        refreshProfile();
      } else if (form) {
        form.reset();
        validateProfilePasswords();
        clearProfileTagError();
        setProfileAvatarHint('');
        var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
        if (avatarUploadButton) {
          avatarUploadButton.textContent = t('upload', '上传');
          avatarUploadButton.disabled = false;
        }
      }
    }
    modal._reimuSetProfileOpen = setOpen;

    function setMessage(text, ok) {
      if (!message) {
        return;
      }
      message.textContent = text || '';
      message.classList.toggle('success', !!ok);
      message.classList.toggle('error', !ok && !!text);
    }

    function normalizeUrlInput(input) {
      if (!input) {
        return;
      }
      var value = String(input.value || '').trim();
      if (value && !/^[a-z][a-z0-9+.-]*:\/\//i.test(value) && /^[^\s/@]+\.[^\s]+/.test(value)) {
        input.value = 'https://' + value;
      }
    }

    function validateProfilePasswords() {
      if (!form) {
        return true;
      }
      var password = qs('[name="new_password"]', form);
      var confirm = qs('[name="new_password_confirm"]', form);
      var messageText = t('passwordMismatch', '两次输入的密码不一致。');
      var invalid = !!(password && confirm && confirm.value && password.value !== confirm.value);
      if (confirm) {
        confirm.classList.toggle('is-invalid', invalid);
        confirm.setCustomValidity(invalid ? messageText : '');
      }
      if (password) {
        password.classList.toggle('is-invalid', invalid);
      }
      return !invalid;
    }

    function setProfileAvatarHint(text, ok) {
      var hint = form ? qs('[data-profile-avatar-hint]', form) : null;
      if (!hint) {
        return;
      }
      hint.textContent = text || '';
      hint.classList.toggle('success', !!ok);
      hint.classList.toggle('error', !ok && !!text);
    }

    function markProfileAvatarChanged(changed) {
      profileAvatarChanged = !!changed;
      var input = form ? qs('[data-profile-avatar-changed]', form) : null;
      if (input) {
        input.value = profileAvatarChanged ? '1' : '0';
      }
    }

    function profileAvatarUrlChanged() {
      var input = form ? qs('[name="avatar_url"]', form) : null;
      return !!(input && String(input.value || '').trim() !== String(profileAvatarOriginalUrl || '').trim());
    }

    function profileStatusMessage(type, status) {
      var map = {
        avatar: {
          pending: ['avatarPending', '头像审核中'],
          updated: ['avatarUpdated', '头像已更新'],
          rejected: ['avatarRejected', '头像审核不通过']
        },
        tags: {
          pending: ['tagsPending', '标签审核中'],
          updated: ['tagsUpdated', '标签已更新'],
          rejected: ['tagsRejected', '标签审核不通过']
        },
        comments: {
          pending: ['commentsPending', '评论审核中'],
          updated: ['commentsUpdated', '评论已更新'],
          rejected: ['commentsRejected', '评论审核不通过']
        }
      };
      var item = map[type] && map[type][status] ? map[type][status] : null;
      return item ? t(item[0], item[1]) : '';
    }

    function hasPendingProfileStatus(statuses) {
      statuses = statuses || {};
      return ['avatar', 'tags', 'comments'].some(function (type) {
        return statuses[type] && statuses[type].status === 'pending';
      });
    }

    function profileStatusRows(statuses) {
      statuses = statuses || {};
      var order = ['avatar', 'tags', 'comments'];
      return order.map(function (type) {
        var item = statuses[type] || {};
        var status = item.status || '';
        var text = status ? profileStatusMessage(type, status) : '';
        if (!text) {
          return null;
        }
        return {
          type: type,
          status: status,
          text: text,
          state: status === 'rejected' ? 'error' : (status === 'updated' ? 'success' : 'pending'),
          count: Number(item.count || 0)
        };
      }).filter(Boolean);
    }

    function ackProfileStatuses(types) {
      types = Array.isArray(types) ? types.filter(Boolean) : [];
      if (!types.length || !config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
        return;
      }
      var data = new FormData();
      data.append('action', 'yneko_reimu_profile_status_ack');
      data.append('nonce', config.login.profileNonce || '');
      types.forEach(function (type) {
        data.append('types[]', type);
      });
      fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          if (payload && payload.success && payload.data && config.login) {
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
          }
        }).catch(function () {});
    }

    function setInlineProfileStatus(text, state, type, count) {
      var rows = text ? [{ text: text, state: state, type: type, count: count || 0, status: state === 'pending' ? 'pending' : '' }] : [];
      setInlineProfileStatuses(rows);
    }

    function setInlineProfileStatuses(rows) {
      rows = Array.isArray(rows) ? rows : [];
      qsa('.reimu-comment-current-user').forEach(function (identity) {
        var name = qs('.reimu-comment-current-user__name', identity);
        var list = qs('[data-profile-inline-status-list]', identity);
        if (!rows.length) {
          if (list) {
            list.remove();
          }
          return;
        }
        if (!list) {
          list = document.createElement('span');
          list.className = 'reimu-comment-current-user__statuses';
          list.setAttribute('data-profile-inline-status-list', '');
          if (name) {
            name.insertAdjacentElement('afterend', list);
          } else {
            identity.appendChild(list);
          }
        }
        list.innerHTML = '';
        rows.forEach(function (row) {
          var status = document.createElement('span');
          status.className = 'reimu-comment-current-user__status';
          status.setAttribute('data-profile-inline-status', '');
          status.setAttribute('data-profile-status-kind', row.type || '');
          status.setAttribute('data-profile-status-state', row.status || row.state || '');
          status.textContent = row.text || '';
          status.classList.toggle('is-error', row.state === 'error');
          status.classList.toggle('is-success', row.state === 'success');
          status.classList.toggle('is-pending', row.state === 'pending');
          if (row.state === 'pending' && Number(row.count || 0) > 1 && (row.type === 'tags' || row.type === 'comments')) {
            var badge = document.createElement('b');
            badge.className = 'reimu-comment-current-user__status-count';
            badge.textContent = String(row.count);
            status.appendChild(badge);
          }
          list.appendChild(status);
        });
      });
    }

    function applyInlineProfileStatus(data, options) {
      options = options || {};
      var rows = profileStatusRows(data && data.reviewStatuses ? data.reviewStatuses : {});
      if (!rows.length) {
        if (options.clearEmpty) {
          setInlineProfileStatuses([]);
        }
        return false;
      }
      setInlineProfileStatuses(rows);
      var pending = rows.some(function (row) { return row.state === 'pending'; });
      var ackTypes = rows.filter(function (row) { return row.state !== 'pending'; }).map(function (row) { return row.type; });
      if (ackTypes.length) {
        if (options.autohide !== false) {
          window.setTimeout(function () {
            var pendingRows = rows.filter(function (row) { return row.state === 'pending'; });
            setInlineProfileStatuses(pendingRows);
            ackProfileStatuses(ackTypes);
          }, 4200);
        }
      }
      return pending;
    }

    function applyProfilePayload(data, options) {
      if (!data) {
        return false;
      }
      options = options || {};
      var modalOpen = modal.classList.contains('show') || modal.getAttribute('aria-hidden') === 'false';
      if (options.forceFill || (options.fillProfile !== false && !modalOpen)) {
        fillProfile(data);
      }
      updateCommentBadgesForProfile(data);
      updateVisibleProfileLinks(data);
      updateVisibleProfileAvatars(data);
      return applyInlineProfileStatus(data, options);
    }

    function startProfileStatusPolling() {
      window.clearInterval(profileStatusTimer);
      profileStatusTimer = window.setInterval(function () {
        if (!config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
          return;
        }
        var data = new FormData();
        data.append('action', 'yneko_reimu_profile_get');
        data.append('nonce', config.login.profileNonce || '');
        data.append('redirect_to', window.location.href || '');
        fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
          .then(function (response) { return response.json(); })
          .then(function (payload) {
            if (!payload || !payload.success || !payload.data) {
              return;
            }
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
            var stillPending = applyProfilePayload(payload.data, { autohide: true, fillProfile: false });
            if (stillPending) {
              return;
            }
            window.clearInterval(profileStatusTimer);
          }).catch(function () {});
      }, 5000);
    }
    modal._reimuStartProfileStatusPolling = startProfileStatusPolling;
    modal._reimuApplyProfilePayload = applyProfilePayload;
    modal._reimuSetInlineProfileStatus = setInlineProfileStatus;
    modal._reimuSetInlineProfileStatuses = setInlineProfileStatuses;
    modal._reimuHasPendingProfileStatus = hasPendingProfileStatus;

    function fillProfile(data) {
      if (!data || !form) {
        return;
      }
      var fields = {
        display_name: data.displayName,
        current_email: data.email,
        user_email: '',
        avatar_url: data.avatarUrl,
        profile_url: data.profileUrl
      };
      profileAvatarOriginalUrl = data.avatarUrl || '';
      Object.keys(fields).forEach(function (name) {
        var input = qs('[name="' + name + '"]', form);
        if (input) {
          input.value = fields[name] || '';
        }
      });
      var currentEmailDisplay = qs('[data-profile-current-email-display]', form);
      if (currentEmailDisplay) {
        currentEmailDisplay.textContent = fields.current_email || '';
      }
      var twoFactor = qs('[name="totp_enabled"]', form);
      if (twoFactor) {
        twoFactor.checked = !!data.twoFactor;
      }
      var avatarFrame = qs('[name="avatar_frame_enabled"]', form);
      if (avatarFrame) {
        avatarFrame.checked = data.avatarFrameEnabled !== false;
      }
      var preview = qs('[data-profile-avatar-preview]', form);
      if (preview && data.avatarUrl) {
        preview.src = data.avatarUrl;
      }
      var status = qs('[data-profile-avatar-status]', form);
      if (status) {
        status.hidden = !data.avatarPending;
        status.textContent = data.avatarPending ? t('avatarPending', '头像审核中') : '';
      }
      var commentTags = Array.isArray(data.commentTags) ? data.commentTags : [];
      var pendingTags = Array.isArray(data.pendingCommentTags) ? data.pendingCommentTags : [];
      var activeCustomTags = commentTags.filter(function (tag) { return tag && tag.type === 'custom'; });
      var seenCustomTags = {};
      var customTags = [];
      pendingTags.concat(activeCustomTags).forEach(function (tag) {
        if (!tag || !tag.label) {
          return;
        }
        var key = String(tag.id || tag.old_id || '').trim() || (String(tag.label || '').trim().toLowerCase() + '|' + String(tag.color || '').trim().toLowerCase());
        if (seenCustomTags[key]) {
          return;
        }
        seenCustomTags[key] = true;
        customTags.push(tag);
      });
      var specialTags = commentTags.filter(function (tag) { return tag && tag.type === 'special' && tag.key; });
      specialTags.forEach(function (tag) {
        var specialInput = qs('[name="comment_special_enabled[' + tag.key + ']"]', form);
        if (specialInput) {
          specialInput.checked = tag.enabled !== '0';
        }
      });
      qsa('[name="comment_tag_label[]"]', form).forEach(function (input, index) {
        var tag = customTags[index] || null;
        input.value = tag && tag.label ? tag.label : '';
      });
      qsa('[name="comment_tag_color[]"]', form).forEach(function (input, index) {
        var tag = customTags[index] || null;
        input.value = tag && tag.color ? tag.color : '#ff5252';
      });
      renderProfileCustomTags(customTags);
      var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
      if (avatarUploadButton) {
        avatarUploadButton.textContent = t('upload', '上传');
        avatarUploadButton.disabled = false;
      }
      var avatarFileInput = qs('[data-profile-avatar-file]', form);
      if (avatarFileInput) {
        avatarFileInput.value = '';
      }
      markProfileAvatarChanged(false);
      setProfileAvatarHint('');
      validateProfilePasswords();
    }

    var profileTagMessageTimer = null;
    function clearProfileTagError() {
      var message = qs('[data-profile-tags-message]', form);
      if (message) {
        message.hidden = true;
        message.textContent = '';
      }
      qsa('[name="comment_tag_label[]"]', form).forEach(function (input) {
        input.classList.remove('is-invalid');
      });
      window.clearTimeout(profileTagMessageTimer);
    }

    function showProfileTagError(payload) {
      var data = payload && payload.data ? payload.data : {};
      var message = qs('[data-profile-tags-message]', form);
      var inputs = qsa('[name="comment_tag_label[]"]', form);
      var index = Number.isFinite(Number(data.index)) ? Number(data.index) : -1;
      clearProfileTagError();
      if (index >= 0 && inputs[index]) {
        inputs[index].classList.add('is-invalid');
        if (inputs[index].focus) {
          inputs[index].focus();
        }
      } else if (data.value) {
        inputs.some(function (input) {
          if (String(input.value || '').trim().toLowerCase() === String(data.value || '').trim().toLowerCase()) {
            input.classList.add('is-invalid');
            if (input.focus) {
              input.focus();
            }
            return true;
          }
          return false;
        });
      }
      if (message) {
        message.textContent = data.message || (payload && payload.message) || t('loginFailed', '操作失败。');
        message.hidden = false;
        profileTagMessageTimer = window.setTimeout(clearProfileTagError, 4200);
      }
    }

    function updateCommentBadgesForProfile(data) {
      if (!data || !data.userId) {
        return;
      }
      var html = data.commentBadgesHtml || '';
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"]').forEach(function (item) {
        var headline = qs('.reimu-comment__headline', item);
        if (!headline) {
          return;
        }
        var existing = qs('.reimu-comment-user-tags', headline);
        if (existing) {
          if (html) {
            existing.outerHTML = html;
          } else {
            existing.remove();
          }
          return;
        }
        if (html) {
          var author = qs('.reimu-comment__author', headline);
          if (author) {
            author.insertAdjacentHTML('afterend', html);
          }
        }
      });
    }

    function updateVisibleProfileLinks(data) {
      if (!data || !data.userId) {
        return;
      }
      if (data.identity) {
        qsa('.reimu-comment-current-user').forEach(function (identity) {
          identity.outerHTML = data.identity;
        });
        initCommentAjaxLogout();
      }
      var displayName = data.displayName || '';
      var profileUrl = String(data.publicProfileUrl || '').trim();
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"]').forEach(function (item) {
        var authorWrap = qs('.reimu-comment__author', item);
        if (!authorWrap) {
          return;
        }
        if (profileUrl) {
          authorWrap.innerHTML = '<a class="reimu-comment__author-link" href="' + escapeHtml(profileUrl) + '" target="_blank" rel="noopener noreferrer nofollow">' + escapeHtml(displayName) + '</a>';
        } else {
          authorWrap.innerHTML = '<span class="reimu-comment__author-name">' + escapeHtml(displayName) + '</span>';
        }
      });
    }

    function updateVisibleProfileAvatars(data) {
      if (!data || !data.userId || !data.avatarHtml) {
        return;
      }
      if (data.identity) {
        qsa('.reimu-comment-current-user').forEach(function (identity) {
          identity.outerHTML = data.identity;
        });
        initCommentAjaxLogout();
      }
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"] .reimu-comment__avatar').forEach(function (avatar) {
        avatar.innerHTML = data.avatarHtml;
      });
    }

    function profileSpecialCount() {
      var count = 0;
      qsa('[name^="comment_special_enabled["]', form).forEach(function (input) {
        if (input.checked) {
          count += 1;
        }
      });
      return count;
    }

    function profileEnabledCustomTagCount() {
      var count = 0;
      qsa('[data-profile-tag-enabled]', form).forEach(function (input) {
        if (input.checked) {
          count += 1;
        }
      });
      return count;
    }

    function profileSelectedTagCount() {
      return profileSpecialCount() + profileEnabledCustomTagCount();
    }

    function enforceProfileSpecialLimit(changedInput) {
      var checked = qsa('[name^="comment_special_enabled["], [data-profile-tag-enabled]', form).filter(function (input) {
        return input.checked;
      });
      if (checked.length <= 2) {
        return;
      }
      var toDisable = checked.find(function (input) {
        return input !== changedInput;
      }) || checked[0];
      if (toDisable) {
        toDisable.checked = false;
      }
    }

    function profileCustomTagCapacity() {
      return Math.max(0, 2 - profileSpecialCount());
    }

    function profileCustomTagStorageLimit() {
      var list = qs('[data-profile-tag-list]', form);
      return Math.max(1, Number(list && list.dataset ? list.dataset.storageLimit || 5 : 5));
    }

    function syncProfileAddTagState() {
      var list = qs('[data-profile-tag-list]', form);
      var add = qs('[data-profile-add-tag]', form);
      if (!list || !add) {
        return;
      }
      var rows = qsa('.reimu-profile-tag-row', list);
      var capacity = profileCustomTagCapacity();
      var selected = 0;
      list.dataset.maxTags = String(capacity);
      add.hidden = false;
      add.disabled = rows.length >= profileCustomTagStorageLimit();
      rows.forEach(function (row) {
        var checkbox = qs('[data-profile-tag-enabled]', row);
        if (checkbox && checkbox.checked) {
          selected += 1;
          if (selected > capacity) {
            checkbox.checked = false;
            selected -= 1;
          }
        }
      });
      rows.forEach(function (row) {
        var checkbox = qs('[data-profile-tag-enabled]', row);
        var hidden = qs('[name="comment_tag_enabled[]"]', row);
        var active = !!(checkbox && checkbox.checked);
        row.classList.toggle('is-disabled', !active);
        if (hidden) {
          hidden.value = active ? '1' : '0';
        }
        if (checkbox) {
          checkbox.disabled = !active && selected >= capacity;
          checkbox.setAttribute('aria-disabled', checkbox.disabled ? 'true' : 'false');
        }
      });
    }

    function profileTagRow(tag) {
      tag = tag || {};
      var row = document.createElement('div');
      row.className = 'reimu-profile-tag-row';
      var enabled = tag.enabled !== '0';
      row.innerHTML = '<input name="comment_tag_id[]" type="hidden" value="' + escapeHtml(tag.id || '') + '">' +
        '<input name="comment_tag_enabled[]" type="hidden" value="' + (enabled ? '1' : '0') + '">' +
        '<label class="reimu-profile-tag-enabled" title="' + escapeHtml(t('enable', '启用')) + '"><input type="checkbox" data-profile-tag-enabled' + (enabled ? ' checked' : '') + '><span></span></label>' +
        '<input name="comment_tag_label[]" type="text" maxlength="8" placeholder="' + escapeHtml(t('commentTag', '标签')) + '" value="' + escapeHtml(tag.label || '') + '">' +
        '<input name="comment_tag_color[]" type="color" value="' + escapeHtml(tag.color || '#ff5252') + '">' +
        '<button type="button" class="reimu-profile-remove-tag" data-profile-remove-tag aria-label="' + escapeHtml(t('remove', '删除')) + '">×</button>';
      return row;
    }

    function renderProfileCustomTags(tags) {
      var list = qs('[data-profile-tag-list]', form);
      if (!list) {
        return;
      }
      list.innerHTML = '';
      tags = Array.isArray(tags) ? tags : [];
      tags.slice(0, profileCustomTagStorageLimit()).forEach(function (tag) {
        if (tag && tag.label) {
          list.appendChild(profileTagRow(tag));
        }
      });
      syncProfileAddTagState();
    }

    function refreshProfile() {
      if (!config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
        return;
      }
      var data = new FormData();
      data.append('action', 'yneko_reimu_profile_get');
      data.append('nonce', config.login.profileNonce || '');
      data.append('redirect_to', window.location.href || '');
      fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          if (payload && payload.success) {
            if (payload.data && config.login) {
              config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
              config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
            }
            var stillPending = applyProfilePayload(payload.data, { autohide: false, forceFill: true });
            if (stillPending || hasPendingProfileStatus(payload.data && payload.data.reviewStatuses)) {
              startProfileStatusPolling();
            }
          }
        }).catch(function () {});
    }

    function postProfileAction(action, data) {
      data = data || new FormData();
      data.append('action', action);
      data.append('nonce', config.login.profileNonce || '');
      data.append('redirect_to', window.location.href || '');
      return fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          if (payload && payload.data && config.login) {
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
          }
          return payload;
        });
    }

    qsa('[data-profile-close]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setOpen(false);
      });
    });

    document.addEventListener('click', function (event) {
      var trigger = event.target && event.target.closest ? event.target.closest('[data-reimu-profile-open]') : null;
      if (!trigger) {
        return;
      }
      event.preventDefault();
      setOpen(true);
    });

    qsa('[data-password-toggle]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        var wrap = button.closest('.reimu-login-password-row');
        var input = wrap ? qs('input', wrap) : null;
        if (!input) {
          return;
        }
        var visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.classList.toggle('is-visible', !visible);
        button.setAttribute('aria-label', !visible ? t('hidePassword', '隐藏密码') : t('showPassword', '显示密码'));
      });
    });

    var avatarUrlInput = qs('[name="avatar_url"]', form);
    if (avatarUrlInput) {
      avatarUrlInput.addEventListener('input', function () {
        markProfileAvatarChanged(profileAvatarUrlChanged());
        var preview = qs('[data-profile-avatar-preview]', form);
        if (preview && avatarUrlInput.value) {
          preview.src = avatarUrlInput.value;
        }
      });
    }

    var currentEmailInput = qs('[data-profile-current-email]', form);
    if (currentEmailInput) {
      currentEmailInput.setAttribute('tabindex', '-1');
      currentEmailInput.setAttribute('aria-readonly', 'true');
      currentEmailInput.addEventListener('focus', function () {
        currentEmailInput.blur();
      });
    }

    var addTagButton = qs('[data-profile-add-tag]', form);
    if (addTagButton) {
      addTagButton.addEventListener('click', function () {
        var list = qs('[data-profile-tag-list]', form);
        if (!list || addTagButton.disabled) {
          return;
        }
        if (qsa('.reimu-profile-tag-row', list).length >= profileCustomTagStorageLimit()) {
          syncProfileAddTagState();
          return;
        }
        list.appendChild(profileTagRow({ color: '#ff5252', enabled: profileEnabledCustomTagCount() < profileCustomTagCapacity() ? '1' : '0' }));
        syncProfileAddTagState();
      });
    }

    qsa('[name^="comment_special_enabled["]', form).forEach(function (input) {
      input.addEventListener('change', function () {
        if (input.checked) {
          enforceProfileSpecialLimit(input);
        }
        syncProfileAddTagState();
      });
    });

    form.addEventListener('click', function (event) {
      var remove = event.target && event.target.closest ? event.target.closest('[data-profile-remove-tag]') : null;
      if (!remove) {
        return;
      }
      event.preventDefault();
      var row = remove.closest('.reimu-profile-tag-row');
      if (row) {
        row.remove();
      }
      syncProfileAddTagState();
    });

    form.addEventListener('change', function (event) {
      var checkbox = event.target && event.target.matches && event.target.matches('[data-profile-tag-enabled]') ? event.target : null;
      if (!checkbox) {
        return;
      }
      if (checkbox.checked && profileSelectedTagCount() > 2) {
        checkbox.checked = false;
        showProfileTagError({ data: { message: t('commentTagLimit', '特殊标签和已勾选的自定义标签合计最多 2 个。') } });
      } else {
        clearProfileTagError();
      }
      syncProfileAddTagState();
    });

    var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
    var avatarFileInput = qs('[data-profile-avatar-file]', form);
    if (avatarUploadButton && avatarFileInput) {
      avatarUploadButton.addEventListener('click', function () {
        if (avatarUploadButton.disabled) {
          return;
        }
        setProfileAvatarHint('');
        avatarFileInput.value = '';
        markProfileAvatarChanged(profileAvatarUrlChanged());
        avatarFileInput.click();
      });
      avatarFileInput.addEventListener('change', function () {
        var file = avatarFileInput.files && avatarFileInput.files[0] ? avatarFileInput.files[0] : null;
        if (!file) {
          avatarUploadButton.textContent = t('upload', '上传');
          avatarUploadButton.disabled = false;
          setProfileAvatarHint('');
          return;
        }
        avatarUploadButton.textContent = t('upload', '上传');
        var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var extOk = /\.(?:jpe?g|png|webp)$/i.test(file.name || '');
        if (allowedTypes.indexOf(file.type) === -1 && !extOk) {
          setProfileAvatarHint(t('avatarInvalidType', '头像仅支持 JPG、PNG 或 WebP。'), false);
          avatarFileInput.value = '';
          return;
        }
        var maxMb = Number(modal.getAttribute('data-avatar-max-mb') || 1);
        if (file.size > maxMb * 1024 * 1024) {
          setProfileAvatarHint(t('avatarTooLarge', '头像文件超过大小限制。'), false);
          avatarFileInput.value = '';
          return;
        }
        var preview = qs('[data-profile-avatar-preview]', form);
        if (preview && window.URL && window.URL.createObjectURL) {
          preview.src = window.URL.createObjectURL(file);
        }
        markProfileAvatarChanged(true);
        setProfileAvatarHint(t('avatarReady', '头像已选择，保存后生效。'), true);
      });
    }

    qsa('[name="new_password"], [name="new_password_confirm"]', form).forEach(function (input) {
      input.addEventListener('input', validateProfilePasswords);
    });

    var emailCodeButton = qs('[data-profile-email-code-send]', form);
    if (emailCodeButton) {
      emailCodeButton.addEventListener('click', function () {
        if (emailCodeButton.disabled) {
          return;
        }
        var data = new FormData();
        var email = qs('[name="user_email"]', form);
        var currentEmail = qs('[name="current_email"]', form);
        var newEmail = email ? String(email.value || '').trim() : '';
        var oldEmail = currentEmail ? String(currentEmail.value || '').trim() : '';
        if (!newEmail || newEmail.toLowerCase() === oldEmail.toLowerCase()) {
          setMessage(t('emailDuplicate', '新邮箱地址不要与原邮箱地址重复。'), false);
          if (email && email.focus) {
            email.focus();
          }
          return;
        }
        data.append('user_email', newEmail);
        emailCodeButton.disabled = true;
        emailCodeButton.textContent = t('registerCodeSending', '发送中...');
        postProfileAction('yneko_reimu_profile_email_code', data).then(function (payload) {
          setMessage(payload && payload.data && payload.data.message ? payload.data.message : '', payload && payload.success);
          if (!payload || !payload.success) {
            emailCodeButton.disabled = false;
            emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
            return;
          }
          var remaining = 60;
          emailCodeButton.setAttribute('data-label', emailCodeButton.getAttribute('data-label') || emailCodeButton.textContent);
          window.clearInterval(emailTimer);
          emailTimer = window.setInterval(function () {
            emailCodeButton.textContent = String(t('registerCodeWait', '%s 秒后重发')).replace('%s', remaining);
            remaining -= 1;
            if (remaining < 0) {
              window.clearInterval(emailTimer);
              emailCodeButton.disabled = false;
              emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
            }
          }, 1000);
        }).catch(function () {
          setMessage(config.login.failedText || t('loginFailed', '操作失败。'), false);
          emailCodeButton.disabled = false;
          emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
        });
      });
    }

    var twoFactorToggle = qs('[data-profile-2fa-toggle]', form);
    var twoFactorSetup = qs('[data-profile-2fa-setup]', form);
    if (twoFactorToggle && twoFactorSetup) {
      twoFactorSetup.hidden = !twoFactorToggle.checked;
      twoFactorToggle.addEventListener('change', function () {
        twoFactorSetup.hidden = !twoFactorToggle.checked;
      });
    }

    var generate2fa = qs('[data-profile-2fa-generate]', form);
    if (generate2fa) {
      generate2fa.addEventListener('click', function () {
        postProfileAction('yneko_reimu_profile_totp_generate').then(function (payload) {
          if (!payload || !payload.success) {
            setMessage(payload && payload.data && payload.data.message ? payload.data.message : config.login.failedText, false);
            return;
          }
          var secret = qs('[data-profile-2fa-secret]', form);
          var qr = qs('[data-profile-2fa-qr]', form);
          if (secret) {
            secret.textContent = payload.data.secret || '';
          }
          if (qr) {
            qr.src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(payload.data.uri || '');
            qr.hidden = false;
          }
          setMessage(t('profile2faGenerated', '请用认证器扫码，并输入 6 位验证码后保存。'), true);
        });
      });
    }

    if (form) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        normalizeUrlInput(qs('[name="profile_url"]', form));
        normalizeUrlInput(qs('[name="avatar_url"]', form));
        if (!validateProfilePasswords()) {
          var confirm = qs('[name="new_password_confirm"]', form);
          setMessage(t('passwordMismatch', '两次输入的密码不一致。'), false);
          if (confirm && confirm.reportValidity) {
            confirm.reportValidity();
          }
          return;
        }
        var data = new FormData(form);
        var avatarChanged = profileAvatarChanged;
        postProfileAction('yneko_reimu_profile_save', data).then(function (payload) {
          setMessage(payload && payload.data && payload.data.message ? payload.data.message : '', payload && payload.success);
          if (payload && payload.success) {
            clearProfileTagError();
            var stillPending = applyProfilePayload(payload.data, { autohide: true, forceFill: true });
            if (stillPending || hasPendingProfileStatus(payload.data && payload.data.reviewStatuses)) {
              startProfileStatusPolling();
            } else if (avatarChanged && payload.data && payload.data.avatarUrl) {
              applyInlineProfileStatus(payload.data, { autohide: true });
            }
            setOpen(false);
            refreshCommentLoginState();
          } else if (payload && payload.data && payload.data.field === 'comment_tag_label') {
            showProfileTagError(payload);
          } else if (payload && payload.data && payload.data.message) {
            setMessage(payload.data.message, false);
          }
        }).catch(function () {
          setMessage(config.login.failedText || t('loginFailed', '操作失败。'), false);
        });
      });
    }
    refreshProfile();
  }

  function applyCommentLoggedInState(data) {
    if (!data || !data.loggedIn || !data.identity) {
      return false;
    }
    if (config.comments) {
      config.comments.nonce = data.commentNonce || config.comments.nonce;
    }
    if (config.commentUploads) {
      Object.assign(config.commentUploads, data.commentUploads || {});
      config.commentUploads.isLoggedIn = true;
      config.commentUploads.nonce = data.commentUploadNonce || config.commentUploads.nonce || '';
    }
    if (config.login) {
      config.login.logoutNonce = data.logoutNonce || config.login.logoutNonce;
      config.login.profileNonce = data.profileNonce || config.login.profileNonce;
    }
    qsa('.reimu-comment-form').forEach(function (form) {
      form.classList.add('reimu-comment-form--logged-in');
      qsa('.reimu-comment-form__fields input', form).forEach(function (input) {
        input.value = '';
      });
      qsa('.reimu-comment-form__fields', form).forEach(function (fields) {
        fields.hidden = true;
      });
      var current = qs('.reimu-comment-current-user', form);
      if (current) {
        current.outerHTML = data.identity;
      } else {
        var toolbar = qs('.reimu-comment-toolbar', form);
        if (toolbar) {
          toolbar.insertAdjacentHTML('beforebegin', data.identity);
        }
      }
      qsa('.reimu-comment-login', form).forEach(function (login) {
        login.remove();
      });
      qsa('[data-comment-upload-login]', form).forEach(function (notice) {
        notice.hidden = true;
      });
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea) {
        initCommentUploadRows(form, textarea);
      }
    });
    if (data.profileModal && !qs('#reimu-profile-modal')) {
      document.body.insertAdjacentHTML('beforeend', data.profileModal);
    }
    if (data.loginModal && !qs('#reimu-login-modal')) {
      document.body.insertAdjacentHTML('beforeend', data.loginModal);
    }
    initProfileModal();
    initLoginModal();
    initCommentAjaxLogout();
    var profileModal = qs('#reimu-profile-modal');
    if (profileModal && data.profile && profileModal._reimuApplyProfilePayload) {
      var stillPending = profileModal._reimuApplyProfilePayload(data.profile, { autohide: false });
      var hasPending = profileModal._reimuHasPendingProfileStatus && profileModal._reimuHasPendingProfileStatus(data.profile.reviewStatuses);
      if ((stillPending || hasPending) && profileModal._reimuStartProfileStatusPolling) {
        profileModal._reimuStartProfileStatusPolling();
      }
    }
    return true;
  }

  function applyCommentLoggedOutState(data) {
    qsa('.reimu-comment-form').forEach(function (form) {
      form.classList.remove('reimu-comment-form--logged-in');
      var guestFieldsHtml = data && data.guestFieldsHtml ? String(data.guestFieldsHtml) : '';
      if (guestFieldsHtml) {
        var existingFields = qs('.reimu-comment-form__fields', form);
        if (existingFields) {
          existingFields.outerHTML = guestFieldsHtml;
        } else {
          var commentField = qs('.comment-form-comment', form);
          if (commentField) {
            commentField.insertAdjacentHTML('beforebegin', guestFieldsHtml);
          } else {
            form.insertAdjacentHTML('afterbegin', guestFieldsHtml);
          }
        }
      }
      qsa('.reimu-comment-form__fields', form).forEach(function (fields) {
        fields.hidden = false;
      });
      var identity = qs('.reimu-comment-current-user', form);
      if (identity) {
        identity.remove();
      }
      var actions = qs('.reimu-comment-actions', form);
      if (actions) {
        var existingLogin = qs('.reimu-comment-login', actions);
        var wordCount = qs('.reimu-comment-word-count', actions);
        var loginUrl = data && data.loginUrl ? data.loginUrl : '#reimu-login-modal';
        var loginHtml = data && data.loginHtml ? String(data.loginHtml) : '<a class="reimu-comment-login-link" href="' + escapeHtml(loginUrl) + '">' + escapeHtml(t('login', '登录')) + '</a>';
        var replacement = document.createElement('span');
        replacement.className = 'reimu-comment-login';
        replacement.innerHTML = loginHtml;
        if (existingLogin) {
          existingLogin.replaceWith(replacement);
        } else if (wordCount) {
          actions.insertBefore(replacement, wordCount.nextSibling);
        } else {
          actions.insertBefore(replacement, actions.firstChild);
        }
      }
      qsa('[data-comment-upload-login]', form).forEach(function (notice) {
        notice.hidden = true;
      });
    });
    if (config.commentUploads) {
      Object.assign(config.commentUploads, data && data.commentUploads ? data.commentUploads : {});
      config.commentUploads.isLoggedIn = false;
      config.commentUploads.nonce = '';
    }
    qsa('.reimu-comment-form').forEach(function (form) {
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea) {
        initCommentUploadRows(form, textarea);
      }
    });
    initCommentLoginTriggers();
    return true;
  }

  function initProfileOpenDelegation() {
    if (document.documentElement.dataset.profileOpenDelegationReady) {
      return;
    }
    document.documentElement.dataset.profileOpenDelegationReady = 'true';
    document.addEventListener('click', function (event) {
      var trigger = event.target && event.target.closest ? event.target.closest('[data-reimu-profile-open]') : null;
      if (!trigger) {
        return;
      }
      event.preventDefault();
      function openModal() {
        var modal = qs('#reimu-profile-modal');
        if (!modal) {
          return false;
        }
        initProfileModal();
        if (modal._reimuSetProfileOpen) {
          modal._reimuSetProfileOpen(true);
        } else {
          modal.classList.add('show');
          modal.setAttribute('aria-hidden', 'false');
          modal.hidden = false;
          modal.inert = false;
        }
        return true;
      }
      if (openModal()) {
        return;
      }
      refreshCommentLoginState().then(openModal);
    });
  }

  function refreshCommentLoginState() {
    if (!config.login || !config.login.ajaxUrl) {
      return Promise.resolve(false);
    }
    var formData = new FormData();
    formData.append('action', 'yneko_reimu_login_state');
    formData.append('redirect_to', window.location.href || '');
    return fetch(config.login.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    }).then(function (response) {
      return response.json().catch(function () {
        return { success: false };
      });
    }).then(function (payload) {
      var updated = false;
      if (payload && payload.success && payload.data) {
        updated = payload.data.loggedIn ? applyCommentLoggedInState(payload.data) : applyCommentLoggedOutState(payload.data);
      }
      return updated;
    }).catch(function () {
      return false;
    });
  }

  function openAuthPopup(url, name, width, height) {
    width = width || 560;
    height = height || 720;
    var left = Math.max(0, Math.round((window.screen.width - width) / 2));
    var top = Math.max(0, Math.round((window.screen.height - height) / 2));
    var features = [
      'popup=yes',
      'width=' + width,
      'height=' + height,
      'left=' + left,
      'top=' + top,
      'resizable=yes',
      'scrollbars=yes'
    ].join(',');
    var popup = window.open(url, name || 'yneko_reimu_auth', features);
    if (popup && popup.focus) {
      popup.focus();
      return true;
    }
    return false;
  }

  function initGithubPopupLogin() {
    if (document.documentElement.dataset.githubPopupLoginReady) {
      return;
    }
    document.documentElement.dataset.githubPopupLoginReady = 'true';

    window.addEventListener('message', function (event) {
      var expectedOrigin = window.location.origin;
      if (event.origin !== expectedOrigin) {
        return;
      }
      var data = event.data || {};
      if (!data || data.type !== 'yneko-reimu-github-login') {
        return;
      }
      setLoginModalOpen(false);
      refreshCommentLoginState().then(function (updated) {
        showTooltip(t('loginSuccess', updated ? '登录成功。' : '登录成功，正在刷新...'));
        if (!updated) {
          window.setTimeout(function () {
            window.location.reload();
          }, 380);
        }
      });
    });

    document.addEventListener('click', function (event) {
      var link = event.target && event.target.closest ? event.target.closest('[data-reimu-github-popup]') : null;
      if (!link) {
        return;
      }
      event.preventDefault();
      if (!openAuthPopup(link.href, 'yneko_reimu_github_login', 560, 720)) {
        window.location.href = link.href;
      }
    });
  }

  function initAuthPopupLinks() {
    if (document.documentElement.dataset.authPopupLinksReady) {
      return;
    }
    document.documentElement.dataset.authPopupLinksReady = 'true';
    document.addEventListener('click', function (event) {
      var link = event.target && event.target.closest ? event.target.closest('[data-reimu-auth-popup]') : null;
      if (!link) {
        return;
      }
      event.preventDefault();
      if (!openAuthPopup(link.href, 'yneko_reimu_wp_auth', 520, 680)) {
        window.location.href = link.href;
      }
    });
  }

  function initCommentLoginTriggers() {
    qsa('.reimu-comment-login-link').forEach(function (link) {
      if (link.dataset.loginTriggerReady) {
        return;
      }
      link.dataset.loginTriggerReady = 'true';
      link.setAttribute('data-no-pjax', '');
      link.addEventListener('click', function (event) {
        if (!qs('#reimu-login-modal')) {
          return;
        }
        event.preventDefault();
        setLoginModalOpen(true);
      });
    });
  }

  function initCommentAjaxLogout() {
    qsa('[data-reimu-ajax-logout]').forEach(function (link) {
      if (link.dataset.ajaxLogoutReady) {
        return;
      }
      link.dataset.ajaxLogoutReady = 'true';
      link.addEventListener('click', function (event) {
        if (!config.login || !config.login.ajaxUrl || !config.login.logoutNonce) {
          return;
        }
        event.preventDefault();
        if (link.classList.contains('is-loading')) {
          return;
        }
        link.classList.add('is-loading');
        var formData = new FormData();
        formData.append('action', 'yneko_reimu_logout');
        formData.append('nonce', config.login.logoutNonce || '');
        formData.append('redirect_to', window.location.href || '');
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false };
          });
        }).then(function (payload) {
          if (!payload || !payload.success) {
            window.location.href = link.href;
            return;
          }
          applyCommentLoggedOutState(payload.data || {});
          document.body.classList.remove('logged-in', 'admin-bar');
          var profileModal = qs('#reimu-profile-modal');
          if (profileModal) {
            profileModal.remove();
          }
          if (payload.data && payload.data.loginModal && !qs('#reimu-login-modal')) {
            document.body.insertAdjacentHTML('beforeend', payload.data.loginModal);
          }
          initLoginModal();
          initCommentLoginTriggers();
          showTooltip(payload.data && payload.data.message ? payload.data.message : t('logoutSuccess', '已退出登录。'));
        }).catch(function () {
          window.location.href = link.href;
        }).finally(function () {
          link.classList.remove('is-loading');
        });
      });
    });
  }

  function initWordPressCommentForm() {
    var respond = qs('#respond');
    var respondPlaceholder = qs('#reimu-respond-placeholder');
    if (respond && !respondPlaceholder) {
      respondPlaceholder = document.createElement('span');
      respondPlaceholder.id = 'reimu-respond-placeholder';
      respondPlaceholder.hidden = true;
      respondPlaceholder.setAttribute('aria-hidden', 'true');
      if (respond.parentNode) {
        respond.parentNode.insertBefore(respondPlaceholder, respond);
      }
    }

    function moveRespondBack() {
      respond = qs('#respond');
      if (!respond) {
        return;
      }
      if (respondPlaceholder && respondPlaceholder.parentNode) {
        respondPlaceholder.parentNode.insertBefore(respond, respondPlaceholder.nextSibling);
      } else if (qs('#comment-panel-wordpress')) {
        qs('#comment-panel-wordpress').insertBefore(respond, qs('#comment-panel-wordpress .reimu-comment-list-header'));
      }
      respond.classList.remove('reimu-respond-inline');
      respond.removeAttribute('aria-label');
      var liveParentInput = qs('#comment_parent', respond);
      if (liveParentInput) {
        liveParentInput.value = '0';
      }
      var textarea = qs('#comment', respond);
      if (textarea && textarea.value.charAt(0) === '@') {
        textarea.value = textarea.value.replace(/^@[^\s]+\s*/, '');
        dispatchInputEvent(textarea);
      }
    }

    function ensureReplyCancelButton() {
      var liveRespond = qs('#respond');
      if (!liveRespond || qs('[data-reimu-cancel-reply]', liveRespond)) {
        return;
      }
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'reimu-comment-cancel';
      button.setAttribute('aria-label', t('cancelReply', '取消回复'));
      button.setAttribute('data-reimu-cancel-reply', 'true');
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        moveRespondBack();
      });
      liveRespond.insertBefore(button, liveRespond.firstChild);
    }

    function placeRespondForReply(link) {
      var liveRespond = qs('#respond');
      var textarea = qs('#comment');
      var liveParentInput = qs('#comment_parent');
      var item = link.closest('.reimu-comment');
      var content = item ? qs('.reimu-comment__content', item) : null;
      var commentId = item ? String(item.id || '').replace('comment-', '') : '';
      if (liveParentInput && commentId) {
        liveParentInput.value = commentId;
      }
      if (liveRespond && content && liveRespond.parentNode !== content) {
        content.appendChild(liveRespond);
      }
      if (liveRespond) {
        liveRespond.classList.add('reimu-respond-inline');
        liveRespond.setAttribute('aria-label', t('replyComment', '回复评论'));
      }
      if (textarea && !textarea.value) {
        var author = qs('.reimu-comment__author', item);
        var name = author ? author.textContent.trim().replace(/\s+/g, '') : '';
        if (name) {
          textarea.value = '@' + name + ' ';
          dispatchInputEvent(textarea);
        }
      }
      return { respond: liveRespond, textarea: textarea, content: content };
    }

    qsa('.reimu-comment-form').forEach(function (form) {
      if (form.dataset.wpCommentReady) {
        return;
      }
      form.dataset.wpCommentReady = 'true';
      var textarea = qs('textarea[name="comment"]', form);
      var counter = qs('[data-comment-word-count]', form);
      if (textarea && counter) {
        var updateCount = function () {
          counter.textContent = String(commentTextForCount(textarea.value).length);
        };
        textarea.addEventListener('input', updateCount);
        textarea.addEventListener('change', updateCount);
        updateCount();
      }
      initProfileOpenDelegation();
      initCommentTools(form);
      initAjaxCommentSubmit(form);
    });
    ensureReplyCancelButton();

    initCommentSorting();
    initCommentLikes();
    initCommentOwnerActions();
    initLoginModal();
    initProfileOpenDelegation();
    initProfileModal();
    initGithubPopupLogin();
    initAuthPopupLinks();
    initCommentLoginTriggers();
    initCommentAjaxLogout();

    qsa('#comments .comment-reply-link').forEach(function (link) {
      if (link.dataset.reimuReplyReady) {
        return;
      }
      link.dataset.reimuReplyReady = 'true';
      link.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var placement = placeRespondForReply(link);
        window.setTimeout(function () {
          placement = placeRespondForReply(link);
        }, 90);
        var liveRespond = placement.respond;
        var textarea = placement.textarea;
        if (liveRespond && liveRespond.scrollIntoView) {
          liveRespond.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (textarea && textarea.focus) {
          window.setTimeout(function () {
            textarea.focus({ preventScroll: true });
          }, 30);
        }
      });
    });

    var cancel = qs('#cancel-comment-reply-link');
    if (cancel && !cancel.dataset.reimuCancelReady) {
      cancel.dataset.reimuCancelReady = 'true';
      cancel.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        moveRespondBack();
        window.setTimeout(moveRespondBack, 20);
      });
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

  function samePath(urlA, urlB) {
    return urlA.origin === urlB.origin && urlA.pathname.replace(/\/+$/, '') === urlB.pathname.replace(/\/+$/, '') && urlA.search === urlB.search;
  }

  function isSamePageHashUrl(url) {
    return !!(url && url.hash && samePath(url, new URL(window.location.href)));
  }

  function scrollToHash(hash, options) {
    var target = getHeadingFromHash(hash);
    if (!target) {
      return false;
    }
    return scrollHeadingIntoView(target, options && options.instant ? 'auto' : 'smooth');
  }

  function isAssetPath(pathname) {
    return /\.(?:7z|avi|avif|bmp|css|csv|docx?|eot|gif|gz|ico|jpeg|jpg|js|json|m4a|m4v|mov|mp3|mp4|ogg|ogv|pdf|png|rar|svg|tar|ttf|txt|wav|webm|webp|woff2?|xlsx?|xml|zip)$/i.test(pathname);
  }

  function shouldPjaxLink(anchor, event) {
    if (!config.pjax || !anchor || !anchor.href) {
      return false;
    }
    if (event && (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || (typeof event.button === 'number' && event.button > 0))) {
      return false;
    }
    if (anchor.dataset.noPjax !== undefined || anchor.closest('[data-no-pjax]')) {
      return false;
    }
    if (anchor.target && anchor.target !== '_self') {
      return false;
    }
    if (anchor.hasAttribute('download')) {
      return false;
    }
    var href = anchor.getAttribute('href') || '';
    if (/^(?:mailto|tel|sms|javascript|data|blob|vbscript):/i.test(href)) {
      return false;
    }
    var url;
    try {
      url = new URL(anchor.href, window.location.href);
    } catch (error) {
      return false;
    }
    if (url.origin !== window.location.origin) {
      return false;
    }
    if (url.protocol !== 'http:' && url.protocol !== 'https:') {
      return false;
    }
    if (isAssetPath(url.pathname)) {
      return false;
    }
    if (/\/(?:wp-admin|wp-login\.php|wp-json|xmlrpc\.php)(?:\/|$)/i.test(url.pathname)) {
      return false;
    }
    if (/\/(?:feed|comments\/feed)(?:\/|$)/i.test(url.pathname) || /[?&](?:feed|preview|customize_changeset_uuid|replytocom)=/i.test(url.search)) {
      return false;
    }
    if (isSamePageHashUrl(url)) {
      return false;
    }
    return !(url.href === window.location.href || (samePath(url, new URL(window.location.href)) && url.hash === window.location.hash));
  }

  function syncHeadMetadata(nextDoc) {
    var nextHtmlLang = nextDoc.documentElement && nextDoc.documentElement.getAttribute('lang');
    if (nextHtmlLang) {
      document.documentElement.setAttribute('lang', nextHtmlLang);
    }
    var nextCanonical = qs('link[rel="canonical"]', nextDoc);
    var currentCanonical = qs('link[rel="canonical"]');
    if (nextCanonical && currentCanonical) {
      currentCanonical.setAttribute('href', nextCanonical.getAttribute('href') || '');
    }
    ['description', 'keywords'].forEach(function (name) {
      var selector = 'meta[name="' + name + '"]';
      var nextMeta = qs(selector, nextDoc);
      var currentMeta = qs(selector);
      if (nextMeta && currentMeta) {
        currentMeta.setAttribute('content', nextMeta.getAttribute('content') || '');
      } else if (nextMeta && !currentMeta) {
        document.head.appendChild(nextMeta.cloneNode(true));
      } else if (!nextMeta && currentMeta) {
        currentMeta.remove();
      }
    });
  }

  function syncInlineConfig(nextDoc) {
    qsa('script', nextDoc).forEach(function (script) {
      var text = script.textContent || '';
      if (text.indexOf('window.REIMU_CONFIG=') !== -1) {
        try {
          Function(text)();
          config = window.REIMU_CONFIG || config;
        } catch (error) {
          if (window.console && window.console.warn) {
            window.console.warn('[Yneko-Reimu] failed to sync page config', error);
          }
        }
      }
      if (text.indexOf('window.REIMU_HEATMAP_CONFIG') !== -1) {
        try {
          window.REIMU_HEATMAP_CONFIG = undefined;
          Function(text)();
        } catch (error) {
          if (window.console && window.console.warn) {
            window.console.warn('[Yneko-Reimu] failed to sync heatmap config', error);
          }
        }
      }
    });
    if (!qs('#heatmap', nextDoc)) {
      window.REIMU_HEATMAP_CONFIG = undefined;
    }
  }

  function hideHeatmapTooltip() {
    var tooltip = qs('#heatmap-tooltip');
    if (tooltip) {
      tooltip.style.display = 'none';
      tooltip.style.visibility = '';
      tooltip.innerHTML = '';
    }
  }

  function replayPjaxScripts(nextDoc) {
    qsa('#wrap script, #mobile-nav script, .site-search script', nextDoc).forEach(function (script) {
      var src = script.getAttribute('src') || '';
      var text = script.textContent || '';
      if (!src && text.indexOf('window.REIMU_HEATMAP_CONFIG') !== -1) {
        return;
      }
      var copy = document.createElement('script');
      Array.prototype.slice.call(script.attributes || []).forEach(function (attr) {
        copy.setAttribute(attr.name, attr.value);
      });
      if (src && qsa('script[src]').some(function (existing) { return existing.getAttribute('src') === src; })) {
        return;
      }
      if (text) {
        copy.text = text;
      }
      (document.body || document.head).appendChild(copy);
    });
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

  function getAuthModalState() {
    var loginModal = qs('#reimu-login-modal');
    var profileModal = qs('#reimu-profile-modal');
    var activePanel = loginModal ? qs('[data-login-panel].is-active', loginModal) : null;
    return {
      loginOpen: !!(loginModal && loginModal.classList.contains('show')),
      loginPanel: activePanel ? activePanel.getAttribute('data-login-panel') || 'login' : 'login',
      profileOpen: !!(profileModal && profileModal.classList.contains('show'))
    };
  }

  function restoreAuthModalState(state) {
    if (!state) {
      return;
    }
    initLoginModal();
    initProfileModal();
    var loginModal = qs('#reimu-login-modal');
    if (loginModal && state.loginOpen) {
      setLoginModalOpen(true);
      if (loginModal._reimuSetLoginPanel) {
        loginModal._reimuSetLoginPanel(state.loginPanel || 'login');
      }
    }
    var profileModal = qs('#reimu-profile-modal');
    if (profileModal && state.profileOpen) {
      if (profileModal._reimuSetProfileOpen) {
        profileModal._reimuSetProfileOpen(true);
      } else {
        profileModal.classList.add('show');
        profileModal.setAttribute('aria-hidden', 'false');
        profileModal.hidden = false;
        profileModal.inert = false;
      }
    }
  }

  function replacePageContent(nextDoc) {
    var authModalState = getAuthModalState();
    var preservedAPlayer = preserveAPlayer();
    if (preservedAPlayer && preservedAPlayer.parentNode) {
      preservedAPlayer.parentNode.removeChild(preservedAPlayer);
    }

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
    replaceElement('#footer', nextDoc);
    replaceElement('#reimu-login-modal', nextDoc, { appendTo: qs('#container') || document.body });
    replaceElement('#reimu-profile-modal', nextDoc, { appendTo: qs('#container') || document.body, keepMissing: true });

    if (preservedAPlayer) {
      placeAPlayerInSlot(preservedAPlayer);
    }

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

    document.body.className = nextDoc.body.className;
    document.body.classList.remove('mobile-nav-on');
    document.body.classList.add('reimu-page-loading');
    document.body.setAttribute('aria-busy', 'true');
    syncHeadMetadata(nextDoc);
    syncInlineConfig(nextDoc);
    restoreAuthModalState(authModalState);
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
      initCommentSelector,
      initWordPressCommentForm,
      initSponsor,
      initShare,
      initLoadMore,
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
    hideLoader: hideLoader
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReimu);
  } else {
    initReimu();
  }
}());
