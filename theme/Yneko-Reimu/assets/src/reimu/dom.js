export function qs(selector, parent) {
  return (parent || document).querySelector(selector);
}

export function qsa(selector, parent) {
  return Array.prototype.slice.call((parent || document).querySelectorAll(selector));
}

export function getAssetBaseUrl() {
  var script = document.currentScript || qs('script[src*="/assets/dist/reimu.js"]');
  var src = script ? script.getAttribute('src') || '' : '';
  if (!src) {
    return '';
  }
  try {
    return new URL('.', new URL(src, window.location.href)).href;
  } catch (error) {
    return src.replace(/[^/]*$/, '');
  }
}

export function escapeHtml(value) {
  return String(value || '').replace(/[&<>"']/g, function (char) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[char];
  });
}
