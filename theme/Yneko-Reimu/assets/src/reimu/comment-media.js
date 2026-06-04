export function createCommentMedia(deps) {
  deps = deps || {};
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return deps.config || {}; };
  var t = typeof deps.t === 'function' ? deps.t : function (_, fallback) { return fallback; };
  var escapeHtml = typeof deps.escapeHtml === 'function' ? deps.escapeHtml : function (value) { return String(value || ''); };
  var dispatchInputEvent = typeof deps.dispatchInputEvent === 'function' ? deps.dispatchInputEvent : function () {};
  var requestConfirm = typeof deps.requestConfirm === 'function' ? deps.requestConfirm : function (message) {
    return Promise.resolve(typeof globalThis.confirm === 'function' ? globalThis.confirm(message) : false);
  };

  function insertIntoTextarea(textarea, text) {
    if (!textarea || !text) {
      return;
    }
    var value = textarea.value || '';
    var start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : value.length;
    var end = typeof textarea.selectionEnd === 'number' ? textarea.selectionEnd : start;
    var before = value.slice(0, start);
    var after = value.slice(end);
    textarea.value = before + text + after;
    var position = start + text.length;
    textarea.focus();
    textarea.setSelectionRange(position, position);
    dispatchInputEvent(textarea);
  }

  function commentMediaStore(textarea) {
    if (!textarea._reimuCommentMedia) {
      textarea._reimuCommentMedia = {
        index: 0,
        items: {}
      };
    }
    return textarea._reimuCommentMedia;
  }

  function commentMediaToken(textarea, url, type, options) {
    var store = commentMediaStore(textarea);
    store.index += 1;
    var kind = type === 'gif' ? 'GIF' : 'IMAGE';
    var token = '[' + kind + ':' + store.index + ']';
    store.items[token] = {
      url: url,
      type: type === 'gif' ? 'gif' : 'image',
      cleanupKey: options && options.cleanupKey ? String(options.cleanupKey) : '',
      uploaded: !!(options && options.uploaded)
    };
    return token;
  }

  function resolveCommentMediaTokens(value, textarea) {
    var store = textarea && textarea._reimuCommentMedia ? textarea._reimuCommentMedia.items : {};
    return String(value || '').replace(/\[(GIF|IMAGE):(\d+)\]/g, function (token, kind) {
      var item = store[token];
      if (!item || !item.url) {
        return token;
      }
      return '![' + (kind === 'GIF' ? 'GIF' : 'image') + '](' + item.url + ')';
    });
  }

  function commentMediaRegex() {
    return /\[(GIF|IMAGE):(\d+)\]|!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/gi;
  }

  function commentMediaEntries(value) {
    var entries = [];
    String(value || '').replace(commentMediaRegex(), function (match, tokenKind, tokenIndex, alt, url, offset) {
      var type = tokenKind ? String(tokenKind).toLowerCase() : (/gif/i.test(alt || '') || /\.gif(?:[?#]|$)/i.test(url || '') ? 'gif' : 'image');
      entries.push({
        text: match,
        type: type === 'gif' ? 'gif' : 'image',
        offset: offset,
        length: match.length
      });
      return match;
    });
    return entries;
  }

  function cleanupUnsubmittedCommentMedia(item) {
    var config = getConfig();
    if (!item || !item.uploaded || !item.cleanupKey || !item.url || !config.login || !config.login.ajaxUrl) {
      return;
    }
    var uploads = config.commentUploads || {};
    if (!uploads.nonce) {
      return;
    }
    var formData = new FormData();
    formData.append('action', 'yneko_reimu_comment_upload_discard');
    formData.append('nonce', uploads.nonce || '');
    formData.append('url', item.url || '');
    formData.append('cleanup_key', item.cleanupKey || '');
    fetch(config.login.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    }).catch(function () {});
  }

  function commentMediaReplaceMessage(entries) {
    var hasGif = entries.some(function (entry) {
      return entry.type === 'gif';
    });
    var hasImage = entries.some(function (entry) {
      return entry.type === 'image';
    });
    if (hasGif && !hasImage) {
      return t('commentMediaReplaceGifConfirm', '是否清空当前表情并重新添加？');
    }
    if (hasImage && !hasGif) {
      return t('commentMediaReplaceImageConfirm', '是否清空当前图片并重新添加？');
    }
    return t('commentMediaReplaceAllConfirm', '是否清空当前图片和表情并重新添加？');
  }

  function removeCommentMediaFromTextarea(textarea) {
    if (!textarea) {
      return;
    }
    var value = textarea.value || '';
    var entries = commentMediaEntries(value);
    if (!entries.length) {
      return;
    }
    var start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : value.length;
    var store = textarea._reimuCommentMedia ? textarea._reimuCommentMedia.items : null;
    if (store) {
      entries.forEach(function (entry) {
        if (/^\[(?:GIF|IMAGE):\d+\]$/i.test(entry.text)) {
          cleanupUnsubmittedCommentMedia(store[entry.text]);
          delete store[entry.text];
        }
      });
    }
    var cleaned = value
      .replace(commentMediaRegex(), '\n')
      .replace(/[ \t]*\n[ \t]*/g, '\n')
      .replace(/\n{3,}/g, '\n\n')
      .trim();
    textarea.value = cleaned;
    if (textarea.setSelectionRange) {
      var position = Math.min(start, cleaned.length);
      textarea.setSelectionRange(position, position);
    }
    dispatchInputEvent(textarea);
  }

  function confirmCommentMediaReplace(textarea) {
    var entries = commentMediaEntries(textarea ? textarea.value : '');
    return !entries.length ? Promise.resolve(true) : requestConfirm(commentMediaReplaceMessage(entries));
  }

  function prepareCommentMediaInsert(textarea, confirmedReplace) {
    var entries = commentMediaEntries(textarea ? textarea.value : '');
    if (!entries.length) {
      return Promise.resolve(true);
    }
    if (confirmedReplace) {
      removeCommentMediaFromTextarea(textarea);
      return Promise.resolve(true);
    }
    return requestConfirm(commentMediaReplaceMessage(entries)).then(function (confirmed) {
      if (!confirmed) {
        return false;
      }
      removeCommentMediaFromTextarea(textarea);
      return true;
    });
  }

  function commentMediaLimitOk(value) {
    return commentMediaEntries(value).length <= 1;
  }

  function commentTextForCount(value) {
    return String(value || '')
      .replace(/\[(?:GIF|IMAGE):\d+\]/g, '')
      .replace(/!\[[^\]]*\]\((?:https?:)?\/\/[^)\s]+\)/gi, '')
      .replace(/\[[^\]]+\]\((?:https?:)?\/\/[^)\s]+\)/gi, function (_, label) {
        return label || '';
      })
      .trim();
  }

  function markdownToHtml(text) {
    var blocks = [];
    var source = String(text || '').replace(/\r\n?/g, '\n').replace(/```\s*(?:[a-z0-9_-]+)?[^\n]*\n?([\s\S]*?)```/gi, function (_, code) {
      var key = '%%REIMU_COMMENT_CODE_' + blocks.length + '%%';
      blocks.push({
        key: key,
        html: '<pre><code>' + escapeHtml(String(code || '').replace(/\n$/, '')) + '</code></pre>'
      });
      return '\n' + key + '\n';
    });

    var html = escapeHtml(source)
      .replace(/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/gi, function (_, alt, url) {
        return '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(alt) + '" loading="lazy" decoding="async">';
      })
      .replace(/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/gi, function (_, label, url) {
        return '<a href="' + escapeHtml(url) + '" rel="nofollow noopener noreferrer" target="_blank">' + escapeHtml(label) + '</a>';
      })
      .replace(/`([^`]+)`/g, '<code>$1</code>')
      .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

    html = html.split(/\n{2,}/).map(function (block) {
      var trimmed = block.trim();
      if (!trimmed) {
        return '';
      }
      if (/^%%REIMU_COMMENT_CODE_\d+%%$/.test(trimmed)) {
        return trimmed;
      }
      return '<p>' + trimmed.replace(/\n/g, '<br>') + '</p>';
    }).join('');

    blocks.forEach(function (block) {
      html = html.replace(block.key, block.html);
    });

    return html || '<p class="reimu-comment-preview-empty">' + escapeHtml(t('commentPreviewEmpty', '还没有内容。')) + '</p>';
  }

  function updateCommentPreview(form, textarea, qs) {
    var preview = qs('[data-comment-preview-panel] .reimu-comment-preview-content', form);
    if (preview) {
      preview.innerHTML = markdownToHtml(resolveCommentMediaTokens(textarea.value, textarea));
    }
  }

  function insertCommentMedia(textarea, url, type, options) {
    return prepareCommentMediaInsert(textarea, options && options.confirmedReplace).then(function (confirmed) {
      if (!confirmed) {
        return false;
      }
      insertIntoTextarea(textarea, commentMediaToken(textarea, url, type, options || {}));
      return true;
    });
  }

  return {
    insertIntoTextarea: insertIntoTextarea,
    commentMediaStore: commentMediaStore,
    commentMediaToken: commentMediaToken,
    resolveCommentMediaTokens: resolveCommentMediaTokens,
    commentMediaRegex: commentMediaRegex,
    commentMediaEntries: commentMediaEntries,
    cleanupUnsubmittedCommentMedia: cleanupUnsubmittedCommentMedia,
    commentMediaReplaceMessage: commentMediaReplaceMessage,
    removeCommentMediaFromTextarea: removeCommentMediaFromTextarea,
    confirmCommentMediaReplace: confirmCommentMediaReplace,
    prepareCommentMediaInsert: prepareCommentMediaInsert,
    commentMediaLimitOk: commentMediaLimitOk,
    commentTextForCount: commentTextForCount,
    markdownToHtml: markdownToHtml,
    updateCommentPreview: updateCommentPreview,
    insertCommentMedia: insertCommentMedia
  };
}
