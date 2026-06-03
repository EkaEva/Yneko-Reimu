export function createPhotoSwipeModule(deps) {
  var t = deps.t;
  var qs = deps.qs;
  var qsa = deps.qsa;
  var escapeHtml = deps.escapeHtml;

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

  return {
    init: preparePhotoSwipeImages,
    destroy: closePhotoSwipeOverlay
  };
}
