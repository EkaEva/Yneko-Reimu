import { createCommentList } from './comment-list.js';
import { createCommentMedia } from './comment-media.js';
import { createCommentTools } from './comment-tools.js';
import { createProfileFormUi } from './profile-form.js';
import { createProfileStatusUi } from './profile-status.js';

export function createCommentsProfileRuntime(deps) {
  deps = deps || {};
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var escapeHtml = deps.escapeHtml;
  var dispatchInputEvent = deps.dispatchInputEvent;
  var storageGet = deps.storageGet;
  var storageSet = deps.storageSet;
  var storageRemove = deps.storageRemove;
  var showTooltip = deps.showTooltip;
  var revealViewportAos = deps.revealViewportAos;
  var requestThemeConfirm = deps.requestThemeConfirm;
  var getBody = typeof deps.getBody === 'function' ? deps.getBody : function () { return document.body; };
  var config = getConfig();
  var commentMedia = createCommentMedia({
    getConfig: getConfig,
    t: t,
    escapeHtml: escapeHtml,
    dispatchInputEvent: dispatchInputEvent,
    requestConfirm: requestThemeConfirm
  });
  var insertIntoTextarea = commentMedia.insertIntoTextarea;
  var resolveCommentMediaTokens = commentMedia.resolveCommentMediaTokens;
  var commentMediaEntries = commentMedia.commentMediaEntries;
  var confirmCommentMediaReplace = commentMedia.confirmCommentMediaReplace;
  var commentMediaLimitOk = commentMedia.commentMediaLimitOk;
  var commentTextForCount = commentMedia.commentTextForCount;
  var markdownToHtml = commentMedia.markdownToHtml;
  var insertCommentMedia = commentMedia.insertCommentMedia;
  var updateCommentPreview = function (form, textarea) {
    commentMedia.updateCommentPreview(form, textarea, qs);
  };

  function syncConfig() {
    config = getConfig();
    return config;
  }

  function initCommentSelector() {
    var selectors = qsa('#comments .selector-item[data-selector]');
    if (!selectors.length) {
      return;
    }
    selectors.forEach(function (selector, index) {
      if (selector.dataset.commentReady) {
        return;
      }
      selector.dataset.commentReady = 'true';
      selector.addEventListener('click', function () {
        selectors.forEach(function (item) { item.classList.remove('active'); });
        qsa('.comment-panel').forEach(function (panel) { panel.classList.remove('active'); });
        selector.classList.add('active');
        var panel = qs('#comment-panel-' + selector.getAttribute('data-selector'));
        if (panel) {
          panel.classList.add('active');
        }
      });
      if (index === 0) {
        selector.click();
      }
    });
  }

  function initCommentUploadRows(form, textarea) {
    function uploadState(type) {
      var uploads = config.commentUploads || {};
      var enabledKey = type === 'gif' ? 'gifEnabled' : 'imageEnabled';
      return {
        uploads: uploads,
        enabled: !!(uploads.enabled && uploads[enabledKey]),
        canUpload: !!(uploads.enabled && uploads[enabledKey] && uploads.isLoggedIn && uploads.nonce && config.login && config.login.ajaxUrl)
      };
    }

    qsa('[data-comment-upload-row]', form).forEach(function (row) {
      var type = row.getAttribute('data-comment-upload-row') === 'gif' ? 'gif' : 'image';
      row.hidden = !uploadState(type).canUpload;
    });
    qsa('[data-comment-upload-login]', form).forEach(function (note) {
      var type = note.getAttribute('data-comment-upload-login') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      note.hidden = state.canUpload || !state.enabled;
      note.textContent = state.enabled ? t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。') : t(type === 'gif' ? 'commentUploadGifDisabled' : 'commentUploadImageDisabled', type === 'gif' ? '评论 GIF 上传已关闭。' : '评论图片上传已关闭。');
    });
    qsa('[data-comment-upload-button]', form).forEach(function (button) {
      var type = button.getAttribute('data-comment-upload-button') === 'gif' ? 'gif' : 'image';
      button.hidden = !uploadState(type).enabled;
    });
    qsa('[data-comment-upload-input]', form).forEach(function (input) {
      var type = input.getAttribute('data-comment-upload-input') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      input.disabled = !state.canUpload;
      if (!state.canUpload) {
        input.value = '';
      }
    });
    qsa('[data-comment-upload-status]', form).forEach(function (status) {
      var type = status.getAttribute('data-comment-upload-status') === 'gif' ? 'gif' : 'image';
      var state = uploadState(type);
      if (!state.enabled || state.canUpload) {
        status.textContent = '';
      }
    });

    qsa('[data-comment-upload-button]', form).forEach(function (button) {
      if (button.dataset.commentUploadReady) {
        return;
      }
      button.dataset.commentUploadReady = 'true';
      var type = button.getAttribute('data-comment-upload-button') === 'gif' ? 'gif' : 'image';
      var input = qs('[data-comment-upload-input="' + type + '"]', form);
      if (input && !input.dataset.commentUploadInputReady) {
        input.dataset.commentUploadInputReady = 'true';
        input.addEventListener('change', function () {
          if (input.files && input.files[0]) {
            if (!uploadState(type).canUpload) {
              input.value = '';
              showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
              initCommentUploadRows(form, textarea);
              return;
            }
            var replaceConfirmed = false;
            if (commentMediaEntries(textarea ? textarea.value : '').length) {
              confirmCommentMediaReplace(textarea).then(function (confirmed) {
                if (!confirmed) {
                  input.value = '';
                  return;
                }
                replaceConfirmed = true;
                uploadCommentFile(type, input, button, textarea, form, replaceConfirmed);
              });
              return;
            }
            uploadCommentFile(type, input, button, textarea, form, replaceConfirmed);
          }
        });
      }
      button.addEventListener('click', function () {
        var state = uploadState(type);
        if (!state.enabled) {
          showTooltip(t(type === 'gif' ? 'commentUploadGifDisabled' : 'commentUploadImageDisabled', type === 'gif' ? '评论 GIF 上传已关闭。' : '评论图片上传已关闭。'));
          initCommentUploadRows(form, textarea);
          return;
        }
        if (!state.canUpload) {
          showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
          initCommentUploadRows(form, textarea);
          return;
        }
        if (input) {
          input.click();
        }
      });
    });

    function uploadCommentFile(type, input, button, textarea, form, replaceConfirmed) {
      var state = uploadState(type);
      if (!state.canUpload) {
        showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
        return;
      }

      var status = qs('[data-comment-upload-status="' + type + '"]', form);
      if (!input || !input.files || !input.files[0]) {
        showTooltip(t('commentUploadChoose', '请先选择文件。'));
        return;
      }

      var formData = new FormData();
      formData.append('action', 'yneko_reimu_comment_upload');
      formData.append('nonce', state.uploads.nonce || '');
      formData.append('type', type);
      formData.append('file', input.files[0]);
      button.disabled = true;
      if (status) {
        status.textContent = t('commentUploadUploading', '上传中...');
      }

      fetch(config.login.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      }).then(function (response) {
        return response.json().catch(function () {
          return { success: false, data: { message: t('commentUploadFailed', '上传失败。') } };
        });
      }).then(function (payload) {
        if (!payload || !payload.success || !payload.data || !payload.data.url) {
          var message = payload && payload.data && payload.data.message ? payload.data.message : t('commentUploadFailed', '上传失败。');
          if (status) {
            status.textContent = message;
          }
          showTooltip(message);
          return;
        }
        insertCommentMedia(textarea, payload.data.url, payload.data.type || type, {
          confirmedReplace: !!replaceConfirmed,
          cleanupKey: payload.data.cleanupKey || '',
          uploaded: !!payload.data.cleanupKey
        }).then(function (inserted) {
          if (!inserted) {
            return;
          }
          input.value = '';
          if (status) {
            status.textContent = Object.prototype.hasOwnProperty.call(payload.data, 'message') ? payload.data.message : t('commentUploadDone', '已插入评论。');
          }
          if (payload.data.requiresReview && payload.data.message) {
            showTooltip(payload.data.message);
          }
        });
      }).catch(function () {
        if (status) {
          status.textContent = t('commentUploadFailed', '上传失败。');
        }
        showTooltip(t('commentUploadFailed', '上传失败。'));
      }).finally(function () {
        button.disabled = false;
      });
    }
  }

  var commentTools = createCommentTools({
    qs: qs,
    qsa: qsa,
    t: t,
    escapeHtml: escapeHtml,
    showTooltip: showTooltip,
    insertIntoTextarea: insertIntoTextarea,
    insertCommentMedia: insertCommentMedia,
    resolveCommentMediaTokens: resolveCommentMediaTokens,
    markdownToHtml: markdownToHtml,
    updateCommentPreview: updateCommentPreview,
    initCommentUploadRows: initCommentUploadRows,
    getConfig: function () {
      return config;
    }
  });
  var closeCommentPopovers = commentTools.closeCommentPopovers;
  var setCommentToolState = commentTools.setCommentToolState;
  var initCommentTools = commentTools.initCommentTools;

  var commentList = createCommentList({
    qs: qs,
    qsa: qsa,
    t: t,
    revealViewportAos: function () {
      revealViewportAos();
    }
  });
  var syncLoadMoreRoot = commentList.syncLoadMoreRoot;
  var initLoadMore = commentList.initLoadMore;
  var sortCommentList = commentList.sortCommentList;
  var getActiveCommentSortMode = commentList.getActiveCommentSortMode;
  var initCommentSorting = commentList.initCommentSorting;

  function updateCommentCount(count, label) {
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

  function setLoginModalOpen(open) {
    var modal = qs('#reimu-login-modal');
    if (!modal) {
      return;
    }
    modal.classList.toggle('show', !!open);
    modal.setAttribute('aria-hidden', open ? 'false' : 'true');
    modal.hidden = !open;
    modal.inert = !open;
    var pageBody = getBody();
    if (pageBody) {
      pageBody.classList.toggle('reimu-login-on', !!open);
    }
    if (open) {
      initLoginModal();
      var activePanel = qs('[data-login-panel].is-active', modal);
      var isLoginPanel = !activePanel || activePanel.getAttribute('data-login-panel') === 'login';
      if (!isLoginPanel && modal._reimuSetLoginPanel) {
        modal._reimuSetLoginPanel('login');
      } else if (modal._reimuClearAuthMessages) {
        modal._reimuClearAuthMessages();
      }
      var input = qs('#reimu-login-user', modal);
      if (input && input.focus) {
        window.setTimeout(function () {
          input.focus();
        }, 80);
      }
    }
  }

  function initLoginModal() {
    var modal = qs('#reimu-login-modal');
    if (!modal || modal.dataset.loginModalReady) {
      return;
    }
    modal.dataset.loginModalReady = 'true';
    if (!modal.classList.contains('show')) {
      modal.hidden = true;
      modal.inert = true;
    }
    var form = qs('[data-reimu-login-form]', modal);
    var registerForm = qs('[data-reimu-register-form]', modal);
    var lostForm = qs('[data-reimu-lost-form]', modal);
    var message = qs('[data-login-message]', modal);
    var submit = qs('.reimu-login-submit', modal);
    var registerCodeTimer = null;
    var lostCodeTimer = null;

    function clearAuthMessages() {
      qsa('.reimu-login-message', modal).forEach(function (item) {
        item.textContent = '';
        item.classList.remove('error', 'success');
      });
      qsa('[data-login-2fa]', modal).forEach(function (item) {
        item.hidden = true;
        var input = qs('[name="two_factor_code"]', item);
        if (input) {
          input.value = '';
        }
      });
      qsa('[data-password-toggle]', modal).forEach(function (button) {
        var wrap = button.closest('.reimu-login-password-row');
        var input = wrap ? qs('input', wrap) : null;
        if (input) {
          input.type = 'password';
        }
        button.classList.remove('is-visible');
        button.setAttribute('aria-label', t('showPassword', '显示密码'));
      });
    }
    modal._reimuClearAuthMessages = clearAuthMessages;

    function setPanel(name) {
      name = name || 'login';
      if (name === 'register' && !registerForm) {
        name = 'login';
      }
      if (name === 'lost' && !lostForm) {
        name = 'login';
      }
      qsa('[data-login-panel]', modal).forEach(function (panel) {
        var active = panel.getAttribute('data-login-panel') === name;
        panel.hidden = !active;
        panel.classList.toggle('is-active', active);
      });
      clearAuthMessages();
      var login2fa = qs('[data-login-2fa]', modal);
      if (login2fa && name !== 'login') {
        login2fa.hidden = true;
      }
      var title = qs('#reimu-login-title', modal);
      var desc = qs('.reimu-login-modal__desc', modal);
      if (title) {
        title.textContent = name === 'register' ? t('register', '注册') : (name === 'lost' ? t('lostPassword', '忘记密码？') : t('login', '登录'));
      }
      if (desc) {
        desc.textContent = name === 'register'
          ? t('registerDesc', '验证邮箱后即可创建账号。')
          : (name === 'lost' ? t('lostPasswordDesc', '验证邮箱后即可重置密码。') : desc.getAttribute('data-login-desc') || desc.textContent);
        desc.hidden = name === 'login' && !(desc.getAttribute('data-login-desc') || '').trim();
      }
      if (name === 'login') {
        var social = qs('.reimu-login-social', modal);
        if (social) {
          social.hidden = false;
        }
      } else {
        var socialPanel = qs('.reimu-login-social', modal);
        if (socialPanel) {
          socialPanel.hidden = true;
        }
      }
    }
    modal._reimuSetLoginPanel = setPanel;

    var descNode = qs('.reimu-login-modal__desc', modal);
    if (descNode && !descNode.getAttribute('data-login-desc')) {
      descNode.setAttribute('data-login-desc', descNode.textContent);
    }

    qsa('[data-login-close]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setLoginModalOpen(false);
        setPanel('login');
      });
    });

    qsa('[data-login-panel-trigger]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setPanel(button.getAttribute('data-login-panel-trigger') || 'login');
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('show')) {
        setLoginModalOpen(false);
        setPanel('login');
      }
    });

    if (form) {
      var savedEmail = storageGet('yneko_reimu_login_email');
      var loginEmailInput = qs('[name="log"]', form);
      var rememberInput = qs('[name="rememberme"]', form);
      if (savedEmail && loginEmailInput && !loginEmailInput.value) {
        loginEmailInput.value = savedEmail;
      }
      if (savedEmail && rememberInput) {
        rememberInput.checked = true;
      }
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!config.login || !config.login.ajaxUrl) {
          return;
        }
        function requestLogin(retried) {
          var formData = new FormData(form);
          formData.append('action', 'yneko_reimu_login');
          formData.append('nonce', config.login.nonce || '');
          return fetch(config.login.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          }).then(function (response) {
            return response.json().catch(function () {
              return { success: false, data: { message: config.login.failedText || t('loginFailed', '登录失败。') } };
            });
          }).then(function (payload) {
            if (!retried && payload && !payload.success && payload.data && payload.data.loginNonce && config.login) {
              config.login.nonce = payload.data.loginNonce;
              return requestLogin(true);
            }
            return payload;
          });
        }
        if (message) {
          message.textContent = config.login.loadingText || t('loginLoading', '登录中...');
          message.classList.remove('error', 'success');
        }
        if (submit) {
          submit.disabled = true;
        }
        requestLogin(false).then(function (payload) {
          var twoFactor = qs('[data-login-2fa]', form);
          if (payload && payload.data && payload.data.loginNonce && config.login) {
            config.login.nonce = payload.data.loginNonce;
          }
          if (payload && payload.success) {
            var submittedEmail = qs('[name="log"]', form);
            var submittedRemember = qs('[name="rememberme"]', form);
            if (submittedRemember && submittedRemember.checked && submittedEmail) {
              storageSet('yneko_reimu_login_email', submittedEmail.value || '');
            } else {
              storageRemove('yneko_reimu_login_email');
            }
            if (message) {
              message.textContent = config.login.successText || t('loginSuccess', '登录成功。');
              message.classList.add('success');
            }
            refreshCommentLoginState().then(function (updated) {
              if (updated) {
                if (form) {
                  form.reset();
                }
                var twoFactor = qs('[data-login-2fa]', form);
                if (twoFactor) {
                  twoFactor.hidden = true;
                }
                setLoginModalOpen(false);
                return;
              }
              window.setTimeout(function () {
                window.location.reload();
              }, 380);
            });
            return;
          }
          var text = payload && payload.data && payload.data.message ? payload.data.message : (config.login.failedText || t('loginFailed', '登录失败。'));
          if (payload && payload.data && payload.data.requires2fa) {
            if (twoFactor) {
              twoFactor.hidden = false;
              var twoFactorInput = qs('[name="two_factor_code"]', twoFactor);
              if (twoFactorInput && twoFactorInput.focus) {
                twoFactorInput.focus();
              }
            }
          } else if (twoFactor) {
            twoFactor.hidden = true;
            var hiddenInput = qs('[name="two_factor_code"]', twoFactor);
            if (hiddenInput) {
              hiddenInput.value = '';
            }
          }
          if (message) {
            message.textContent = text;
            message.classList.add('error');
          }
        }).catch(function () {
          if (message) {
            message.textContent = config.login.failedText || t('loginFailed', '登录失败。');
            message.classList.add('error');
          }
        }).finally(function () {
          if (submit) {
            submit.disabled = false;
          }
        });
      });
    }

    qsa('[data-password-toggle]', modal).forEach(function (button) {
      if (button.dataset.passwordToggleReady) {
        return;
      }
      button.dataset.passwordToggleReady = 'true';
      button.addEventListener('click', function () {
        var wrap = button.closest('.reimu-login-password-row');
        var input = wrap ? qs('input', wrap) : null;
        if (!input) {
          return;
        }
        var visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.classList.toggle('is-visible', !visible);
        button.setAttribute('aria-label', !visible ? t('hidePassword', '隐藏密码') : t('showPassword', '显示密码'));
      });
    });

    function setCodeCountdown(button, seconds, timerSetter) {
      var remaining = Number(seconds || 60);
      var timer = null;
      if (!button) {
        return;
      }
      button.disabled = true;
      function render() {
        button.textContent = String(t('registerCodeWait', '%s 秒后重发')).replace('%s', remaining);
        remaining -= 1;
        if (remaining < 0) {
          window.clearInterval(timer);
          timerSetter(null);
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        }
      }
      render();
      timer = window.setInterval(render, 1000);
      timerSetter(timer);
    }

    function bindCodeButton(authForm, selector, action, nonceKey, fields, messageSelector, timerName) {
      if (!authForm) {
        return;
      }
      var button = qs(selector, authForm);
      var authMessage = qs(messageSelector, authForm);
      if (!button || button.dataset.registerCodeReady) {
        return;
      }
      button.dataset.registerCodeReady = 'true';
      button.setAttribute('data-label', button.textContent);
      button.addEventListener('click', function () {
        if (!config.login || !config.login.ajaxUrl || button.disabled) {
          return;
        }
        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', config.login[nonceKey] || '');
        formData.append('redirect_to', window.location.href || '');
        fields.forEach(function (fieldName) {
          var field = qs('[name="' + fieldName + '"]', authForm);
          formData.append(fieldName, field ? field.value || '' : '');
        });
        button.disabled = true;
        button.textContent = t('registerCodeSending', '发送中...');
        if (authMessage) {
          authMessage.textContent = '';
          authMessage.classList.remove('error', 'success');
        }
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          var text = payload && payload.data && payload.data.message ? payload.data.message : t('registerCodeSent', '验证码已发送，请检查您的邮箱。');
          if (authMessage) {
            authMessage.innerHTML = text;
            authMessage.classList.toggle('success', !!(payload && payload.success));
            authMessage.classList.toggle('error', !(payload && payload.success));
          }
          if (payload && payload.success) {
            if (timerName === 'lost') {
              window.clearInterval(lostCodeTimer);
              setCodeCountdown(button, 60, function (timer) {
                lostCodeTimer = timer;
              });
            } else {
              window.clearInterval(registerCodeTimer);
              setCodeCountdown(button, 60, function (timer) {
                registerCodeTimer = timer;
              });
            }
            return;
          }
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        }).catch(function () {
          if (authMessage) {
            authMessage.textContent = config.login.failedText || t('loginFailed', '操作失败。');
            authMessage.classList.add('error');
          }
          button.disabled = false;
          button.textContent = button.getAttribute('data-label') || t('sendCode', '发送验证码');
        });
      });
    }

    function bindSimpleAuthForm(authForm, action, nonceKey, messageSelector) {
      if (!authForm) {
        return;
      }
      var authMessage = qs(messageSelector, authForm);
      var authSubmit = qs('[type="submit"]', authForm);
      authForm.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!config.login || !config.login.ajaxUrl) {
          return;
        }
        var formData = new FormData(authForm);
        formData.append('action', action);
        formData.append('nonce', config.login[nonceKey] || '');
        formData.append('redirect_to', window.location.href || '');
        if (authMessage) {
          var loadingText = authForm.getAttribute('data-loading-text') || '';
          if (!loadingText) {
            loadingText = action === 'yneko_reimu_register'
              ? t('registerLoading', '注册中...')
              : (action === 'yneko_reimu_lostpassword' ? t('resetLoading', '重置中...') : (config.login.loadingText || t('loginLoading', '处理中...')));
          }
          authMessage.textContent = loadingText;
          authMessage.classList.remove('error', 'success');
        }
        if (authSubmit) {
          authSubmit.disabled = true;
        }
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          var text = payload && payload.data && payload.data.message ? payload.data.message : (config.login.failedText || t('loginFailed', '操作失败。'));
          if (authMessage) {
            authMessage.innerHTML = text;
            authMessage.classList.toggle('success', !!(payload && payload.success));
            authMessage.classList.toggle('error', !(payload && payload.success));
          }
          if (payload && payload.success && (action === 'yneko_reimu_register' || action === 'yneko_reimu_lostpassword')) {
            var registeredEmail = qs('[name="user_email"]', authForm);
            var registeredPassword = qs('[name="user_password"]', authForm);
            var registeredEmailValue = registeredEmail ? registeredEmail.value || '' : '';
            var registeredPasswordValue = registeredPassword ? registeredPassword.value || '' : '';
            var loginEmail = qs('[name="log"]', form);
            var loginPassword = qs('[name="pwd"]', form);
            if (action === 'yneko_reimu_register') {
              if (loginEmail) {
                loginEmail.value = registeredEmailValue;
              }
              if (loginPassword) {
                loginPassword.value = registeredPasswordValue;
              }
            }
            authForm.reset();
            window.setTimeout(function () {
              setPanel('login');
              if (action === 'yneko_reimu_register') {
                if (loginEmail) {
                  loginEmail.value = registeredEmailValue;
                }
                if (loginPassword) {
                  loginPassword.value = registeredPasswordValue;
                }
              }
            }, 900);
          }
        }).catch(function () {
          if (authMessage) {
            authMessage.textContent = config.login.failedText || t('loginFailed', '操作失败。');
            authMessage.classList.add('error');
          }
        }).finally(function () {
          if (authSubmit) {
            authSubmit.disabled = false;
          }
        });
      });
    }

    bindCodeButton(registerForm, '[data-register-code-send]', 'yneko_reimu_register_code', 'registerCodeNonce', ['display_name', 'user_email'], '[data-register-message]', 'register');
    bindCodeButton(lostForm, '[data-lost-code-send]', 'yneko_reimu_lostpassword_code', 'lostCodeNonce', ['user_login'], '[data-lost-message]', 'lost');
    bindSimpleAuthForm(registerForm, 'yneko_reimu_register', 'registerNonce', '[data-register-message]');
    bindSimpleAuthForm(lostForm, 'yneko_reimu_lostpassword', 'lostNonce', '[data-lost-message]');
  }

  function initProfileModal() {
    var modal = qs('#reimu-profile-modal');
    if (!modal || modal.dataset.profileReady) {
      return;
    }
    modal.dataset.profileReady = 'true';
    var form = qs('[data-reimu-profile-form]', modal);
    var message = qs('[data-profile-message]', modal);
    var emailTimer = null;
    var profileStatusTimer = null;
    var profileAvatarState = { changed: false };
    var profileTagState = { tagMessageTimer: null };
    var profileAvatarOriginalUrl = '';
    var profileStatusUi = createProfileStatusUi({
      qs: qs,
      qsa: qsa,
      t: t,
      ackProfileStatuses: ackProfileStatuses
    });
    var profileFormUi = createProfileFormUi({
      qs: qs,
      qsa: qsa,
      t: t,
      escapeHtml: escapeHtml,
      form: form
    });
    var normalizeUrlInput = profileFormUi.normalizeUrlInput;
    var validateProfilePasswords = profileFormUi.validateProfilePasswords;
    var setProfileAvatarHint = profileFormUi.setProfileAvatarHint;
    var profileAvatarUrlChanged = function () {
      return profileFormUi.profileAvatarUrlChanged(profileAvatarOriginalUrl);
    };
    var markProfileAvatarChanged = function (changed) {
      return profileFormUi.markProfileAvatarChanged(changed, profileAvatarState);
    };
    var clearProfileTagError = function () {
      profileFormUi.clearProfileTagError(profileTagState);
    };
    var showProfileTagError = function (payload) {
      profileFormUi.showProfileTagError(payload, profileTagState);
    };
    var profileEnabledCustomTagCount = profileFormUi.profileEnabledCustomTagCount;
    var profileSelectedTagCount = profileFormUi.profileSelectedTagCount;
    var enforceProfileSpecialLimit = profileFormUi.enforceProfileSpecialLimit;
    var profileCustomTagCapacity = profileFormUi.profileCustomTagCapacity;
    var profileCustomTagStorageLimit = profileFormUi.profileCustomTagStorageLimit;
    var syncProfileAddTagState = profileFormUi.syncProfileAddTagState;
    var profileTagRow = profileFormUi.profileTagRow;
    var renderProfileCustomTags = profileFormUi.renderProfileCustomTags;
    var hasPendingProfileStatus = profileStatusUi.hasPendingProfileStatus;
    var setInlineProfileStatus = profileStatusUi.setInlineProfileStatus;
    var setInlineProfileStatuses = profileStatusUi.setInlineProfileStatuses;
    var applyInlineProfileStatus = profileStatusUi.applyInlineProfileStatus;

    function setOpen(open) {
      modal.classList.toggle('show', !!open);
      modal.setAttribute('aria-hidden', open ? 'false' : 'true');
      modal.hidden = !open;
      modal.inert = !open;
      var pageBody = getBody();
      if (pageBody) {
        pageBody.classList.toggle('reimu-login-on', !!open);
      }
      if (open) {
        if (message) {
          message.textContent = '';
          message.classList.remove('error', 'success');
        }
        refreshProfile();
      } else if (form) {
        form.reset();
        validateProfilePasswords();
        clearProfileTagError();
        setProfileAvatarHint('');
        var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
        if (avatarUploadButton) {
          avatarUploadButton.textContent = t('upload', '上传');
          avatarUploadButton.disabled = false;
        }
      }
    }
    modal._reimuSetProfileOpen = setOpen;

    function setMessage(text, ok) {
      if (!message) {
        return;
      }
      message.textContent = text || '';
      message.classList.toggle('success', !!ok);
      message.classList.toggle('error', !ok && !!text);
    }

    function ackProfileStatuses(types) {
      types = Array.isArray(types) ? types.filter(Boolean) : [];
      if (!types.length || !config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
        return;
      }
      var data = new FormData();
      data.append('action', 'yneko_reimu_profile_status_ack');
      data.append('nonce', config.login.profileNonce || '');
      types.forEach(function (type) {
        data.append('types[]', type);
      });
      fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          if (payload && payload.success && payload.data && config.login) {
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
          }
        }).catch(function () {});
    }

    function applyProfilePayload(data, options) {
      if (!data) {
        return false;
      }
      options = options || {};
      var modalOpen = modal.classList.contains('show') || modal.getAttribute('aria-hidden') === 'false';
      if (options.forceFill || (options.fillProfile !== false && !modalOpen)) {
        fillProfile(data);
      }
      updateCommentBadgesForProfile(data);
      updateVisibleProfileLinks(data);
      updateVisibleProfileAvatars(data);
      return applyInlineProfileStatus(data, options);
    }

    function startProfileStatusPolling() {
      window.clearInterval(profileStatusTimer);
      profileStatusTimer = window.setInterval(function () {
        if (!config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
          return;
        }
        var data = new FormData();
        data.append('action', 'yneko_reimu_profile_get');
        data.append('nonce', config.login.profileNonce || '');
        data.append('redirect_to', window.location.href || '');
        fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
          .then(function (response) { return response.json(); })
          .then(function (payload) {
            if (!payload || !payload.success || !payload.data) {
              return;
            }
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
            var stillPending = applyProfilePayload(payload.data, { autohide: true, fillProfile: false });
            if (stillPending) {
              return;
            }
            window.clearInterval(profileStatusTimer);
          }).catch(function () {});
      }, 5000);
    }
    modal._reimuStartProfileStatusPolling = startProfileStatusPolling;
    modal._reimuApplyProfilePayload = applyProfilePayload;
    modal._reimuSetInlineProfileStatus = setInlineProfileStatus;
    modal._reimuSetInlineProfileStatuses = setInlineProfileStatuses;
    modal._reimuHasPendingProfileStatus = hasPendingProfileStatus;

    function fillProfile(data) {
      if (!data || !form) {
        return;
      }
      var fields = {
        display_name: data.displayName,
        current_email: data.email,
        user_email: '',
        avatar_url: data.avatarUrl,
        profile_url: data.profileUrl
      };
      profileAvatarOriginalUrl = data.avatarUrl || '';
      Object.keys(fields).forEach(function (name) {
        var input = qs('[name="' + name + '"]', form);
        if (input) {
          input.value = fields[name] || '';
        }
      });
      var currentEmailDisplay = qs('[data-profile-current-email-display]', form);
      if (currentEmailDisplay) {
        currentEmailDisplay.textContent = fields.current_email || '';
      }
      var twoFactor = qs('[name="totp_enabled"]', form);
      if (twoFactor) {
        twoFactor.checked = !!data.twoFactor;
      }
      var avatarFrame = qs('[name="avatar_frame_enabled"]', form);
      if (avatarFrame) {
        avatarFrame.checked = data.avatarFrameEnabled !== false;
      }
      var preview = qs('[data-profile-avatar-preview]', form);
      if (preview && data.avatarUrl) {
        preview.src = data.avatarUrl;
      }
      var status = qs('[data-profile-avatar-status]', form);
      if (status) {
        status.hidden = !data.avatarPending;
        status.textContent = data.avatarPending ? t('avatarPending', '头像审核中') : '';
      }
      var commentTags = Array.isArray(data.commentTags) ? data.commentTags : [];
      var pendingTags = Array.isArray(data.pendingCommentTags) ? data.pendingCommentTags : [];
      var activeCustomTags = commentTags.filter(function (tag) { return tag && tag.type === 'custom'; });
      var seenCustomTags = {};
      var customTags = [];
      pendingTags.concat(activeCustomTags).forEach(function (tag) {
        if (!tag || !tag.label) {
          return;
        }
        var key = String(tag.id || tag.old_id || '').trim() || (String(tag.label || '').trim().toLowerCase() + '|' + String(tag.color || '').trim().toLowerCase());
        if (seenCustomTags[key]) {
          return;
        }
        seenCustomTags[key] = true;
        customTags.push(tag);
      });
      var specialTags = commentTags.filter(function (tag) { return tag && tag.type === 'special' && tag.key; });
      specialTags.forEach(function (tag) {
        var specialInput = qs('[name="comment_special_enabled[' + tag.key + ']"]', form);
        if (specialInput) {
          specialInput.checked = tag.enabled !== '0';
        }
      });
      qsa('[name="comment_tag_label[]"]', form).forEach(function (input, index) {
        var tag = customTags[index] || null;
        input.value = tag && tag.label ? tag.label : '';
      });
      qsa('[name="comment_tag_color[]"]', form).forEach(function (input, index) {
        var tag = customTags[index] || null;
        input.value = tag && tag.color ? tag.color : '#ff5252';
      });
      renderProfileCustomTags(customTags);
      var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
      if (avatarUploadButton) {
        avatarUploadButton.textContent = t('upload', '上传');
        avatarUploadButton.disabled = false;
      }
      var avatarFileInput = qs('[data-profile-avatar-file]', form);
      if (avatarFileInput) {
        avatarFileInput.value = '';
      }
      markProfileAvatarChanged(false);
      setProfileAvatarHint('');
      validateProfilePasswords();
    }

    function updateCommentBadgesForProfile(data) {
      if (!data || !data.userId) {
        return;
      }
      var html = data.commentBadgesHtml || '';
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"]').forEach(function (item) {
        var headline = qs('.reimu-comment__headline', item);
        if (!headline) {
          return;
        }
        var existing = qs('.reimu-comment-user-tags', headline);
        if (existing) {
          if (html) {
            existing.outerHTML = html;
          } else {
            existing.remove();
          }
          return;
        }
        if (html) {
          var author = qs('.reimu-comment__author', headline);
          if (author) {
            author.insertAdjacentHTML('afterend', html);
          }
        }
      });
    }

    function updateVisibleProfileLinks(data) {
      if (!data || !data.userId) {
        return;
      }
      if (data.identity) {
        qsa('.reimu-comment-current-user').forEach(function (identity) {
          identity.outerHTML = data.identity;
        });
        initCommentAjaxLogout();
      }
      var displayName = data.displayName || '';
      var profileUrl = String(data.publicProfileUrl || '').trim();
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"]').forEach(function (item) {
        var authorWrap = qs('.reimu-comment__author', item);
        if (!authorWrap) {
          return;
        }
        if (profileUrl) {
          authorWrap.innerHTML = '<a class="reimu-comment__author-link" href="' + escapeHtml(profileUrl) + '" target="_blank" rel="noopener noreferrer nofollow">' + escapeHtml(displayName) + '</a>';
        } else {
          authorWrap.innerHTML = '<span class="reimu-comment__author-name">' + escapeHtml(displayName) + '</span>';
        }
      });
    }

    function updateVisibleProfileAvatars(data) {
      if (!data || !data.userId || !data.avatarHtml) {
        return;
      }
      if (data.identity) {
        qsa('.reimu-comment-current-user').forEach(function (identity) {
          identity.outerHTML = data.identity;
        });
        initCommentAjaxLogout();
      }
      qsa('.reimu-comment[data-comment-user-id="' + String(data.userId) + '"] .reimu-comment__avatar').forEach(function (avatar) {
        avatar.innerHTML = data.avatarHtml;
      });
    }

    function refreshProfile() {
      if (!config.login || !config.login.ajaxUrl || !config.login.profileNonce) {
        return;
      }
      var data = new FormData();
      data.append('action', 'yneko_reimu_profile_get');
      data.append('nonce', config.login.profileNonce || '');
      data.append('redirect_to', window.location.href || '');
      fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
          if (payload && payload.success) {
            if (payload.data && config.login) {
              config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
              config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
            }
            var stillPending = applyProfilePayload(payload.data, { autohide: false, forceFill: true });
            if (stillPending || hasPendingProfileStatus(payload.data && payload.data.reviewStatuses)) {
              startProfileStatusPolling();
            }
          }
        }).catch(function () {});
    }

    function postProfileAction(action, data) {
      data = data || new FormData();
      data.append('action', action);
      data.append('nonce', config.login.profileNonce || '');
      data.append('redirect_to', window.location.href || '');
      return fetch(config.login.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: config.login.failedText || t('loginFailed', '操作失败。') } };
          });
        }).then(function (payload) {
          if (payload && payload.data && config.login) {
            config.login.profileNonce = payload.data.profileNonce || config.login.profileNonce;
            config.login.logoutNonce = payload.data.logoutNonce || config.login.logoutNonce;
          }
          return payload;
        });
    }

    qsa('[data-profile-close]', modal).forEach(function (button) {
      button.addEventListener('click', function () {
        setOpen(false);
      });
    });

    document.addEventListener('click', function (event) {
      var trigger = event.target && event.target.closest ? event.target.closest('[data-reimu-profile-open]') : null;
      if (!trigger) {
        return;
      }
      event.preventDefault();
      setOpen(true);
    });

    profileFormUi.bindPasswordToggles(modal);

    var avatarUrlInput = qs('[name="avatar_url"]', form);
    if (avatarUrlInput) {
      avatarUrlInput.addEventListener('input', function () {
        markProfileAvatarChanged(profileAvatarUrlChanged());
        var preview = qs('[data-profile-avatar-preview]', form);
        if (preview && avatarUrlInput.value) {
          preview.src = avatarUrlInput.value;
        }
      });
    }

    var currentEmailInput = qs('[data-profile-current-email]', form);
    if (currentEmailInput) {
      currentEmailInput.setAttribute('tabindex', '-1');
      currentEmailInput.setAttribute('aria-readonly', 'true');
      currentEmailInput.addEventListener('focus', function () {
        currentEmailInput.blur();
      });
    }

    var addTagButton = qs('[data-profile-add-tag]', form);
    if (addTagButton) {
      addTagButton.addEventListener('click', function () {
        var list = qs('[data-profile-tag-list]', form);
        if (!list || addTagButton.disabled) {
          return;
        }
        if (qsa('.reimu-profile-tag-row', list).length >= profileCustomTagStorageLimit()) {
          syncProfileAddTagState();
          return;
        }
        list.appendChild(profileTagRow({ color: '#ff5252', enabled: profileEnabledCustomTagCount() < profileCustomTagCapacity() ? '1' : '0' }));
        syncProfileAddTagState();
      });
    }

    qsa('[name^="comment_special_enabled["]', form).forEach(function (input) {
      input.addEventListener('change', function () {
        if (input.checked) {
          enforceProfileSpecialLimit(input);
        }
        syncProfileAddTagState();
      });
    });

    form.addEventListener('click', function (event) {
      var remove = event.target && event.target.closest ? event.target.closest('[data-profile-remove-tag]') : null;
      if (!remove) {
        return;
      }
      event.preventDefault();
      var row = remove.closest('.reimu-profile-tag-row');
      if (row) {
        row.remove();
      }
      syncProfileAddTagState();
    });

    form.addEventListener('change', function (event) {
      var checkbox = event.target && event.target.matches && event.target.matches('[data-profile-tag-enabled]') ? event.target : null;
      if (!checkbox) {
        return;
      }
      if (checkbox.checked && profileSelectedTagCount() > 2) {
        checkbox.checked = false;
        showProfileTagError({ data: { message: t('commentTagLimit', '特殊标签和已勾选的自定义标签合计最多 2 个。') } });
      } else {
        clearProfileTagError();
      }
      syncProfileAddTagState();
    });

    var avatarUploadButton = qs('[data-profile-avatar-upload]', form);
    var avatarFileInput = qs('[data-profile-avatar-file]', form);
    if (avatarUploadButton && avatarFileInput) {
      avatarUploadButton.addEventListener('click', function () {
        if (avatarUploadButton.disabled) {
          return;
        }
        setProfileAvatarHint('');
        avatarFileInput.value = '';
        markProfileAvatarChanged(profileAvatarUrlChanged());
        avatarFileInput.click();
      });
      avatarFileInput.addEventListener('change', function () {
        var file = avatarFileInput.files && avatarFileInput.files[0] ? avatarFileInput.files[0] : null;
        if (!file) {
          avatarUploadButton.textContent = t('upload', '上传');
          avatarUploadButton.disabled = false;
          setProfileAvatarHint('');
          return;
        }
        avatarUploadButton.textContent = t('upload', '上传');
        var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var extOk = /\.(?:jpe?g|png|webp)$/i.test(file.name || '');
        if (allowedTypes.indexOf(file.type) === -1 && !extOk) {
          setProfileAvatarHint(t('avatarInvalidType', '头像仅支持 JPG、PNG 或 WebP。'), false);
          avatarFileInput.value = '';
          return;
        }
        var maxMb = Number(modal.getAttribute('data-avatar-max-mb') || 1);
        if (file.size > maxMb * 1024 * 1024) {
          setProfileAvatarHint(t('avatarTooLarge', '头像文件超过大小限制。'), false);
          avatarFileInput.value = '';
          return;
        }
        var preview = qs('[data-profile-avatar-preview]', form);
        if (preview && window.URL && window.URL.createObjectURL) {
          preview.src = window.URL.createObjectURL(file);
        }
        markProfileAvatarChanged(true);
        setProfileAvatarHint(t('avatarReady', '头像已选择，保存后生效。'), true);
      });
    }

    qsa('[name="new_password"], [name="new_password_confirm"]', form).forEach(function (input) {
      input.addEventListener('input', validateProfilePasswords);
    });

    var emailCodeButton = qs('[data-profile-email-code-send]', form);
    if (emailCodeButton) {
      emailCodeButton.addEventListener('click', function () {
        if (emailCodeButton.disabled) {
          return;
        }
        var data = new FormData();
        var email = qs('[name="user_email"]', form);
        var currentEmail = qs('[name="current_email"]', form);
        var newEmail = email ? String(email.value || '').trim() : '';
        var oldEmail = currentEmail ? String(currentEmail.value || '').trim() : '';
        if (!newEmail || newEmail.toLowerCase() === oldEmail.toLowerCase()) {
          setMessage(t('emailDuplicate', '新邮箱地址不要与原邮箱地址重复。'), false);
          if (email && email.focus) {
            email.focus();
          }
          return;
        }
        data.append('user_email', newEmail);
        emailCodeButton.disabled = true;
        emailCodeButton.textContent = t('registerCodeSending', '发送中...');
        postProfileAction('yneko_reimu_profile_email_code', data).then(function (payload) {
          setMessage(payload && payload.data && payload.data.message ? payload.data.message : '', payload && payload.success);
          if (!payload || !payload.success) {
            emailCodeButton.disabled = false;
            emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
            return;
          }
          var remaining = 60;
          emailCodeButton.setAttribute('data-label', emailCodeButton.getAttribute('data-label') || emailCodeButton.textContent);
          window.clearInterval(emailTimer);
          emailTimer = window.setInterval(function () {
            emailCodeButton.textContent = String(t('registerCodeWait', '%s 秒后重发')).replace('%s', remaining);
            remaining -= 1;
            if (remaining < 0) {
              window.clearInterval(emailTimer);
              emailCodeButton.disabled = false;
              emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
            }
          }, 1000);
        }).catch(function () {
          setMessage(config.login.failedText || t('loginFailed', '操作失败。'), false);
          emailCodeButton.disabled = false;
          emailCodeButton.textContent = emailCodeButton.getAttribute('data-label') || t('sendCode', '发送验证码');
        });
      });
    }

    var twoFactorToggle = qs('[data-profile-2fa-toggle]', form);
    var twoFactorSetup = qs('[data-profile-2fa-setup]', form);
    if (twoFactorToggle && twoFactorSetup) {
      twoFactorSetup.hidden = !twoFactorToggle.checked;
      twoFactorToggle.addEventListener('change', function () {
        twoFactorSetup.hidden = !twoFactorToggle.checked;
      });
    }

    var generate2fa = qs('[data-profile-2fa-generate]', form);
    if (generate2fa) {
      generate2fa.addEventListener('click', function () {
        postProfileAction('yneko_reimu_profile_totp_generate').then(function (payload) {
          if (!payload || !payload.success) {
            setMessage(payload && payload.data && payload.data.message ? payload.data.message : config.login.failedText, false);
            return;
          }
          var secret = qs('[data-profile-2fa-secret]', form);
          var qr = qs('[data-profile-2fa-qr]', form);
          if (secret) {
            secret.textContent = payload.data.secret || '';
          }
          if (qr) {
            qr.src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(payload.data.uri || '');
            qr.hidden = false;
          }
          setMessage(t('profile2faGenerated', '请用认证器扫码，并输入 6 位验证码后保存。'), true);
        });
      });
    }

    if (form) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        normalizeUrlInput(qs('[name="profile_url"]', form));
        normalizeUrlInput(qs('[name="avatar_url"]', form));
        if (!validateProfilePasswords()) {
          var confirm = qs('[name="new_password_confirm"]', form);
          setMessage(t('passwordMismatch', '两次输入的密码不一致。'), false);
          if (confirm && confirm.reportValidity) {
            confirm.reportValidity();
          }
          return;
        }
        var data = new FormData(form);
        var avatarChanged = profileAvatarState.changed;
        postProfileAction('yneko_reimu_profile_save', data).then(function (payload) {
          setMessage(payload && payload.data && payload.data.message ? payload.data.message : '', payload && payload.success);
          if (payload && payload.success) {
            clearProfileTagError();
            var stillPending = applyProfilePayload(payload.data, { autohide: true, forceFill: true });
            if (stillPending || hasPendingProfileStatus(payload.data && payload.data.reviewStatuses)) {
              startProfileStatusPolling();
            } else if (avatarChanged && payload.data && payload.data.avatarUrl) {
              applyInlineProfileStatus(payload.data, { autohide: true });
            }
            setOpen(false);
            refreshCommentLoginState();
          } else if (payload && payload.data && payload.data.field === 'comment_tag_label') {
            showProfileTagError(payload);
          } else if (payload && payload.data && payload.data.message) {
            setMessage(payload.data.message, false);
          }
        }).catch(function () {
          setMessage(config.login.failedText || t('loginFailed', '操作失败。'), false);
        });
      });
    }
    refreshProfile();
  }

  function applyCommentLoggedInState(data) {
    if (!data || !data.loggedIn || !data.identity) {
      return false;
    }
    if (config.comments) {
      config.comments.nonce = data.commentNonce || config.comments.nonce;
    }
    if (config.commentUploads) {
      Object.assign(config.commentUploads, data.commentUploads || {});
      config.commentUploads.isLoggedIn = true;
      config.commentUploads.nonce = data.commentUploadNonce || config.commentUploads.nonce || '';
    }
    if (config.login) {
      config.login.logoutNonce = data.logoutNonce || config.login.logoutNonce;
      config.login.profileNonce = data.profileNonce || config.login.profileNonce;
    }
    qsa('.reimu-comment-form').forEach(function (form) {
      form.classList.add('reimu-comment-form--logged-in');
      qsa('.reimu-comment-form__fields input', form).forEach(function (input) {
        input.value = '';
      });
      qsa('.reimu-comment-form__fields', form).forEach(function (fields) {
        fields.hidden = true;
      });
      var current = qs('.reimu-comment-current-user', form);
      if (current) {
        current.outerHTML = data.identity;
      } else {
        var toolbar = qs('.reimu-comment-toolbar', form);
        if (toolbar) {
          toolbar.insertAdjacentHTML('beforebegin', data.identity);
        }
      }
      qsa('.reimu-comment-login', form).forEach(function (login) {
        login.remove();
      });
      qsa('[data-comment-upload-login]', form).forEach(function (notice) {
        notice.hidden = true;
      });
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea) {
        initCommentUploadRows(form, textarea);
      }
    });
    if (data.profileModal && !qs('#reimu-profile-modal')) {
      document.body.insertAdjacentHTML('beforeend', data.profileModal);
    }
    if (data.loginModal && !qs('#reimu-login-modal')) {
      document.body.insertAdjacentHTML('beforeend', data.loginModal);
    }
    initProfileModal();
    initLoginModal();
    initCommentAjaxLogout();
    var profileModal = qs('#reimu-profile-modal');
    if (profileModal && data.profile && profileModal._reimuApplyProfilePayload) {
      var stillPending = profileModal._reimuApplyProfilePayload(data.profile, { autohide: false });
      var hasPending = profileModal._reimuHasPendingProfileStatus && profileModal._reimuHasPendingProfileStatus(data.profile.reviewStatuses);
      if ((stillPending || hasPending) && profileModal._reimuStartProfileStatusPolling) {
        profileModal._reimuStartProfileStatusPolling();
      }
    }
    return true;
  }

  function applyCommentLoggedOutState(data) {
    qsa('.reimu-comment-form').forEach(function (form) {
      form.classList.remove('reimu-comment-form--logged-in');
      var guestFieldsHtml = data && data.guestFieldsHtml ? String(data.guestFieldsHtml) : '';
      if (guestFieldsHtml) {
        var existingFields = qs('.reimu-comment-form__fields', form);
        if (existingFields) {
          existingFields.outerHTML = guestFieldsHtml;
        } else {
          var commentField = qs('.comment-form-comment', form);
          if (commentField) {
            commentField.insertAdjacentHTML('beforebegin', guestFieldsHtml);
          } else {
            form.insertAdjacentHTML('afterbegin', guestFieldsHtml);
          }
        }
      }
      qsa('.reimu-comment-form__fields', form).forEach(function (fields) {
        fields.hidden = false;
      });
      var identity = qs('.reimu-comment-current-user', form);
      if (identity) {
        identity.remove();
      }
      var actions = qs('.reimu-comment-actions', form);
      if (actions) {
        var existingLogin = qs('.reimu-comment-login', actions);
        var wordCount = qs('.reimu-comment-word-count', actions);
        var loginUrl = data && data.loginUrl ? data.loginUrl : '#reimu-login-modal';
        var loginHtml = data && data.loginHtml ? String(data.loginHtml) : '<a class="reimu-comment-login-link" href="' + escapeHtml(loginUrl) + '">' + escapeHtml(t('login', '登录')) + '</a>';
        var replacement = document.createElement('span');
        replacement.className = 'reimu-comment-login';
        replacement.innerHTML = loginHtml;
        if (existingLogin) {
          existingLogin.replaceWith(replacement);
        } else if (wordCount) {
          actions.insertBefore(replacement, wordCount.nextSibling);
        } else {
          actions.insertBefore(replacement, actions.firstChild);
        }
      }
      qsa('[data-comment-upload-login]', form).forEach(function (notice) {
        notice.hidden = true;
      });
    });
    if (config.commentUploads) {
      Object.assign(config.commentUploads, data && data.commentUploads ? data.commentUploads : {});
      config.commentUploads.isLoggedIn = false;
      config.commentUploads.nonce = '';
    }
    qsa('.reimu-comment-form').forEach(function (form) {
      var textarea = qs('textarea[name="comment"]', form);
      if (textarea) {
        initCommentUploadRows(form, textarea);
      }
    });
    initCommentLoginTriggers();
    return true;
  }

  function initProfileOpenDelegation() {
    if (document.documentElement.dataset.profileOpenDelegationReady) {
      return;
    }
    document.documentElement.dataset.profileOpenDelegationReady = 'true';
    document.addEventListener('click', function (event) {
      var trigger = event.target && event.target.closest ? event.target.closest('[data-reimu-profile-open]') : null;
      if (!trigger) {
        return;
      }
      event.preventDefault();
      function openModal() {
        var modal = qs('#reimu-profile-modal');
        if (!modal) {
          return false;
        }
        initProfileModal();
        if (modal._reimuSetProfileOpen) {
          modal._reimuSetProfileOpen(true);
        } else {
          modal.classList.add('show');
          modal.setAttribute('aria-hidden', 'false');
          modal.hidden = false;
          modal.inert = false;
        }
        return true;
      }
      if (openModal()) {
        return;
      }
      refreshCommentLoginState().then(openModal);
    });
  }

  function refreshCommentLoginState() {
    if (!config.login || !config.login.ajaxUrl) {
      return Promise.resolve(false);
    }
    var formData = new FormData();
    formData.append('action', 'yneko_reimu_login_state');
    formData.append('redirect_to', window.location.href || '');
    return fetch(config.login.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    }).then(function (response) {
      return response.json().catch(function () {
        return { success: false };
      });
    }).then(function (payload) {
      var updated = false;
      if (payload && payload.success && payload.data) {
        updated = payload.data.loggedIn ? applyCommentLoggedInState(payload.data) : applyCommentLoggedOutState(payload.data);
      }
      return updated;
    }).catch(function () {
      return false;
    });
  }

  function openAuthPopup(url, name, width, height) {
    width = width || 560;
    height = height || 720;
    var left = Math.max(0, Math.round((window.screen.width - width) / 2));
    var top = Math.max(0, Math.round((window.screen.height - height) / 2));
    var features = [
      'popup=yes',
      'width=' + width,
      'height=' + height,
      'left=' + left,
      'top=' + top,
      'resizable=yes',
      'scrollbars=yes'
    ].join(',');
    var popup = window.open(url, name || 'yneko_reimu_auth', features);
    if (popup && popup.focus) {
      popup.focus();
      return popup;
    }
    return null;
  }

  function initGithubPopupLogin() {
    if (document.documentElement.dataset.githubPopupLoginReady) {
      return;
    }
    document.documentElement.dataset.githubPopupLoginReady = 'true';
    var githubPopupPollTimer = null;
    var githubPopupHandled = false;

    function handleGithubLoginSuccess() {
      if (githubPopupHandled) {
        return;
      }
      githubPopupHandled = true;
      setLoginModalOpen(false);
      refreshCommentLoginState().then(function (updated) {
        showTooltip(t('loginSuccess', updated ? '登录成功。' : '登录成功，正在刷新...'));
        if (!updated) {
          window.setTimeout(function () {
            window.location.reload();
          }, 380);
        }
      });
    }

    window.addEventListener('message', function (event) {
      var expectedOrigin = window.location.origin;
      if (event.origin !== expectedOrigin) {
        return;
      }
      var data = event.data || {};
      if (!data || data.type !== 'yneko-reimu-github-login') {
        return;
      }
      handleGithubLoginSuccess();
    });

    window.addEventListener('storage', function (event) {
      if (event.key !== 'yneko-reimu-github-login' || !event.newValue) {
        return;
      }
      try {
        var data = JSON.parse(event.newValue);
        if (data && data.type === 'yneko-reimu-github-login') {
          handleGithubLoginSuccess();
        }
      } catch (error) {}
    });

    document.addEventListener('click', function (event) {
      var link = event.target && event.target.closest ? event.target.closest('[data-reimu-github-popup]') : null;
      if (!link) {
        return;
      }
      event.preventDefault();
      var popup = openAuthPopup(link.href, 'yneko_reimu_github_login', 560, 720);
      if (!popup) {
        window.location.href = link.href;
        return;
      }
      githubPopupHandled = false;
      window.clearInterval(githubPopupPollTimer);
      githubPopupPollTimer = window.setInterval(function () {
        if (popup.closed) {
          window.clearInterval(githubPopupPollTimer);
          return;
        }
        try {
          var popupLocation = popup.location.href || '';
          if (popupLocation && popupLocation.indexOf(window.location.origin) === 0) {
            var done = popup.document && popup.document.body && popup.document.body.getAttribute('data-yneko-reimu-github-login-done') === '1';
            if (done) {
              popup.close();
              window.clearInterval(githubPopupPollTimer);
              handleGithubLoginSuccess();
            }
          }
        } catch (error) {}
      }, 300);
    });
  }

  function initAuthPopupLinks() {
    if (document.documentElement.dataset.authPopupLinksReady) {
      return;
    }
    document.documentElement.dataset.authPopupLinksReady = 'true';
    document.addEventListener('click', function (event) {
      var link = event.target && event.target.closest ? event.target.closest('[data-reimu-auth-popup]') : null;
      if (!link) {
        return;
      }
      event.preventDefault();
      if (!openAuthPopup(link.href, 'yneko_reimu_wp_auth', 520, 680)) {
        window.location.href = link.href;
      }
    });
  }

  function initCommentLoginTriggers() {
    qsa('.reimu-comment-login-link').forEach(function (link) {
      if (link.dataset.loginTriggerReady) {
        return;
      }
      link.dataset.loginTriggerReady = 'true';
      link.setAttribute('data-no-pjax', '');
      link.addEventListener('click', function (event) {
        if (!qs('#reimu-login-modal')) {
          return;
        }
        event.preventDefault();
        setLoginModalOpen(true);
      });
    });
  }

  function initCommentAjaxLogout() {
    qsa('[data-reimu-ajax-logout]').forEach(function (link) {
      if (link.dataset.ajaxLogoutReady) {
        return;
      }
      link.dataset.ajaxLogoutReady = 'true';
      link.addEventListener('click', function (event) {
        if (!config.login || !config.login.ajaxUrl || !config.login.logoutNonce) {
          return;
        }
        event.preventDefault();
        if (link.classList.contains('is-loading')) {
          return;
        }
        link.classList.add('is-loading');
        var formData = new FormData();
        formData.append('action', 'yneko_reimu_logout');
        formData.append('nonce', config.login.logoutNonce || '');
        formData.append('redirect_to', window.location.href || '');
        fetch(config.login.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false };
          });
        }).then(function (payload) {
          if (!payload || !payload.success) {
            window.location.href = link.href;
            return;
          }
          applyCommentLoggedOutState(payload.data || {});
          document.body.classList.remove('logged-in', 'admin-bar');
          var profileModal = qs('#reimu-profile-modal');
          if (profileModal) {
            profileModal.remove();
          }
          if (payload.data && payload.data.loginModal && !qs('#reimu-login-modal')) {
            document.body.insertAdjacentHTML('beforeend', payload.data.loginModal);
          }
          initLoginModal();
          initCommentLoginTriggers();
          showTooltip(payload.data && payload.data.message ? payload.data.message : t('logoutSuccess', '已退出登录。'));
        }).catch(function () {
          window.location.href = link.href;
        }).finally(function () {
          link.classList.remove('is-loading');
        });
      });
    });
  }

  function initWordPressCommentForm() {
    var respond = qs('#respond');
    var respondPlaceholder = qs('#reimu-respond-placeholder');
    if (respond && !respondPlaceholder) {
      respondPlaceholder = document.createElement('span');
      respondPlaceholder.id = 'reimu-respond-placeholder';
      respondPlaceholder.hidden = true;
      respondPlaceholder.setAttribute('aria-hidden', 'true');
      if (respond.parentNode) {
        respond.parentNode.insertBefore(respondPlaceholder, respond);
      }
    }

    function moveRespondBack() {
      respond = qs('#respond');
      if (!respond) {
        return;
      }
      if (respondPlaceholder && respondPlaceholder.parentNode) {
        respondPlaceholder.parentNode.insertBefore(respond, respondPlaceholder.nextSibling);
      } else if (qs('#comment-panel-wordpress')) {
        qs('#comment-panel-wordpress').insertBefore(respond, qs('#comment-panel-wordpress .reimu-comment-list-header'));
      }
      respond.classList.remove('reimu-respond-inline');
      respond.removeAttribute('aria-label');
      var liveParentInput = qs('#comment_parent', respond);
      if (liveParentInput) {
        liveParentInput.value = '0';
      }
      var textarea = qs('#comment', respond);
      if (textarea && textarea.value.charAt(0) === '@') {
        textarea.value = textarea.value.replace(/^@[^\s]+\s*/, '');
        dispatchInputEvent(textarea);
      }
    }

    function ensureReplyCancelButton() {
      var liveRespond = qs('#respond');
      if (!liveRespond || qs('[data-reimu-cancel-reply]', liveRespond)) {
        return;
      }
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'reimu-comment-cancel';
      button.setAttribute('aria-label', t('cancelReply', '取消回复'));
      button.setAttribute('data-reimu-cancel-reply', 'true');
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        moveRespondBack();
      });
      liveRespond.insertBefore(button, liveRespond.firstChild);
    }

    function placeRespondForReply(link) {
      var liveRespond = qs('#respond');
      var textarea = qs('#comment');
      var liveParentInput = qs('#comment_parent');
      var item = link.closest('.reimu-comment');
      var content = item ? qs('.reimu-comment__content', item) : null;
      var commentId = item ? String(item.id || '').replace('comment-', '') : '';
      if (liveParentInput && commentId) {
        liveParentInput.value = commentId;
      }
      if (liveRespond && content && liveRespond.parentNode !== content) {
        content.appendChild(liveRespond);
      }
      if (liveRespond) {
        liveRespond.classList.add('reimu-respond-inline');
        liveRespond.setAttribute('aria-label', t('replyComment', '回复评论'));
      }
      if (textarea && !textarea.value) {
        var author = qs('.reimu-comment__author', item);
        var name = author ? author.textContent.trim().replace(/\s+/g, '') : '';
        if (name) {
          textarea.value = '@' + name + ' ';
          dispatchInputEvent(textarea);
        }
      }
      return { respond: liveRespond, textarea: textarea, content: content };
    }

    qsa('.reimu-comment-form').forEach(function (form) {
      if (form.dataset.wpCommentReady) {
        return;
      }
      form.dataset.wpCommentReady = 'true';
      var textarea = qs('textarea[name="comment"]', form);
      var counter = qs('[data-comment-word-count]', form);
      if (textarea && counter) {
        var updateCount = function () {
          counter.textContent = String(commentTextForCount(textarea.value).length);
        };
        textarea.addEventListener('input', updateCount);
        textarea.addEventListener('change', updateCount);
        updateCount();
      }
      initProfileOpenDelegation();
      initCommentTools(form);
      initAjaxCommentSubmit(form);
    });
    ensureReplyCancelButton();

    initCommentSorting();
    initCommentLikes();
    initCommentOwnerActions();
    initLoginModal();
    initProfileOpenDelegation();
    initProfileModal();
    initGithubPopupLogin();
    initAuthPopupLinks();
    initCommentLoginTriggers();
    initCommentAjaxLogout();

    qsa('#comments .comment-reply-link').forEach(function (link) {
      if (link.dataset.reimuReplyReady) {
        return;
      }
      link.dataset.reimuReplyReady = 'true';
      link.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var placement = placeRespondForReply(link);
        window.setTimeout(function () {
          placement = placeRespondForReply(link);
        }, 90);
        var liveRespond = placement.respond;
        var textarea = placement.textarea;
        if (liveRespond && liveRespond.scrollIntoView) {
          liveRespond.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (textarea && textarea.focus) {
          window.setTimeout(function () {
            textarea.focus({ preventScroll: true });
          }, 30);
        }
      });
    });

    var cancel = qs('#cancel-comment-reply-link');
    if (cancel && !cancel.dataset.reimuCancelReady) {
      cancel.dataset.reimuCancelReady = 'true';
      cancel.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        moveRespondBack();
        window.setTimeout(moveRespondBack, 20);
      });
    }
  }


  return {
    syncConfig: syncConfig,
    initCommentSelector: initCommentSelector,
    initCommentUploadRows: initCommentUploadRows,
    initAjaxCommentSubmit: initAjaxCommentSubmit,
    initCommentLikes: initCommentLikes,
    initCommentOwnerActions: initCommentOwnerActions,
    setLoginModalOpen: setLoginModalOpen,
    initLoginModal: initLoginModal,
    initProfileModal: initProfileModal,
    applyCommentLoggedInState: applyCommentLoggedInState,
    applyCommentLoggedOutState: applyCommentLoggedOutState,
    refreshCommentLoginState: refreshCommentLoginState,
    initProfileOpenDelegation: initProfileOpenDelegation,
    initGithubPopupLogin: initGithubPopupLogin,
    initAuthPopupLinks: initAuthPopupLinks,
    initCommentLoginTriggers: initCommentLoginTriggers,
    initCommentAjaxLogout: initCommentAjaxLogout,
    initWordPressCommentForm: initWordPressCommentForm,
    initLoadMore: initLoadMore
  };
}
