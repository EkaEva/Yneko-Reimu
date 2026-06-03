export function createCommentTools(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var escapeHtml = deps.escapeHtml;
  var showTooltip = deps.showTooltip;
  var insertIntoTextarea = deps.insertIntoTextarea;
  var insertCommentMedia = deps.insertCommentMedia;
  var resolveCommentMediaTokens = deps.resolveCommentMediaTokens;
  var markdownToHtml = deps.markdownToHtml;
  var updateCommentPreview = deps.updateCommentPreview;
  var initCommentUploadRows = deps.initCommentUploadRows;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };

  function closeCommentPopovers(form, except) {
    qsa('.reimu-comment-popover', form).forEach(function (popover) {
      if (popover !== except) {
        popover.hidden = true;
      }
    });
    qsa('[data-comment-tool]', form).forEach(function (button) {
      if (!except || button.getAttribute('data-comment-tool') !== except.getAttribute('data-comment-popover')) {
        button.classList.remove('active');
        button.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function setCommentToolState(form, name, active) {
    var button = qs('[data-comment-tool="' + name + '"]', form);
    if (button) {
      button.classList.toggle('active', !!active);
      button.setAttribute('aria-expanded', active ? 'true' : 'false');
    }
  }

  function toggleCommentPopover(form, name) {
    var popover = qs('[data-comment-popover="' + name + '"]', form);
    var button = qs('[data-comment-tool="' + name + '"]', form);
    if (!popover || !button) {
      return;
    }
    var shouldOpen = popover.hidden;
    closeCommentPopovers(form, shouldOpen ? popover : null);
    popover.hidden = !shouldOpen;
    button.classList.toggle('active', shouldOpen);
    button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    if (shouldOpen) {
      var status = qs('[data-comment-upload-status="' + name + '"]', form);
      if (status) {
        status.textContent = '';
      }
    }
  }

  function initCommentPopoverOutsideClose() {
    if (document.documentElement.dataset.commentPopoverOutsideReady) {
      return;
    }
    document.documentElement.dataset.commentPopoverOutsideReady = 'true';
    document.addEventListener('click', function (event) {
      var target = event.target;
      if (!target || !target.closest) {
        return;
      }
      if (target.closest('.reimu-comment-popover') || target.closest('[data-comment-tool]')) {
        return;
      }
      qsa('.reimu-comment-form').forEach(function (form) {
        closeCommentPopovers(form);
      });
    });
  }

  function initCommentGifLibrary(form, textarea) {
    var library = qs('[data-comment-gif-library]', form);
    var config = getConfig();
    var uploads = config.commentUploads || {};
    var gifs = Array.isArray(uploads.gifs) ? uploads.gifs : [];
    if (!library || library.dataset.gifLibraryReady) {
      return;
    }
    library.dataset.gifLibraryReady = 'true';

    if (!gifs.length) {
      library.innerHTML = '<p class="reimu-comment-gif-empty">' + escapeHtml(t('commentGifEmpty', '暂无可选 GIF。')) + '</p>';
      return;
    }

    library.innerHTML = gifs.map(function (item) {
      return '<button type="button" class="reimu-comment-gif-item" data-comment-gif-url="' + escapeHtml(item.url || '') + '" title="' + escapeHtml(item.title || 'GIF') + '"><img src="' + escapeHtml(item.url || '') + '" alt="GIF" loading="lazy" decoding="async"></button>';
    }).join('');

    qsa('[data-comment-gif-url]', library).forEach(function (button) {
      button.addEventListener('click', function () {
        var url = button.getAttribute('data-comment-gif-url') || '';
        if (url) {
          if (insertCommentMedia(textarea, url, 'gif')) {
            closeCommentPopovers(form);
          }
        }
      });
    });
  }

  function initCommentTools(form) {
    var textarea = qs('textarea[name="comment"]', form);
    if (!textarea) {
      return;
    }

    qsa('[data-comment-tool]', form).forEach(function (button) {
      if (button.dataset.commentToolReady) {
        return;
      }
      button.dataset.commentToolReady = 'true';
      button.setAttribute('aria-expanded', 'false');
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var tool = button.getAttribute('data-comment-tool');
        if (tool === 'preview') {
          var panel = qs('[data-comment-preview-panel]', form);
          var shouldOpen = panel ? panel.hidden : false;
          if (panel) {
            panel.hidden = !shouldOpen;
            panel.classList.toggle('is-open', shouldOpen);
          }
          updateCommentPreview(form, textarea);
          closeCommentPopovers(form);
          setCommentToolState(form, 'preview', shouldOpen);
          return;
        }
        toggleCommentPopover(form, tool);
        var input = qs('[data-comment-popover="' + tool + '"] input', form);
        if (input && !input.hidden) {
          window.setTimeout(function () {
            input.focus();
          }, 0);
        }
      });
    });

    initCommentGifLibrary(form, textarea);
    initCommentUploadRows(form, textarea);
    initCommentPopoverOutsideClose();
    textarea.addEventListener('input', function () {
      var preview = qs('[data-comment-preview-panel]', form);
      if (preview && !preview.hidden) {
        updateCommentPreview(form, textarea);
      }
    });

    qsa('[data-comment-insert]', form).forEach(function (button) {
      if (button.dataset.commentInsertReady) {
        return;
      }
      button.dataset.commentInsertReady = 'true';
      button.addEventListener('click', function () {
        insertIntoTextarea(textarea, button.getAttribute('data-comment-insert') || '');
        closeCommentPopovers(form);
      });
    });

    qsa('[data-comment-url-insert]', form).forEach(function (button) {
      if (button.dataset.commentUrlReady) {
        return;
      }
      button.dataset.commentUrlReady = 'true';
      button.addEventListener('click', function () {
        var type = button.getAttribute('data-comment-url-insert');
        var input = qs('[data-comment-url-input="' + type + '"]', form);
        var url = input ? String(input.value || '').trim() : '';
        if (!/^https?:\/\//i.test(url)) {
          showTooltip(t('invalidImageUrl', '请输入 http(s) 图片地址'));
          if (input) {
            input.focus();
          }
          return;
        }
        if (insertCommentMedia(textarea, url, type)) {
          if (input) {
            input.value = '';
          }
          closeCommentPopovers(form);
        }
      });
    });

    textarea.addEventListener('input', function () {
      var preview = qs('[data-comment-popover="preview"]:not([hidden]) .reimu-comment-preview-content', form);
      if (preview) {
        preview.innerHTML = markdownToHtml(resolveCommentMediaTokens(textarea.value, textarea));
      }
    });
  }

  return {
    closeCommentPopovers: closeCommentPopovers,
    setCommentToolState: setCommentToolState,
    toggleCommentPopover: toggleCommentPopover,
    initCommentPopoverOutsideClose: initCommentPopoverOutsideClose,
    initCommentGifLibrary: initCommentGifLibrary,
    initCommentTools: initCommentTools
  };
}
