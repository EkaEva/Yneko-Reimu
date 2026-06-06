export function createCommentMutations(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var escapeHtml = deps.escapeHtml;
  var dispatchInputEvent = deps.dispatchInputEvent;
  var showTooltip = deps.showTooltip;
  var requestThemeConfirm = deps.requestThemeConfirm;
  var resolveCommentMediaTokens = deps.resolveCommentMediaTokens;
  var commentMediaLimitOk = deps.commentMediaLimitOk;
  var setCommentToolState = deps.setCommentToolState;
  var closeCommentPopovers = deps.closeCommentPopovers;
  var sortCommentList = deps.sortCommentList;
  var getActiveCommentSortMode = deps.getActiveCommentSortMode;
  var syncLoadMoreRoot = deps.syncLoadMoreRoot;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var initWordPressCommentForm = deps.initWordPressCommentForm || function () {};

  function updateCommentCount(count, label) {
    var config = getConfig();
    var title = qs('#comments .reimu-comment-count');
    if (!title || typeof count === 'undefined') {
      return;
    }
    title.textContent = label || (String(count) + ' ' + (config.language === 'en_US' ? 'Comments' : '评论'));
  }

  function clearCommentForm(form) {
    var textarea = qs('textarea[name="comment"]', form);
    if (textarea) {
      textarea.value = '';
      if (textarea._reimuCommentMedia) {
        textarea._reimuCommentMedia = {
          index: 0,
          items: {}
        };
      }
      dispatchInputEvent(textarea);
    }
    qsa('[data-comment-upload-status]', form).forEach(function (status) {
      status.textContent = '';
    });
    var preview = qs('[data-comment-preview-panel]', form);
    if (preview) {
      preview.hidden = true;
      preview.classList.remove('is-open');
    }
    setCommentToolState(form, 'preview', false);
    closeCommentPopovers(form);
  }

  function appendSubmittedComment(html, approved, parentId) {
    var list = qs('#reimu-comment-list');
    if (!list || !html) {
      return null;
    }
    var template = document.createElement('template');
    template.innerHTML = html.trim();
    var item = template.content.firstElementChild;
    if (!item) {
      return null;
    }
    if (!approved) {
      item.classList.add('reimu-comment-pending');
    }
    var parent = parentId ? qs('#comment-' + parentId) : null;
    if (parent) {
      var children = qs(':scope > .children', parent);
      if (!children) {
        children = document.createElement('ol');
        children.className = 'children';
        parent.appendChild(children);
      }
      children.appendChild(item);
    } else {
      list.hidden = false;
      list.appendChild(item);
    }
    var empty = qs('#comments .reimu-comment-empty');
    if (empty) {
      empty.hidden = true;
    }
    if (!parent) {
      sortCommentList(getActiveCommentSortMode());
    }
    initCommentLikes();
    initCommentOwnerActions();
    initWordPressCommentForm();
    syncLoadMoreRoot(list);
    return item;
  }

  function initAjaxCommentSubmit(form) {
    if (form.dataset.ajaxCommentReady) {
      return;
    }
    form.dataset.ajaxCommentReady = 'true';
    form.addEventListener('submit', function (event) {
      var config = getConfig();
      if (!config.login || !config.login.ajaxUrl || !config.comments || !config.comments.nonce) {
        return;
      }
      event.preventDefault();
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea && !textarea.value.trim()) {
        showTooltip(t('commentEmpty', '还没有评论，来抢一张小板凳吧。'));
        textarea.focus();
        return;
      }
      if (textarea && !commentMediaLimitOk(textarea.value)) {
        showTooltip(t('commentMediaLimitOne', '一条评论最多只能添加一张图片或一个 GIF。'));
        textarea.focus();
        return;
      }
      var submit = qs('.reimu-comment-submit', form) || qs('[type="submit"]', form);
      if (submit && submit.disabled) {
        return;
      }
      var originalText = submit ? submit.textContent : '';
      var formData = new FormData(form);
      if (textarea) {
        formData.set('comment', resolveCommentMediaTokens(textarea.value, textarea));
      }
      formData.append('action', 'yneko_reimu_submit_comment');
      formData.append('nonce', config.comments.nonce || '');
      if (submit) {
        submit.disabled = true;
        submit.classList.add('is-loading');
        submit.setAttribute('aria-busy', 'true');
        submit.setAttribute('data-original-text', originalText);
      }
      fetch(config.login.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      }).then(function (response) {
        var contentType = response.headers && response.headers.get ? response.headers.get('content-type') || '' : '';
        if (contentType.indexOf('application/json') === -1) {
          return response.text().then(function (text) {
            var message = String(text || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            return { success: false, data: { message: message || t('commentSubmitFailed', '评论提交失败。') } };
          });
        }
        return response.json().catch(function () {
          return { success: false, data: { message: t('commentSubmitFailed', '评论提交失败。') } };
        });
      }).then(function (payload) {
        if (!payload || !payload.success || !payload.data) {
          var message = payload && payload.data && payload.data.message ? payload.data.message : t('commentSubmitFailed', '评论提交失败。');
          showTooltip(message);
          return;
        }
        var item = appendSubmittedComment(payload.data.html || '', !!payload.data.approved, Number(payload.data.parent_id || 0));
        updateCommentCount(payload.data.count, payload.data.count_label);
        clearCommentForm(form);
        var liveRespond = qs('#respond');
        if (liveRespond && liveRespond.classList.contains('reimu-respond-inline')) {
          var cancel = qs('[data-reimu-cancel-reply]', liveRespond) || qs('#cancel-comment-reply-link');
          if (cancel) {
            cancel.click();
          }
        }
        showTooltip(payload.data.message || (!!payload.data.approved ? t('commentSubmitSuccess', '评论已发布。') : t('commentSubmitPending', '评论已提交，正在等待审核。')));
        if (!payload.data.approved) {
          var profileModal = qs('#reimu-profile-modal');
          if (profileModal && profileModal._reimuSetInlineProfileStatuses) {
            profileModal._reimuSetInlineProfileStatuses([{ text: t('commentsPending', '评论审核中'), state: 'pending', status: 'pending', type: 'comments', count: 1 }]);
          } else if (profileModal && profileModal._reimuSetInlineProfileStatus) {
            profileModal._reimuSetInlineProfileStatus(t('commentsPending', '评论审核中'), 'pending', 'comments', 1);
          }
          if (profileModal && profileModal._reimuStartProfileStatusPolling) {
            profileModal._reimuStartProfileStatusPolling();
          }
        }
        if (item && item.scrollIntoView) {
          window.setTimeout(function () {
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }, 80);
        }
      }).catch(function () {
        showTooltip(t('commentSubmitFailed', '评论提交失败。'));
      }).finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.classList.remove('is-loading');
          submit.removeAttribute('aria-busy');
          submit.textContent = submit.getAttribute('data-original-text') || originalText;
          submit.removeAttribute('data-original-text');
        }
      });
    });
  }

  function getLikedComments() {
    try {
      return JSON.parse(localStorage.getItem('reimu_comment_likes') || '{}') || {};
    } catch (error) {
      return {};
    }
  }

  function setLikedComments(likes) {
    try {
      localStorage.setItem('reimu_comment_likes', JSON.stringify(likes || {}));
    } catch (error) {}
  }

  function initCommentLikes() {
    var likes = getLikedComments();
    qsa('#comments [data-comment-like]').forEach(function (button) {
      var id = button.getAttribute('data-comment-like');
      if (!id) {
        return;
      }
      var commentItem = button.closest('.reimu-comment');
      var hasServerLikedState = commentItem && typeof commentItem.dataset.commentLiked !== 'undefined';
      var serverLiked = hasServerLikedState && commentItem.dataset.commentLiked === '1';
      var liked = hasServerLikedState ? serverLiked : !!likes[id];
      button.classList.toggle('liked', liked);
      button.setAttribute('aria-pressed', liked ? 'true' : 'false');
      if (serverLiked) {
        likes[id] = 1;
      } else if (hasServerLikedState) {
        delete likes[id];
      }
      if (button.dataset.commentLikeReady) {
        return;
      }
      button.dataset.commentLikeReady = 'true';
      button.addEventListener('click', function () {
        var config = getConfig();
        if (!config.login || !config.login.ajaxUrl || button.disabled) {
          return;
        }
        var currentLikes = getLikedComments();
        var currentLiked = !!currentLikes[id];
        var formData = new FormData();
        formData.append('action', 'yneko_reimu_comment_like');
        formData.append('comment_id', id);
        formData.append('liked', currentLiked ? '1' : '0');
        formData.append('nonce', button.getAttribute('data-like-nonce') || '');
        button.disabled = true;
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json();
        }).then(function (payload) {
          if (!payload || !payload.success || !payload.data) {
            return;
          }
          var nextLiked = !!payload.data.liked;
          var count = qs('[data-like-count]', button);
          if (count) {
            count.textContent = String(payload.data.count || 0);
          }
          var commentItem = button.closest('.reimu-comment');
          if (commentItem) {
            commentItem.dataset.commentLikes = String(payload.data.count || 0);
            commentItem.dataset.commentLiked = nextLiked ? '1' : '0';
          }
          currentLikes[id] = nextLiked ? 1 : undefined;
          if (!nextLiked) {
            delete currentLikes[id];
          }
          setLikedComments(currentLikes);
          button.classList.toggle('liked', nextLiked);
          button.setAttribute('aria-pressed', nextLiked ? 'true' : 'false');
          if (getActiveCommentSortMode() === 'hot') {
            sortCommentList('hot');
          }
        }).catch(function () {}).finally(function () {
          button.disabled = false;
        });
      });
    });
  }

  function resetCommentEdit(commentItem) {
    if (!commentItem) {
      return;
    }
    var form = qs('.reimu-comment-edit-form', commentItem);
    var text = qs('.comment-text', commentItem);
    if (form) {
      form.remove();
    }
    if (text) {
      text.hidden = false;
    }
  }

  function initCommentOwnerActions() {
    qsa('#comments [data-comment-edit]').forEach(function (button) {
      if (button.dataset.commentEditReady) {
        return;
      }
      button.dataset.commentEditReady = 'true';
      button.addEventListener('click', function () {
        var commentItem = button.closest('.reimu-comment');
        var commentId = button.getAttribute('data-comment-edit') || '';
        var text = commentItem ? qs('.comment-text', commentItem) : null;
        if (!commentItem || !commentId || !text || qs('.reimu-comment-edit-form', commentItem)) {
          return;
        }
        var raw = text.getAttribute('data-comment-raw') || text.textContent || '';
        text.hidden = true;
        var form = document.createElement('form');
        form.className = 'reimu-comment-edit-form';
        form.innerHTML = '<textarea class="reimu-comment-edit-textarea" rows="4"></textarea><div class="reimu-comment-edit-actions"><button type="submit" class="reimu-comment-edit-save">' + escapeHtml(t('commentEditSave', '保存')) + '</button><button type="button" class="reimu-comment-edit-cancel">' + escapeHtml(t('commentEditCancel', '取消')) + '</button></div>';
        var textarea = qs('textarea', form);
        if (textarea) {
          textarea.value = raw;
        }
        text.insertAdjacentElement('afterend', form);
        if (textarea && textarea.focus) {
          textarea.focus();
        }
        qs('.reimu-comment-edit-cancel', form).addEventListener('click', function () {
          resetCommentEdit(commentItem);
        });
        form.addEventListener('submit', function (event) {
          var config = getConfig();
          event.preventDefault();
          if (!config.login || !config.login.ajaxUrl || !textarea || button.disabled) {
            return;
          }
          var next = textarea.value.trim();
          if (!next) {
            showTooltip(t('commentEmpty', '还没有评论，来抢一张小板凳吧。'));
            textarea.focus();
            return;
          }
          if (!commentMediaLimitOk(next)) {
            showTooltip(t('commentMediaLimitOne', '一条评论最多只能添加一张图片或一个 GIF。'));
            textarea.focus();
            return;
          }
          var submit = qs('[type="submit"]', form);
          var formData = new FormData();
          formData.append('action', 'yneko_reimu_edit_comment');
          formData.append('comment_id', commentId);
          formData.append('comment', next);
          formData.append('nonce', button.getAttribute('data-comment-manage-nonce') || '');
          button.disabled = true;
          if (submit) {
            submit.disabled = true;
          }
          fetch(config.login.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          }).then(function (response) {
            return response.json().catch(function () {
              return { success: false, data: { message: t('commentEditFailed', '评论更新失败。') } };
            });
          }).then(function (payload) {
            if (!payload || !payload.success || !payload.data) {
              showTooltip(payload && payload.data && payload.data.message ? payload.data.message : t('commentEditFailed', '评论更新失败。'));
              return;
            }
            text.innerHTML = payload.data.html || '';
            text.setAttribute('data-comment-raw', payload.data.raw || next);
            resetCommentEdit(commentItem);
            showTooltip(payload.data.message || t('commentSubmitSuccess', '评论已发布。'));
          }).catch(function () {
            showTooltip(t('commentEditFailed', '评论更新失败。'));
          }).finally(function () {
            button.disabled = false;
            if (submit) {
              submit.disabled = false;
            }
          });
        });
      });
    });

    qsa('#comments [data-comment-delete]').forEach(function (button) {
      if (button.dataset.commentDeleteReady) {
        return;
      }
      button.dataset.commentDeleteReady = 'true';
      button.addEventListener('click', function () {
        var commentItem = button.closest('.reimu-comment');
        var commentId = button.getAttribute('data-comment-delete') || '';
        if (!commentItem || !commentId || button.disabled) {
          return;
        }
        requestThemeConfirm(t('commentDeleteConfirm', '确定删除这条评论吗？'), {
          title: t('commentDeleteTitle', '删除评论'),
          okText: t('commentDeleteOk', '删除')
        }).then(function (confirmed) {
          var config = getConfig();
          if (!confirmed || button.disabled) {
            return;
          }
          var formData = new FormData();
          formData.append('action', 'yneko_reimu_delete_comment');
          formData.append('comment_id', commentId);
          formData.append('nonce', button.getAttribute('data-comment-manage-nonce') || '');
          button.disabled = true;
          fetch(config.login.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          }).then(function (response) {
            return response.json().catch(function () {
              return { success: false, data: { message: t('commentDeleteFailed', '评论删除失败。') } };
            });
          }).then(function (payload) {
            if (!payload || !payload.success || !payload.data) {
              showTooltip(payload && payload.data && payload.data.message ? payload.data.message : t('commentDeleteFailed', '评论删除失败。'));
              return;
            }
            var parentList = commentItem.parentNode;
            commentItem.remove();
            updateCommentCount(payload.data.count, payload.data.count_label);
            if (parentList && parentList.classList && parentList.classList.contains('children') && !parentList.children.length) {
              parentList.remove();
            }
            var rootList = qs('#comments .reimu-comment-list');
            if (rootList) {
              syncLoadMoreRoot(rootList);
            }
            showTooltip(payload.data.message || t('commentDeleteFailed', '评论删除失败。'));
          }).catch(function () {
            showTooltip(t('commentDeleteFailed', '评论删除失败。'));
          }).finally(function () {
            button.disabled = false;
          });
        });
      });
    });
  }

  return {
    updateCommentCount: updateCommentCount,
    clearCommentForm: clearCommentForm,
    appendSubmittedComment: appendSubmittedComment,
    initAjaxCommentSubmit: initAjaxCommentSubmit,
    initCommentLikes: initCommentLikes,
    resetCommentEdit: resetCommentEdit,
    initCommentOwnerActions: initCommentOwnerActions
  };
}
