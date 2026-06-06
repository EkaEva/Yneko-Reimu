export function createProfileStatusUi(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var ackProfileStatuses = deps.ackProfileStatuses;

  function profileStatusMessage(type, status) {
    var map = {
      avatar: {
        pending: ['avatarPending', '头像审核中'],
        updated: ['avatarUpdated', '头像已更新'],
        rejected: ['avatarRejected', '头像审核不通过']
      },
      tags: {
        pending: ['tagsPending', '标签审核中'],
        updated: ['tagsUpdated', '标签已更新'],
        rejected: ['tagsRejected', '标签审核不通过']
      },
      comments: {
        pending: ['commentsPending', '评论审核中'],
        updated: ['commentsUpdated', '评论已更新'],
        rejected: ['commentsRejected', '评论审核不通过']
      },
      profile: {
        updated: ['profileUpdatedShort', '已更新']
      }
    };
    var item = map[type] && map[type][status] ? map[type][status] : null;
    return item ? t(item[0], item[1]) : '';
  }

  function hasPendingProfileStatus(statuses) {
    statuses = statuses || {};
    return ['avatar', 'tags', 'comments'].some(function (type) {
      return statuses[type] && statuses[type].status === 'pending';
    });
  }

  function profileStatusRows(statuses) {
    statuses = statuses || {};
    var order = ['profile', 'avatar', 'tags', 'comments'];
    return order.map(function (type) {
      var item = statuses[type] || {};
      var status = item.status || '';
      var text = status ? profileStatusMessage(type, status) : '';
      if (!text) {
        return null;
      }
      return {
        type: type,
        status: status,
        text: text,
        state: status === 'rejected' ? 'error' : (status === 'updated' ? 'success' : 'pending'),
        count: Number(item.count || 0)
      };
    }).filter(Boolean);
  }

  function setInlineProfileStatus(text, state, type, count) {
    var rows = text ? [{ text: text, state: state, type: type, count: count || 0, status: state === 'pending' ? 'pending' : '' }] : [];
    setInlineProfileStatuses(rows);
  }

  function setInlineProfileStatuses(rows) {
    rows = Array.isArray(rows) ? rows : [];
    qsa('.reimu-comment-current-user').forEach(function (identity) {
      var name = qs('.reimu-comment-current-user__name', identity);
      var list = qs('[data-profile-inline-status-list]', identity);
      if (!rows.length) {
        if (list) {
          list.remove();
        }
        return;
      }
      if (!list) {
        list = document.createElement('span');
        list.className = 'reimu-comment-current-user__statuses';
        list.setAttribute('data-profile-inline-status-list', '');
        if (name) {
          name.insertAdjacentElement('afterend', list);
        } else {
          identity.appendChild(list);
        }
      }
      list.innerHTML = '';
      rows.forEach(function (row) {
        var status = document.createElement('span');
        status.className = 'reimu-comment-current-user__status';
        status.setAttribute('data-profile-inline-status', '');
        status.setAttribute('data-profile-status-kind', row.type || '');
        status.setAttribute('data-profile-status-state', row.status || row.state || '');
        status.textContent = row.text || '';
        status.classList.toggle('is-error', row.state === 'error');
        status.classList.toggle('is-success', row.state === 'success');
        status.classList.toggle('is-pending', row.state === 'pending');
        if (row.state === 'pending' && Number(row.count || 0) > 1 && (row.type === 'tags' || row.type === 'comments')) {
          var badge = document.createElement('b');
          badge.className = 'reimu-comment-current-user__status-count';
          badge.textContent = String(row.count);
          status.appendChild(badge);
        }
        list.appendChild(status);
      });
    });
  }

  function applyInlineProfileStatus(data, options) {
    options = options || {};
    var rows = profileStatusRows(data && data.reviewStatuses ? data.reviewStatuses : {});
    if (!rows.length) {
      if (options.clearEmpty) {
        setInlineProfileStatuses([]);
      }
      return false;
    }
    setInlineProfileStatuses(rows);
    var pending = rows.some(function (row) { return row.state === 'pending'; });
    var ackTypes = rows.filter(function (row) { return row.state !== 'pending'; }).map(function (row) { return row.type; });
    if (ackTypes.length && options.autohide !== false) {
      window.setTimeout(function () {
        var pendingRows = rows.filter(function (row) { return row.state === 'pending'; });
        setInlineProfileStatuses(pendingRows);
        if (ackProfileStatuses) {
          ackProfileStatuses(ackTypes);
        }
      }, 4200);
    }
    return pending;
  }

  return {
    hasPendingProfileStatus: hasPendingProfileStatus,
    profileStatusRows: profileStatusRows,
    setInlineProfileStatus: setInlineProfileStatus,
    setInlineProfileStatuses: setInlineProfileStatuses,
    applyInlineProfileStatus: applyInlineProfileStatus
  };
}
