export function createShareModule(deps) {
  var qs = deps.qs;
  var qsa = deps.qsa;
  var getAssetBaseUrl = deps.getAssetBaseUrl;
  var qrCodeScriptPromise = null;
  var assetBaseUrl = getAssetBaseUrl ? getAssetBaseUrl() : '';

  function parseShareData(wrapper) {
    if (!wrapper) {
      return {};
    }
    try {
      return JSON.parse(wrapper.getAttribute('data-reimu-share') || '{}') || {};
    } catch (error) {
      return {};
    }
  }

  function setShareText(node, value) {
    if (node) {
      node.textContent = String(value || '');
    }
  }

  function loadQrCodeModule() {
    if (window.QRCode && typeof window.QRCode.toDataURL === 'function') {
      return Promise.resolve(window.QRCode);
    }

    if (!qrCodeScriptPromise) {
      qrCodeScriptPromise = new Promise(function (resolve, reject) {
        var existing = qs('script[data-reimu-qrcode]');
        if (existing) {
          existing.addEventListener('load', function () {
            resolve(window.QRCode);
          }, { once: true });
          existing.addEventListener('error', reject, { once: true });
          return;
        }

        var script = document.createElement('script');
        script.src = (assetBaseUrl || '') + 'qrcode.js';
        script.async = true;
        script.defer = true;
        script.dataset.reimuQrcode = 'true';
        script.addEventListener('load', function () {
          resolve(window.QRCode);
        }, { once: true });
        script.addEventListener('error', function () {
          reject(new Error('Failed to load qrcode.js'));
        }, { once: true });
        document.head.appendChild(script);
      })
        .catch(function (error) {
          qrCodeScriptPromise = null;
          throw error;
        });
    }

    return qrCodeScriptPromise;
  }

  function renderShareQr(qr, url) {
    if (!qr || !url) {
      return;
    }
    if (qr.dataset.qrUrl === url && qr.getAttribute('src')) {
      return;
    }

    qr.dataset.qrUrl = url;
    qr.removeAttribute('src');
    qr.setAttribute('aria-busy', 'true');

    loadQrCodeModule()
      .then(function (QRCode) {
        if (!QRCode || typeof QRCode.toDataURL !== 'function') {
          throw new Error('QRCode.toDataURL is unavailable');
        }
        return QRCode.toDataURL(url, {
          errorCorrectionLevel: 'M',
          margin: 1,
          width: 180
        });
      })
      .then(function (dataUrl) {
        if (qr.dataset.qrUrl === url) {
          qr.src = dataUrl;
        }
      })
      .catch(function (error) {
        if (window.console && window.console.warn) {
          window.console.warn('[Yneko-Reimu] failed to generate share QR:', error);
        }
      })
      .then(function () {
        qr.removeAttribute('aria-busy');
      });
  }

  function fillWeixinShareCard(wrapper, popup) {
    var data = parseShareData(wrapper);
    var url = data.url || window.location.href;
    var banner = qs('[data-share-weixin-banner]', popup);
    var qr = qs('[data-share-weixin-qr]', popup);

    if (banner && data.image) {
      banner.src = data.image;
    }
    renderShareQr(qr, url);

    setShareText(qs('[data-share-weixin-title]', popup), data.title || document.title);
    setShareText(qs('[data-share-weixin-desc]', popup), data.description || '');
    setShareText(qs('[data-share-weixin-author]', popup), data.author ? 'By: ' + data.author : '');
    setShareText(qs('[data-share-weixin-theme]', popup), data.theme || 'Powered By Yneko-Reimu');
  }

  function closeWeixinShare(except) {
    qsa('[data-share-weixin-popup].active').forEach(function (popup) {
      if (popup === except) {
        return;
      }
      popup.classList.remove('active');
      popup.setAttribute('aria-hidden', 'true');
    });
  }

  function initShare() {
    qsa('.share-wrapper').forEach(function (wrapper) {
      if (wrapper.dataset.shareReady) {
        return;
      }
      wrapper.dataset.shareReady = 'true';

      qsa('[data-share-service="weixin"]', wrapper).forEach(function (link) {
        link.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          var popup = qs('[data-share-weixin-popup]', link);
          if (!popup) {
            return;
          }
          var willOpen = !popup.classList.contains('active');
          closeWeixinShare(popup);
          if (willOpen) {
            fillWeixinShareCard(wrapper, popup);
          }
          popup.classList.toggle('active', willOpen);
          popup.setAttribute('aria-hidden', willOpen ? 'false' : 'true');
        });
      });
    });

    if (!document.documentElement.dataset.shareDelegated) {
      document.documentElement.dataset.shareDelegated = 'true';
      document.addEventListener('click', function (event) {
        if (event.target.closest && event.target.closest('.share-link-weixin')) {
          return;
        }
        closeWeixinShare();
      });
    }
  }


  return {
    initShare: initShare
  };
}
