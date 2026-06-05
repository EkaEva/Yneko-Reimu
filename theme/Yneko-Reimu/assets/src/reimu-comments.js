import { createCore } from './reimu/core.js';
import { createCommentsProfileRuntime } from './reimu/comments-profile.js';

(function () {
  'use strict';

  var runtime = null;

  function createRuntime() {
    var config = window.REIMU_CONFIG || {};
    var core = createCore(config);

    return createCommentsProfileRuntime({
      getConfig: function () {
        return window.REIMU_CONFIG || {};
      },
      qs: core.qs,
      qsa: core.qsa,
      t: function (key, fallback) {
        var i18n = (window.REIMU_CONFIG && window.REIMU_CONFIG.i18n) || {};
        return i18n[key] || fallback;
      },
      escapeHtml: core.escapeHtml,
      dispatchInputEvent: core.dispatchInputEvent,
      storageGet: core.storageGet,
      storageSet: core.storageSet,
      storageRemove: core.storageRemove,
      showTooltip: window.ReimuWP && typeof window.ReimuWP.showTooltip === 'function'
        ? window.ReimuWP.showTooltip
        : function () {},
      revealViewportAos: window.ReimuWP && typeof window.ReimuWP.revealViewportAos === 'function'
        ? window.ReimuWP.revealViewportAos
        : function () {},
      requestThemeConfirm: window.ReimuWP && typeof window.ReimuWP.confirm === 'function'
        ? window.ReimuWP.confirm
        : function (message) {
          return Promise.resolve(typeof globalThis.confirm === 'function' ? globalThis.confirm(message) : false);
        },
      getBody: function () {
        return document.body;
      }
    });
  }

  function getRuntime() {
    if (!runtime) {
      runtime = createRuntime();
    }
    runtime.syncConfig();
    return runtime;
  }

  function init() {
    var module = getRuntime();
    [
      module.initCommentSelector,
      module.initWordPressCommentForm,
      module.initLoadMore
    ].forEach(function (initializer) {
      try {
        initializer();
      } catch (error) {
        if (window.console && window.console.warn) {
          window.console.warn('[Yneko-Reimu] skipped comments init:', initializer.name, error);
        }
      }
    });
  }

  window.ReimuCommentsRuntime = {
    init: init,
    syncConfig: function () {
      getRuntime().syncConfig();
    },
    initCommentSelector: function () {
      getRuntime().initCommentSelector();
    },
    initWordPressCommentForm: function () {
      getRuntime().initWordPressCommentForm();
    },
    initLoadMore: function () {
      getRuntime().initLoadMore();
    },
    initLoginModal: function () {
      getRuntime().initLoginModal();
    },
    initProfileModal: function () {
      getRuntime().initProfileModal();
    },
    setLoginModalOpen: function (open) {
      getRuntime().setLoginModalOpen(open);
    },
    refreshCommentLoginState: function () {
      return getRuntime().refreshCommentLoginState();
    }
  };
}());
