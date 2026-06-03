export function createSearchModule(deps) {
  var config = deps.config;
  var t = deps.t;
  var qs = deps.qs;
  var qsa = deps.qsa;
  var escapeHtml = deps.escapeHtml;
  var debounce = deps.debounce;
  var dispatchReimuEvent = deps.dispatchReimuEvent;
  var getBody = deps.getBody || function () { return document.body; };
  var setBody = deps.setBody || function () {};

  function initSearch() {
    var open = qs('#nav-search-btn');
    var wrapper = qs('.site-search');
    var popup = qs('.site-search .popup');
    var close = qs('.popup-btn-close');
    var form = qs('#reimu-search-input');
    var input = qs('#reimu-search-input input');
    var hits = qs('#reimu-hits');
    var stats = qs('#reimu-stats');
    if (!open || !popup || !wrapper) {
      return;
    }

    if (!popup.classList.contains('show')) {
      wrapper.setAttribute('aria-hidden', 'true');
      wrapper.hidden = true;
      popup.inert = true;
    }

    var unlockSearchBackground = function () {
      var liveBody = document.body;
      var mask = qs('#mask');
      var container = qs('#container');
      var header = qs('#header-nav');
      if (liveBody) {
        liveBody.classList.remove('search-popup-on');
        liveBody.style.overflow = '';
      }
      if (mask && mask.dataset.mode === 'search') {
        mask.classList.add('hide');
        mask.dataset.mode = '';
      }
      if (container) {
        container.style.marginRight = '';
      }
      if (header) {
        header.style.marginRight = '';
      }
    };

    var lockSearchBackground = function () {
      var liveBody = document.body;
      var mask = qs('#mask');
      var container = qs('#container');
      var header = qs('#header-nav');
      var scrollWidth = Math.max(0, window.innerWidth - document.documentElement.offsetWidth);
      if (container) {
        container.style.marginRight = scrollWidth ? scrollWidth + 'px' : '';
      }
      if (header) {
        header.style.marginRight = scrollWidth ? scrollWidth + 'px' : '';
      }
      if (mask) {
        mask.dataset.mode = 'search';
        mask.classList.remove('hide');
      }
      if (liveBody) {
        liveBody.classList.add('search-popup-on');
        liveBody.style.overflow = 'hidden';
      }
    };

    var trapSearchFocus = function (event) {
      var livePopup = qs('.site-search .popup');
      if (!livePopup || !livePopup.classList.contains('show')) {
        return;
      }
      if (event.key === 'Escape') {
        closePopup();
        return;
      }
      if (event.key !== 'Tab') {
        return;
      }
      var focusables = qsa('input, button, [href], select, textarea, [tabindex]:not([tabindex="-1"]), [role="button"]', livePopup).filter(function (item) {
        return !item.disabled && item.offsetParent !== null;
      });
      if (!focusables.length) {
        return;
      }
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    };

    var openPopup = function () {
      var liveWrapper = qs('.site-search');
      var livePopup = qs('.site-search .popup');
      var liveInput = qs('#reimu-search-input input');
      if (!liveWrapper || !livePopup) {
        return;
      }
      var eventDetail = { source: 'search' };
      dispatchReimuEvent('reimu:search-open', eventDetail);
      setBody(document.body || getBody());
      var liveBodyForOpen = getBody();
      if (liveBodyForOpen) {
        liveBodyForOpen.classList.remove('mobile-nav-on');
      }
      liveWrapper.hidden = false;
      liveWrapper.setAttribute('aria-hidden', 'false');
      livePopup.inert = false;
      liveWrapper.classList.add('active');
      livePopup.classList.add('show');
      lockSearchBackground();
      document.removeEventListener('keydown', trapSearchFocus);
      document.addEventListener('keydown', trapSearchFocus);
      if (liveInput) {
        window.setTimeout(function () {
          liveInput.focus();
        }, 100);
      }
    };
    var closePopup = function () {
      var liveWrapper = qs('.site-search');
      var livePopup = qs('.site-search .popup');
      if (liveWrapper) {
        liveWrapper.classList.remove('active');
      }
      if (livePopup) {
        livePopup.classList.remove('show');
        livePopup.inert = true;
      }
      if (liveWrapper) {
        liveWrapper.setAttribute('aria-hidden', 'true');
        window.setTimeout(function () {
          if (!liveWrapper.classList.contains('active')) {
            liveWrapper.hidden = true;
          }
        }, 220);
      }
      unlockSearchBackground();
      document.removeEventListener('keydown', trapSearchFocus);
      dispatchReimuEvent('reimu:search-close', { source: 'search' });
      var liveOpen = qs('#nav-search-btn');
      if (liveOpen) {
        liveOpen.focus();
      }
    };

    window.ReimuSearchClose = closePopup;

    if (!open.dataset.searchReady) {
      open.dataset.searchReady = 'true';
      open.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        event.reimuSearchHandled = true;
        openPopup();
      });
      open.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          openPopup();
        }
      });
    }
    if (!document.documentElement.dataset.searchDelegated) {
      document.documentElement.dataset.searchDelegated = 'true';
      document.addEventListener('click', function (event) {
        if (event.reimuSearchHandled) {
          return;
        }
        var trigger = event.target.closest('.popup-trigger, #nav-search-btn');
        if (!trigger) {
          return;
        }
        event.preventDefault();
        openPopup();
      });
      document.addEventListener('keydown', function (event) {
        var livePopup = qs('.site-search .popup');
        if (event.key === 'Escape' && livePopup && livePopup.classList.contains('show')) {
          closePopup();
        }
      });
    }
    var mask = qs('#mask');
    if (mask && !mask.dataset.searchReady) {
      mask.dataset.searchReady = 'true';
      mask.addEventListener('click', function () {
        if (mask.dataset.mode === 'search') {
          closePopup();
        }
      });
    }
    if (close && !close.dataset.searchReady) {
      close.dataset.searchReady = 'true';
      close.addEventListener('click', function (event) {
        event.preventDefault();
        closePopup();
      });
    }
    if (!wrapper.dataset.searchReady) {
      wrapper.dataset.searchReady = 'true';
      wrapper.addEventListener('click', function (event) {
        if (event.target === wrapper) {
          closePopup();
        }
      });
    }

    if (!form || !input || !hits || !stats || !config.search || form.dataset.searchFormReady) {
      return;
    }
    form.dataset.searchFormReady = 'true';

    form.addEventListener('submit', function (event) {
      if (config.search.type !== 'fallback') {
        event.preventDefault();
      }
    });

    var renderHits = function (items, keyword, total) {
      if (!keyword) {
        stats.textContent = config.searchHint || t('searchHint', '输入关键词后按回车搜索。');
        hits.innerHTML = '';
        return;
      }
      if (typeof total === 'number') {
        stats.textContent = total ? (config.searchStatsText || t('searchStats', '找到 {count} 条结果')).replace('{count}', total) : (config.searchEmptyText || t('searchEmpty', '未发现与「{query}」相关内容')).replace('{query}', keyword);
      } else {
        stats.textContent = items.length ? (config.searchStatsText || t('searchStats', '找到 {count} 条结果')).replace('{count}', items.length) : t('searchNoResults', '没有结果');
      }
      hits.innerHTML = items.length ? items.map(function (item) {
        var title = escapeHtml(getLocalText(item, 'title') || item.post_title || item.name || t('searchUntitled', '无标题'));
        var url = item.url || item.permalink || item.path || '#';
        var type = item.subtype || item.type || '';
        return '<a class="reimu-hit-item-link" href="' + escapeHtml(url) + '">' + title + (type ? '<span class="reimu-hit-type">' + escapeHtml(type) + '</span>' : '') + '</a>';
      }).join('') : '<div id="reimu-hits-empty">' + escapeHtml(t('searchNoResults', '没有结果')) + '</div>';
    };

    var renderPagination = function (page, totalPages, keyword, onPage) {
      var pagination = qs('#reimu-pagination');
      if (!pagination) {
        return;
      }
      if (!keyword || totalPages < 2) {
        pagination.innerHTML = '';
        return;
      }
      var html = '';
      for (var i = 1; i <= totalPages; i += 1) {
        html += '<span class="pagination-item' + (i === page ? ' current' : '') + '"><a href="#" class="page-number" data-page="' + i + '">' + i + '</a></span>';
      }
      pagination.innerHTML = html;
      qsa('a[data-page]', pagination).forEach(function (link) {
        link.addEventListener('click', function (event) {
          event.preventDefault();
          onPage(Number(link.getAttribute('data-page')) || 1);
        });
      });
    };

    var getLocalText = function (item, key) {
      var value = item ? item[key] : '';
      if (Array.isArray(value)) {
        return value.map(function (entry) {
          return typeof entry === 'object' ? (entry.name || entry.slug || entry.title || '') : entry;
        }).join(' ');
      }
      if (value && typeof value === 'object') {
        return Object.keys(value).map(function (name) { return value[name]; }).join(' ');
      }
      return value || '';
    };

    if (config.search.type === 'wordpress' && config.search.restUrl) {
      var activeController = null;
      var perPage = Number(config.search.perPage || 10);
      var wordpressSearch = function (page) {
        var keyword = input.value.trim();
        if (!keyword) {
          renderHits([], '');
          renderPagination(1, 0, '', wordpressSearch);
          return;
        }
        stats.textContent = config.searchingText || t('searching', '少女检索中...');
        if (activeController) {
          activeController.abort();
        }
        activeController = 'AbortController' in window ? new AbortController() : null;
        var url = new URL(config.search.restUrl, window.location.origin);
        url.searchParams.set('search', keyword);
        url.searchParams.set('per_page', String(perPage));
        url.searchParams.set('page', String(page || 1));
        url.searchParams.set('subtype', 'post');
        if (config.search.language) {
          url.searchParams.set('reimu_language', config.search.language);
        }
        fetch(url.toString(), {
          credentials: 'same-origin',
          signal: activeController ? activeController.signal : undefined
        }).then(function (response) {
          var total = Number(response.headers.get('X-WP-Total') || 0);
          var totalPages = Number(response.headers.get('X-WP-TotalPages') || 0);
          return response.json().then(function (items) {
            renderHits(Array.isArray(items) ? items : [], keyword, total);
            renderPagination(page || 1, totalPages, keyword, wordpressSearch);
          });
        }).catch(function (error) {
          if (error && error.name === 'AbortError') {
            return;
          }
          stats.textContent = config.searchHint || t('searchHint', '输入关键词后按回车搜索。');
        });
      };

      input.addEventListener('input', debounce(function () {
        wordpressSearch(1);
      }, 220));
      return;
    }

    if (config.search.type === 'fallback') {
      form.addEventListener('submit', function () {
        closePopup();
      });
      return;
    }

    if (config.search.type === 'local' && config.search.localUrl) {
      var localIndex = [];
      var localPerPage = Number(config.search.perPage || 10);
      fetch(config.search.localUrl, { credentials: 'same-origin' }).then(function (response) {
        return response.json();
      }).then(function (payload) {
        localIndex = Array.isArray(payload) ? payload : (payload.posts || payload.pages || payload.data || []);
      }).catch(function () {
        stats.textContent = t('searchIndexFailed', '本地搜索索引加载失败。');
      });
      var localSearch = function (page) {
        var keyword = input.value.trim().toLowerCase();
        if (!keyword) {
          renderHits([], '');
          renderPagination(1, 0, '', localSearch);
          return;
        }
        var result = localIndex.filter(function (item) {
          return [
            getLocalText(item, 'title'),
            getLocalText(item, 'content'),
            getLocalText(item, 'text'),
            getLocalText(item, 'excerpt'),
            getLocalText(item, 'tags'),
            getLocalText(item, 'categories')
          ].join(' ').toLowerCase().indexOf(keyword) !== -1;
        });
        var currentPage = Number(page || 1);
        var totalPages = Math.ceil(result.length / localPerPage);
        renderHits(result.slice((currentPage - 1) * localPerPage, currentPage * localPerPage), keyword, result.length);
        renderPagination(currentPage, totalPages, keyword, localSearch);
      };
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        localSearch(1);
      });
      input.addEventListener('input', debounce(function () {
        localSearch(1);
      }, 220));
      return;
    }

    if (config.search.type === 'algolia' && window.algoliasearch && config.search.appId && config.search.apiKey && config.search.indexName) {
      var client = window.algoliasearch(config.search.appId, config.search.apiKey);
      var index = client.initIndex(config.search.indexName);
      input.addEventListener('input', function () {
        var keyword = input.value.trim();
        if (!keyword) {
          renderHits([], keyword);
          return;
        }
        stats.textContent = config.searchingText || t('searching', '少女检索中...');
        index.search(keyword, { hitsPerPage: 12 }).then(function (result) {
          renderHits(result.hits || [], keyword, result.nbHits);
        }).catch(function () {
          stats.textContent = t('searchIndexFailed', '本地搜索索引加载失败。');
        });
      });
    }
  }


  return {
    initSearch: initSearch
  };
}
