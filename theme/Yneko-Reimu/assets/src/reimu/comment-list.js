export function createCommentList(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var revealViewportAos = typeof deps.revealViewportAos === 'function' ? deps.revealViewportAos : function () {};

  function commentHotScore(item) {
    if (!item) {
      return 0;
    }
    var parentLikes = Number(item.dataset.commentLikes || 0);
    var replyItems = qsa('.children .reimu-comment', item);
    var replyLikes = 0;
    var latestTime = Number(item.dataset.commentTime || 0);
    replyItems.forEach(function (reply) {
      replyLikes += Number(reply.dataset.commentLikes || 0);
      latestTime = Math.max(latestTime, Number(reply.dataset.commentTime || 0));
    });
    var activeTime = latestTime > 0 ? latestTime : Number(item.dataset.commentTime || 0);
    var ageHours = activeTime > 0 ? Math.max(0, (Date.now() / 1000 - activeTime) / 3600) : 0;
    var baseScore = parentLikes * 4 + replyLikes * 2 + replyItems.length * 3 + 1;
    return baseScore / Math.pow(1 + ageHours / 72, 0.35);
  }

  function commentLatestActivityTime(item) {
    if (!item) {
      return 0;
    }
    var latestTime = Number(item.dataset.commentTime || 0);
    qsa('.children .reimu-comment', item).forEach(function (reply) {
      latestTime = Math.max(latestTime, Number(reply.dataset.commentTime || 0));
    });
    return latestTime;
  }

  function getLoadMoreItems(rootElement) {
    if (!rootElement) {
      return [];
    }
    return qsa(':scope > [data-reimu-loadmore-item]', rootElement).concat(qsa(':scope > .reimu-comment', rootElement));
  }

  function syncLoadMoreRoot(rootElement) {
    if (!rootElement) {
      return;
    }
    var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
    var visible = Math.max(batch, Number(rootElement.getAttribute('data-reimu-loadmore-visible') || batch));
    var items = getLoadMoreItems(rootElement);
    var targetId = rootElement.id ? '#' + rootElement.id : '';
    var button = targetId ? qs('[data-reimu-loadmore-target="' + targetId + '"]') : null;
    var buttonWrap = button ? button.closest('.reimu-load-more-wrap') : null;
    items.forEach(function (item, index) {
      var hidden = index >= visible;
      item.hidden = hidden;
      item.classList.toggle('reimu-loadmore-hidden', hidden);
    });
    if (button) {
      var hasMore = visible < items.length;
      button.hidden = !hasMore;
      button.disabled = !hasMore;
      button.classList.toggle('is-end', !hasMore);
      button.textContent = button.dataset.labelMore || t('loadMore', '加载更多...');
      button.setAttribute('aria-disabled', hasMore ? 'false' : 'true');
      if (buttonWrap) {
        buttonWrap.hidden = !hasMore;
      }
    }
  }

  function initLoadMore() {
    qsa('[data-reimu-loadmore-root]').forEach(function (rootElement) {
      var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
      if (!rootElement.getAttribute('data-reimu-loadmore-visible')) {
        rootElement.setAttribute('data-reimu-loadmore-visible', String(batch));
      }
      syncLoadMoreRoot(rootElement);
    });

    qsa('[data-reimu-loadmore-target]').forEach(function (button) {
      if (button.dataset.loadmoreReady) {
        return;
      }
      button.dataset.loadmoreReady = 'true';
      button.addEventListener('click', function () {
        if (button.disabled || button.classList.contains('is-end')) {
          return;
        }
        var rootElement = qs(button.getAttribute('data-reimu-loadmore-target'));
        if (!rootElement) {
          return;
        }
        var batch = Math.max(1, Number(rootElement.getAttribute('data-reimu-loadmore-batch') || 12));
        var visible = Math.max(batch, Number(rootElement.getAttribute('data-reimu-loadmore-visible') || batch));
        rootElement.setAttribute('data-reimu-loadmore-visible', String(visible + batch));
        syncLoadMoreRoot(rootElement);
        revealViewportAos();
      });
    });
  }

  function sortCommentList(mode) {
    var list = qs('#comments .reimu-comment-list');
    if (!list) {
      return;
    }
    var items = qsa(':scope > .reimu-comment', list);
    items.sort(function (a, b) {
      if (mode === 'hot') {
        return commentHotScore(b) - commentHotScore(a) || commentLatestActivityTime(b) - commentLatestActivityTime(a) || Number(a.dataset.commentTime || 0) - Number(b.dataset.commentTime || 0);
      }
      if (mode === 'desc') {
        return Number(b.dataset.commentTime || 0) - Number(a.dataset.commentTime || 0);
      }
      return Number(a.dataset.commentTime || 0) - Number(b.dataset.commentTime || 0);
    });
    items.forEach(function (item) {
      list.appendChild(item);
    });
    syncLoadMoreRoot(list);
  }

  function getActiveCommentSortMode() {
    var active = qs('#comments [data-comment-sort].active');
    return active ? (active.getAttribute('data-comment-sort') || 'asc') : 'asc';
  }

  function initCommentSorting() {
    qsa('#comments [data-comment-sort]').forEach(function (button) {
      if (button.dataset.commentSortReady) {
        return;
      }
      button.dataset.commentSortReady = 'true';
      button.addEventListener('click', function () {
        qsa('#comments [data-comment-sort]').forEach(function (item) {
          item.classList.remove('active');
        });
        button.classList.add('active');
        sortCommentList(button.getAttribute('data-comment-sort') || 'asc');
      });
    });
  }

  return {
    commentHotScore: commentHotScore,
    commentLatestActivityTime: commentLatestActivityTime,
    getLoadMoreItems: getLoadMoreItems,
    syncLoadMoreRoot: syncLoadMoreRoot,
    initLoadMore: initLoadMore,
    sortCommentList: sortCommentList,
    getActiveCommentSortMode: getActiveCommentSortMode,
    initCommentSorting: initCommentSorting
  };
}
