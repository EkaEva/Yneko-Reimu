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
    var scope = root || document;
    var sections = [];

    if (scope.matches && scope.matches('.yneko-reimu-repeatable')) {
      sections.push(scope);
    }
    scope.querySelectorAll('.yneko-reimu-repeatable').forEach(function (section) {
      sections.push(section);
    });

    sections.forEach(function (section) {
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

  function postAdminTotp(root, action, extra) {
    var data = new FormData();
    data.append('action', action);
    data.append('nonce', root.getAttribute('data-nonce') || '');
    Object.keys(extra || {}).forEach(function (key) {
      data.append(key, extra[key]);
    });

    return fetch(window.ajaxurl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data
    }).then(function (response) {
      return response.json().catch(function () {
        return { success: false, data: {} };
      });
    });
  }

  function loadQrCode(src) {
    if (window.QRCode && typeof window.QRCode.toDataURL === 'function') {
      return Promise.resolve(window.QRCode);
    }
    return new Promise(function (resolve, reject) {
      var existing = document.querySelector('script[data-yneko-admin-qrcode]');
      var script;

      if (existing) {
        existing.addEventListener('load', function () {
          resolve(window.QRCode);
        }, { once: true });
        existing.addEventListener('error', reject, { once: true });
        return;
      }

      script = document.createElement('script');
      script.src = src || '';
      script.async = true;
      script.defer = true;
      script.setAttribute('data-yneko-admin-qrcode', '1');
      script.addEventListener('load', function () {
        resolve(window.QRCode);
      });
      script.addEventListener('error', reject);
      document.head.appendChild(script);
    });
  }

  function setTotpMessage(root, message, error) {
    var node = root.querySelector('[data-yneko-admin-totp-message]');
    if (!node) {
      return;
    }
    node.textContent = message || '';
    node.classList.toggle('is-error', !!error);
  }

  function setTotpState(root, enabled, nonce) {
    var status = root.querySelector('[data-yneko-admin-totp-status]');
    var disable = root.querySelector('[data-yneko-admin-totp-disable]');
    var recovery = root.querySelector('[data-yneko-admin-totp-recovery]');
    var setup = root.querySelector('[data-yneko-admin-totp-setup]');
    var code = root.querySelector('[data-yneko-admin-totp-code]');

    root.setAttribute('data-enabled', enabled ? '1' : '0');
    if (nonce) {
      root.setAttribute('data-nonce', nonce);
    }
    if (status) {
      status.classList.toggle('is-enabled', !!enabled);
      status.innerHTML = labelText(enabled ? 'totpEnabled' : 'totpDisabled', enabled ? '已开启' : '未开启', enabled ? 'Enabled' : 'Disabled');
    }
    if (disable) {
      disable.hidden = !enabled;
    }
    if (recovery) {
      recovery.hidden = !enabled;
    }
    if (setup && enabled) {
      setup.hidden = true;
    }
    if (code && enabled) {
      code.value = '';
    }
  }

  function setRecoveryCount(root, count) {
    var node = root.querySelector('[data-yneko-admin-totp-recovery-count]');
    if (!node) {
      return;
    }
    node.textContent = plain('totpRecoveryCount', '剩余 %d 个', '%d remaining').replace('%d', count);
  }

  function renderRecoveryCodes(root, codes) {
    var list = root.querySelector('[data-yneko-admin-totp-recovery-codes]');
    var copy = root.querySelector('[data-yneko-admin-totp-recovery-copy]');
    var text = Array.isArray(codes) ? codes.join('\n') : '';

    if (list) {
      list.textContent = text;
      list.hidden = !text;
    }
    if (copy) {
      copy.hidden = !text;
      copy.setAttribute('data-recovery-codes', text);
    }
    if (Array.isArray(codes)) {
      setRecoveryCount(root, codes.length);
    }
  }

  function renderTotpSecret(root, payload) {
    var setup = root.querySelector('[data-yneko-admin-totp-setup]');
    var secret = root.querySelector('[data-yneko-admin-totp-secret]');
    var qr = root.querySelector('[data-yneko-admin-totp-qr]');

    if (setup) {
      setup.hidden = false;
    }
    if (secret) {
      secret.textContent = payload.secret || '';
    }
    if (!qr || !payload.uri) {
      return Promise.resolve();
    }

    return loadQrCode(root.getAttribute('data-qrcode-src') || '').then(function (QRCode) {
      if (!QRCode || typeof QRCode.toDataURL !== 'function') {
        throw new Error('QRCode.toDataURL is unavailable');
      }
      return QRCode.toDataURL(payload.uri, {
        errorCorrectionLevel: 'M',
        margin: 1,
        width: 160
      });
    }).then(function (dataUrl) {
      qr.src = dataUrl;
      qr.hidden = false;
    });
  }

  function initAdminTotp() {
    document.querySelectorAll('[data-yneko-admin-totp]').forEach(function (root) {
      var generate = root.querySelector('[data-yneko-admin-totp-generate]');
      var enable = root.querySelector('[data-yneko-admin-totp-enable]');
      var disable = root.querySelector('[data-yneko-admin-totp-disable]');
      var recoveryGenerate = root.querySelector('[data-yneko-admin-totp-recovery-generate]');
      var recoveryCopy = root.querySelector('[data-yneko-admin-totp-recovery-copy]');
      var code = root.querySelector('[data-yneko-admin-totp-code]');

      setTotpState(root, root.getAttribute('data-enabled') === '1', root.getAttribute('data-nonce'));

      if (generate) {
        generate.addEventListener('click', function () {
          setLoading(generate, true);
          setTotpMessage(root, '', false);
          postAdminTotp(root, 'yneko_reimu_admin_totp_generate').then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.data && payload.data.message ? payload.data.message : plain('totpGenerateFailed', '二次认证密钥生成失败。', 'Failed to generate the two-factor secret.'));
            }
            if (payload.data && payload.data.nonce) {
              root.setAttribute('data-nonce', payload.data.nonce);
            }
            return renderTotpSecret(root, payload.data || {}).then(function () {
              setTotpMessage(root, payload.data && payload.data.message ? payload.data.message : '', false);
            });
          }).catch(function (error) {
            setTotpMessage(root, error.message || plain('totpGenerateFailed', '二次认证密钥生成失败。', 'Failed to generate the two-factor secret.'), true);
          }).finally(function () {
            setLoading(generate, false);
          });
        });
      }

      if (enable) {
        enable.addEventListener('click', function () {
          setLoading(enable, true);
          setTotpMessage(root, '', false);
          postAdminTotp(root, 'yneko_reimu_admin_totp_enable', {
            totp_code: code ? code.value : ''
          }).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.data && payload.data.message ? payload.data.message : plain('totpEnableFailed', '二次认证启用失败。', 'Failed to enable two-factor authentication.'));
            }
            setTotpState(root, true, payload.data && payload.data.nonce);
            renderRecoveryCodes(root, payload.data && payload.data.recoveryCodes);
            setTotpMessage(root, payload.data && payload.data.message ? payload.data.message : '', false);
          }).catch(function (error) {
            setTotpMessage(root, error.message || plain('totpEnableFailed', '二次认证启用失败。', 'Failed to enable two-factor authentication.'), true);
          }).finally(function () {
            setLoading(enable, false);
          });
        });
      }

      if (disable) {
        disable.addEventListener('click', function () {
          if (!window.confirm(plain('totpDisableConfirm', '确定关闭当前账号的二次认证吗？', 'Disable two-factor authentication for the current account?'))) {
            return;
          }
          setLoading(disable, true);
          setTotpMessage(root, '', false);
          postAdminTotp(root, 'yneko_reimu_admin_totp_disable').then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.data && payload.data.message ? payload.data.message : plain('totpDisableFailed', '二次认证关闭失败。', 'Failed to disable two-factor authentication.'));
            }
            setTotpState(root, false, payload.data && payload.data.nonce);
            renderRecoveryCodes(root, []);
            setTotpMessage(root, payload.data && payload.data.message ? payload.data.message : '', false);
          }).catch(function (error) {
            setTotpMessage(root, error.message || plain('totpDisableFailed', '二次认证关闭失败。', 'Failed to disable two-factor authentication.'), true);
          }).finally(function () {
            setLoading(disable, false);
          });
        });
      }

      if (recoveryGenerate) {
        recoveryGenerate.addEventListener('click', function () {
          if (!window.confirm(plain('totpRecoveryGenerateConfirm', '重新生成恢复码会让旧恢复码全部失效，确定继续吗？', 'Regenerating recovery codes will invalidate all old codes. Continue?'))) {
            return;
          }
          setLoading(recoveryGenerate, true);
          setTotpMessage(root, '', false);
          postAdminTotp(root, 'yneko_reimu_admin_totp_recovery_generate').then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.data && payload.data.message ? payload.data.message : plain('totpRecoveryGenerateFailed', '恢复码生成失败。', 'Failed to generate recovery codes.'));
            }
            if (payload.data && payload.data.nonce) {
              root.setAttribute('data-nonce', payload.data.nonce);
            }
            renderRecoveryCodes(root, payload.data && payload.data.recoveryCodes);
            setTotpMessage(root, payload.data && payload.data.message ? payload.data.message : '', false);
          }).catch(function (error) {
            setTotpMessage(root, error.message || plain('totpRecoveryGenerateFailed', '恢复码生成失败。', 'Failed to generate recovery codes.'), true);
          }).finally(function () {
            setLoading(recoveryGenerate, false);
          });
        });
      }

      if (recoveryCopy) {
        recoveryCopy.addEventListener('click', function () {
          var text = recoveryCopy.getAttribute('data-recovery-codes') || '';
          if (!text || !navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') {
            return;
          }
          navigator.clipboard.writeText(text).then(function () {
            setTotpMessage(root, plain('totpRecoveryCopied', '恢复码已复制。', 'Recovery codes copied.'), false);
          });
        });
      }
    });
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
  initAdminTotp();
}());
