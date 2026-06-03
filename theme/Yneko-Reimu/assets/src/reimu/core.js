import { escapeHtml, getAssetBaseUrl, qs, qsa } from './dom.js';
import { debounce, dispatchInputEvent, dispatchReimuEvent } from './events.js';
import { storageGet, storageRemove, storageSet } from './storage.js';

export function createCore(config) {
  function trimSlashes(value) {
    return String(value || '').replace(/^\/+|\/+$/g, '');
  }

  function relativePathFromUrl(url) {
    var path = url && url.pathname ? url.pathname : '/';
    var homePath = '/';
    try {
      homePath = new URL(config.homeUrl || '/', window.location.origin).pathname || '/';
    } catch (error) {}
    homePath = homePath.replace(/\/+$/, '') || '/';
    if (homePath !== '/' && path.indexOf(homePath + '/') === 0) {
      path = path.slice(homePath.length);
    } else if (homePath !== '/' && path === homePath) {
      path = '/';
    }
    return trimSlashes(path);
  }

  function languageFromUrl(url) {
    var prefix = trimSlashes(config.i18nPrefix || 'en');
    var path = relativePathFromUrl(url);
    return prefix && (path === prefix || path.indexOf(prefix + '/') === 0) ? 'en_US' : 'zh_CN';
  }

  return {
    storageGet: storageGet,
    storageSet: storageSet,
    storageRemove: storageRemove,
    qs: qs,
    qsa: qsa,
    getAssetBaseUrl: getAssetBaseUrl,
    escapeHtml: escapeHtml,
    debounce: debounce,
    dispatchReimuEvent: dispatchReimuEvent,
    dispatchInputEvent: dispatchInputEvent,
    trimSlashes: trimSlashes,
    relativePathFromUrl: relativePathFromUrl,
    languageFromUrl: languageFromUrl
  };
}

export { escapeHtml, getAssetBaseUrl, qs, qsa } from './dom.js';
export { debounce, dispatchInputEvent, dispatchReimuEvent } from './events.js';
export { storageGet, storageRemove, storageSet } from './storage.js';
