import { createCore } from './reimu/core.js';
import { createSearchModule } from './reimu/search.js';

(function () {
  'use strict';

  function createRuntimeModule() {
    var config = window.REIMU_CONFIG || {};
    var core = createCore(config);
    return createSearchModule({
      config: config,
      t: function (key, fallback) {
        var i18n = config.i18n || {};
        return i18n[key] || fallback;
      },
      qs: core.qs,
      qsa: core.qsa,
      escapeHtml: core.escapeHtml,
      debounce: core.debounce,
      dispatchReimuEvent: core.dispatchReimuEvent,
      getBody: function () { return document.body; },
      setBody: function () {}
    });
  }

  window.ReimuSearchRuntime = {
    init: function () {
      var module = createRuntimeModule();
      module.initSearch();
    },
    open: function () {
      var module = createRuntimeModule();
      module.openSearch();
    }
  };
}());
