export function createPjaxUtils(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var setConfig = typeof deps.setConfig === 'function' ? deps.setConfig : function () {};
  var initLoginModal = deps.initLoginModal || function () {};
  var initProfileModal = deps.initProfileModal || function () {};
  var setLoginModalOpen = deps.setLoginModalOpen || function () {};

  function samePath(urlA, urlB) {
    return urlA.origin === urlB.origin && urlA.pathname.replace(/\/+$/, '') === urlB.pathname.replace(/\/+$/, '') && urlA.search === urlB.search;
  }

  function isSamePageHashUrl(url) {
    return !!(url && url.hash && samePath(url, new URL(window.location.href)));
  }

  function isAssetPath(pathname) {
    return /\.(?:7z|avi|avif|bmp|css|csv|docx?|eot|gif|gz|ico|jpeg|jpg|js|json|m4a|m4v|mov|mp3|mp4|ogg|ogv|pdf|png|rar|svg|tar|ttf|txt|wav|webm|webp|woff2?|xlsx?|xml|zip)$/i.test(pathname);
  }

  function shouldPjaxLink(anchor, event) {
    var config = getConfig();
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
    var config = getConfig();
    qsa('script', nextDoc).forEach(function (script) {
      var text = script.textContent || '';
      if (text.indexOf('window.REIMU_CONFIG=') !== -1) {
        try {
          Function(text)();
          config = window.REIMU_CONFIG || config;
          setConfig(config);
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

  return {
    samePath: samePath,
    isSamePageHashUrl: isSamePageHashUrl,
    isAssetPath: isAssetPath,
    shouldPjaxLink: shouldPjaxLink,
    syncHeadMetadata: syncHeadMetadata,
    syncInlineConfig: syncInlineConfig,
    replayPjaxScripts: replayPjaxScripts,
    getAuthModalState: getAuthModalState,
    restoreAuthModalState: restoreAuthModalState
  };
}
