import { createCore } from './reimu/core.js';
import { createShareModule } from './reimu/share.js';

(function () {
  'use strict';

  function createRuntimeModule() {
    var config = window.REIMU_CONFIG || {};
    var core = createCore(config);
    return createShareModule({
      qs: core.qs,
      qsa: core.qsa,
      getAssetBaseUrl: core.getAssetBaseUrl
    });
  }

  window.ReimuShareRuntime = {
    init: function () {
      var module = createRuntimeModule();
      module.initShare();
    }
  };
}());
