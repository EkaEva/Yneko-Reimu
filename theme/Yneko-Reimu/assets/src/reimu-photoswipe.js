import { createCore } from './reimu/core.js';
import { createPhotoSwipeModule } from './reimu/photoswipe.js';

(function () {
  'use strict';

  function createRuntimeModule() {
    var config = window.REIMU_CONFIG || {};
    var core = createCore(config);
    return createPhotoSwipeModule({
      t: function (key, fallback) {
        var i18n = config.i18n || {};
        return i18n[key] || fallback;
      },
      qs: core.qs,
      qsa: core.qsa,
      escapeHtml: core.escapeHtml
    });
  }

  window.ReimuPhotoSwipeRuntime = {
    init: function () {
      var module = createRuntimeModule();
      module.init();
    },
    destroy: function () {
      var module = createRuntimeModule();
      module.destroy();
    }
  };
}());
