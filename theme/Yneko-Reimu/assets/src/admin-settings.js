(function () {
  'use strict';

  var labels = window.YNEKO_REIMU_ADMIN_I18N || {};
  var locale = labels.locale === 'zh' ? 'zh' : 'en';
  var counters = {
    friend: Date.now(),
    music: Date.now() + 1000
  };

  function esc(value) {
    return String(value || '').replace(/[&<>"']/g, function (chr) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[chr];
    });
  }

  function plain(key, zh, en) {
    var item = labels[key] || {};
    return item[locale] || (locale === 'zh' ? zh : en);
  }

  function labelText(key, zh, en) {
    return '<span class="yneko-reimu-admin-text">' + esc(plain(key, zh, en)) + '</span>';
  }

  function fieldLabel(key, zh, en, control) {
    return '<label>' + labelText(key, zh, en) + control + '</label>';
  }

  function rowHeading(type) {
    return '<div class="yneko-reimu-row-heading" data-row-label="' + type + '"><span class="yneko-reimu-row-number"></span></div>';
  }

  function rowTitle(type, index) {
    var key = type === 'music' ? 'musicItem' : 'friendItem';
    var fallbackZh = type === 'music' ? '曲目' : '友链';
    var fallbackEn = type === 'music' ? 'Track' : 'Friend';
    return '<span class="yneko-reimu-admin-text">' + esc(plain(key, fallbackZh, fallbackEn)) + ' #' + index + '</span>';
  }

  function isAccepted(input, url) {
    var accept = (input && input.dataset ? input.dataset.accept : '') || '';
    var allowed = [];

    if (!accept) {
      return true;
    }
    if (/image\/png/.test(accept)) {
      allowed.push('png');
    }
    if (/image\/webp/.test(accept)) {
      allowed.push('webp');
    }
    if (/image\/avif/.test(accept)) {
      allowed.push('avif');
    }
    if (/image\/jpe?g/.test(accept)) {
      allowed.push('jpe?g');
    }
    if (!allowed.length) {
      return true;
    }

    return new RegExp('\\.(' + allowed.join('|') + ')(?:[?#].*)?$', 'i').test(url || '');
  }

  function activateTab(name) {
    var tabs = document.querySelectorAll('[data-yneko-settings-tab]');
    var panels = document.querySelectorAll('[data-yneko-settings-panel]');
    var exists = false;

    tabs.forEach(function (tab) {
      if (tab.getAttribute('data-yneko-settings-tab') === name) {
        exists = true;
      }
    });
    if (!exists) {
      name = 'general';
    }

    tabs.forEach(function (tab) {
      var active = tab.getAttribute('data-yneko-settings-tab') === name;
      tab.classList.toggle('nav-tab-active', active);
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    panels.forEach(function (panel) {
      var active = panel.getAttribute('data-yneko-settings-panel') === name;
      panel.hidden = !active;
      panel.classList.toggle('is-active', active);
    });

    try {
      window.localStorage.setItem('ynekoReimuSettingsTab', name);
    } catch (error) {}
    if (window.location.hash !== '#' + name) {
      try {
        history.replaceState(null, '', '#' + name);
      } catch (error) {}
    }
  }

  function initTabs() {
    var initial = (window.location.hash || '').replace(/^#/, '');

    if (!initial) {
      try {
        initial = window.localStorage.getItem('ynekoReimuSettingsTab') || '';
      } catch (error) {}
    }

    activateTab(initial || 'general');
    document.querySelectorAll('[data-yneko-settings-tab]').forEach(function (tab) {
      tab.addEventListener('click', function (event) {
        event.preventDefault();
        activateTab(tab.getAttribute('data-yneko-settings-tab') || 'general');
      });
    });
    window.addEventListener('hashchange', function () {
      activateTab((window.location.hash || '').replace(/^#/, ''));
    });
  }

  function refreshNumbers(root) {
    (root || document).querySelectorAll('.yneko-reimu-repeatable').forEach(function (section) {
      var type = section.dataset.repeatable === 'music' ? 'music' : 'friend';
      section.querySelectorAll('.yneko-reimu-repeatable-row').forEach(function (row, index) {
        var heading = row.querySelector('.yneko-reimu-row-heading');
        var number;

        if (!heading) {
          heading = document.createElement('div');
          heading.className = 'yneko-reimu-row-heading';
          heading.setAttribute('data-row-label', type);
          heading.innerHTML = '<span class="yneko-reimu-row-number"></span>';
          row.insertBefore(heading, row.firstChild);
        }
        number = heading.querySelector('.yneko-reimu-row-number');
        if (number) {
          number.innerHTML = rowTitle(type, index + 1);
        }
      });
    });
  }

  function media(button) {
    var field = button.closest('.yneko-reimu-inline-media') || button.closest('.yneko-reimu-media-field');
    var input = field ? field.querySelector('.yneko-reimu-media-url') : null;
    var accept;
    var frame;

    if (!input || !window.wp || !wp.media) {
      return;
    }

    accept = (input.dataset && input.dataset.accept) || '';
    frame = wp.media({
      title: plain('mediaTitle', '选择媒体', 'Select media'),
      button: { text: plain('useMedia', '使用此媒体', 'Use this media') },
      library: accept ? { type: accept.split(',') } : undefined,
      multiple: false
    });
    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      var url = attachment.url || '';

      if (!isAccepted(input, url)) {
        window.alert(plain('invalidImage', '请选择此字段允许的图片格式。', 'Please choose an image format allowed by this field.'));
        return;
      }
      input.value = url;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    frame.open();
  }

  function pickButton() {
    return '<button type="button" class="button yneko-reimu-media-button">' + labelText('choose', '选择', 'Choose') + '</button>';
  }

  function mediaInput(name) {
    return '<span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="' + name + '">' + pickButton() + '</span>';
  }

  function friendTemplate(index) {
    return '<div class="yneko-reimu-repeatable-row">' +
      rowHeading('friend') +
      '<div class="yneko-reimu-row-grid yneko-reimu-row-grid-friend">' +
      fieldLabel('name', '名称', 'Name', '<input type="text" name="yneko_reimu_settings[friends][' + index + '][name]">') +
      fieldLabel('url', '链接', 'URL', '<input type="url" name="yneko_reimu_settings[friends][' + index + '][url]">') +
      fieldLabel('description', '描述', 'Description', '<input type="text" name="yneko_reimu_settings[friends][' + index + '][desc]">') +
      fieldLabel('avatar', '头像', 'Avatar', '<span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[friends][' + index + '][image]">' + pickButton() + '</span>') +
      '</div><div class="yneko-reimu-row-actions"><button type="button" class="button yneko-reimu-remove-row">' + labelText('remove', '删除', 'Remove') + '</button></div></div>';
  }

  function musicTemplate(index) {
    return '<div class="yneko-reimu-repeatable-row">' +
      rowHeading('music') +
      '<div class="yneko-reimu-row-grid yneko-reimu-row-grid-music">' +
      fieldLabel('trackTitle', '歌名', 'Track title', '<input type="text" name="yneko_reimu_settings[music][' + index + '][name]">') +
      fieldLabel('artist', '作者', 'Artist', '<input type="text" name="yneko_reimu_settings[music][' + index + '][artist]">') +
      fieldLabel('audio', '音频', 'Audio', mediaInput('yneko_reimu_settings[music][' + index + '][url]')) +
      fieldLabel('cover', '封面', 'Cover', mediaInput('yneko_reimu_settings[music][' + index + '][cover]')) +
      fieldLabel('lyrics', '歌词 LRC', 'Lyrics LRC', mediaInput('yneko_reimu_settings[music][' + index + '][lrc]')) +
      fieldLabel('themeColor', '主题色', 'Theme color', '<input type="text" name="yneko_reimu_settings[music][' + index + '][theme]" value="#ff5252">') +
      '</div><div class="yneko-reimu-row-actions"><button type="button" class="button yneko-reimu-remove-row">' + labelText('remove', '删除', 'Remove') + '</button></div></div>';
  }

  function setLoading(button, loading) {
    if (!button) {
      return;
    }
    button.classList.toggle('is-loading', !!loading);
    button.disabled = !!loading;
  }

  function initGifAdmin() {
    var file = document.getElementById('yneko-reimu-admin-gif-file');
    var form = document.getElementById('yneko-reimu-admin-gif-upload-form');

    document.addEventListener('click', function (event) {
      var pick = event.target && event.target.closest ? event.target.closest('[data-yneko-admin-gif-pick]') : null;
      var mediaButton;

      if (pick) {
        event.preventDefault();
        if (file) {
          file.click();
        }
        return;
      }

      mediaButton = event.target && event.target.closest ? event.target.closest('[data-yneko-admin-gif-media]') : null;
      if (!mediaButton) {
        return;
      }

      event.preventDefault();
      if (!window.wp || !wp.media) {
        return;
      }

      var frame = wp.media({
        title: plain('adminGifTitle', '选择 GIF', 'Select GIF'),
        button: { text: plain('adminGifUse', '加入表情库', 'Add to library') },
        library: { type: 'image/gif' },
        multiple: false
      });
      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        var data;

        if (!attachment || attachment.mime !== 'image/gif') {
          window.alert(plain('adminGifInvalid', '请选择 GIF 文件。', 'Please select a GIF file.'));
          return;
        }

        data = new FormData();
        data.append('action', 'yneko_reimu_admin_add_gif_media');
        data.append('nonce', mediaButton.getAttribute('data-nonce') || '');
        data.append('attachment_id', attachment.id || '');
        setLoading(mediaButton, true);
        fetch(window.ajaxurl, {
          method: 'POST',
          credentials: 'same-origin',
          body: data
        }).then(function (response) {
          return response.json().catch(function () {
            return { success: false, data: { message: plain('adminGifFailed', 'GIF 入库失败。', 'Failed to add GIF.') } };
          });
        }).then(function (payload) {
          if (!payload || !payload.success) {
            window.alert(payload && payload.data && payload.data.message ? payload.data.message : plain('adminGifFailed', 'GIF 入库失败。', 'Failed to add GIF.'));
            setLoading(mediaButton, false);
            return;
          }
          window.location.href = window.location.href.replace(/#.*$/, '') + '#comments';
          window.location.reload();
        }).catch(function () {
          window.alert(plain('adminGifFailed', 'GIF 入库失败。', 'Failed to add GIF.'));
          setLoading(mediaButton, false);
        });
      });
      frame.open();
    });

    if (file && form) {
      file.addEventListener('change', function () {
        if (file.files && file.files.length) {
          form.submit();
        }
      });
    }
  }

  document.addEventListener('change', function (event) {
    var input = event.target && event.target.matches && event.target.matches('.yneko-reimu-media-url[data-accept]') ? event.target : null;
    if (input && input.value && !isAccepted(input, input.value)) {
      window.alert(plain('invalidImage', '请选择此字段允许的图片格式。', 'Please choose an image format allowed by this field.'));
      input.value = '';
    }
  });

  document.addEventListener('click', function (event) {
    var target = event.target;
    var add;
    var type;
    var repeatable;
    var list;
    var index;

    if (target.closest('[data-yneko-upload-delete]') && !window.confirm(plain('deleteUpload', '确定删除这个评论上传文件吗？', 'Delete this comment upload file?'))) {
      event.preventDefault();
      return;
    }
    if (target.closest('.yneko-reimu-media-button')) {
      event.preventDefault();
      media(target.closest('.yneko-reimu-media-button'));
    }
    if (target.closest('.yneko-reimu-remove-row')) {
      event.preventDefault();
      repeatable = target.closest('.yneko-reimu-repeatable');
      target.closest('.yneko-reimu-repeatable-row').remove();
      refreshNumbers(repeatable || document);
    }

    add = target.closest('.yneko-reimu-add-row');
    if (add) {
      event.preventDefault();
      type = add.dataset.template;
      repeatable = add.closest('.yneko-reimu-repeatable');
      list = repeatable.querySelector('.yneko-reimu-repeatable-list');
      index = counters[type]++;
      list.insertAdjacentHTML('beforeend', type === 'friend' ? friendTemplate(index) : musicTemplate(index));
      refreshNumbers(repeatable);
    }
  });

  initTabs();
  refreshNumbers();
  initGifAdmin();
}());
