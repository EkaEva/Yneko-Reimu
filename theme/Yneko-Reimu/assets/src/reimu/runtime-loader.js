export function createLazyRuntimeLoader(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var getAssetBaseUrl = deps.getAssetBaseUrl;
  var promises = {};

  function loadRuntime(key, options) {
    options = options || {};
    var globalName = options.globalName;
    var initMethod = options.initMethod || 'init';
    var scriptId = options.scriptId;
    var scriptName = options.scriptName;
    var label = options.label || key;
    var runtime = globalName ? window[globalName] : null;
    if (runtime && typeof runtime[initMethod] === 'function') {
      return Promise.resolve(runtime);
    }
    if (promises[key]) {
      return promises[key];
    }
    promises[key] = new Promise(function (resolve, reject) {
      var existing = qs('#' + scriptId);
      var existingRuntime = globalName ? window[globalName] : null;
      if (existing && existingRuntime) {
        resolve(existingRuntime);
        return;
      }
      var script = existing || document.createElement('script');
      script.id = scriptId;
      script.async = true;
      script.onload = function () {
        var loadedRuntime = globalName ? window[globalName] : null;
        if (loadedRuntime && typeof loadedRuntime[initMethod] === 'function') {
          resolve(loadedRuntime);
        } else {
          reject(new Error(label + ' runtime did not register.'));
        }
      };
      script.onerror = function () {
        reject(new Error('Unable to load ' + label + ' runtime.'));
      };
      if (!existing) {
        script.src = getAssetBaseUrl() + scriptName;
        (document.head || document.body || document.documentElement).appendChild(script);
      }
    }).catch(function (error) {
      promises[key] = null;
      throw error;
    });
    return promises[key];
  }

  function reset(key) {
    promises[key] = null;
  }

  return {
    loadRuntime: loadRuntime,
    reset: reset
  };
}
