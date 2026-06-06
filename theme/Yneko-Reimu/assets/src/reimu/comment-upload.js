export function createCommentUploadRuntime(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var showTooltip = deps.showTooltip;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var commentMediaEntries = deps.commentMediaEntries;
  var confirmCommentMediaReplace = deps.confirmCommentMediaReplace;
  var insertCommentMedia = deps.insertCommentMedia;

  function initCommentUploadRows(form, textarea) {
    function uploadState(type) {
      var config = getConfig();
      var uploads = config.commentUploads || {};
      var enabledKey = type === 'gif' ? 'gifEnabled' : 'imageEnabled';
      return {
        config: config,
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

    function uploadCommentFile(type, input, button, textarea, uploadForm, replaceConfirmed) {
      var state = uploadState(type);
      if (!state.canUpload) {
        showTooltip(t(type === 'gif' ? 'commentUploadGifLogin' : 'commentUploadLogin', type === 'gif' ? '登录后可上传 GIF。' : '登录后可上传图片。'));
        return;
      }

      var status = qs('[data-comment-upload-status="' + type + '"]', uploadForm);
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

      fetch(state.config.login.ajaxUrl, {
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

  return {
    initCommentUploadRows: initCommentUploadRows
  };
}
