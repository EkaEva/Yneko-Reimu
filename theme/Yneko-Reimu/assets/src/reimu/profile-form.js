export function createProfileFormUi(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var escapeHtml = deps.escapeHtml;
  var form = deps.form;

  function normalizeUrlInput(input) {
    if (!input) {
      return;
    }
    var value = String(input.value || '').trim();
    if (value && !/^[a-z][a-z0-9+.-]*:\/\//i.test(value) && /^[^\s/@]+\.[^\s]+/.test(value)) {
      input.value = 'https://' + value;
    }
  }

  function validateProfilePasswords() {
    if (!form) {
      return true;
    }
    var password = qs('[name="new_password"]', form);
    var confirm = qs('[name="new_password_confirm"]', form);
    var messageText = t('passwordMismatch', '两次输入的密码不一致。');
    var invalid = !!(password && confirm && confirm.value && password.value !== confirm.value);
    if (confirm) {
      confirm.classList.toggle('is-invalid', invalid);
      confirm.setCustomValidity(invalid ? messageText : '');
    }
    if (password) {
      password.classList.toggle('is-invalid', invalid);
    }
    return !invalid;
  }

  function setProfileAvatarHint(text, ok) {
    var hint = form ? qs('[data-profile-avatar-hint]', form) : null;
    if (!hint) {
      return;
    }
    hint.textContent = text || '';
    hint.classList.toggle('success', !!ok);
    hint.classList.toggle('error', !ok && !!text);
  }

  function markProfileAvatarChanged(changed, state) {
    state = state || {};
    state.changed = !!changed;
    var input = form ? qs('[data-profile-avatar-changed]', form) : null;
    if (input) {
      input.value = state.changed ? '1' : '0';
    }
    return state.changed;
  }

  function profileAvatarUrlChanged(originalUrl) {
    var input = form ? qs('[name="avatar_url"]', form) : null;
    return !!(input && String(input.value || '').trim() !== String(originalUrl || '').trim());
  }

  function clearProfileTagError(state) {
    state = state || {};
    var message = qs('[data-profile-tags-message]', form);
    if (message) {
      message.hidden = true;
      message.textContent = '';
    }
    qsa('[name="comment_tag_label[]"]', form).forEach(function (input) {
      input.classList.remove('is-invalid');
    });
    window.clearTimeout(state.tagMessageTimer);
    state.tagMessageTimer = null;
  }

  function showProfileTagError(payload, state) {
    state = state || {};
    var data = payload && payload.data ? payload.data : {};
    var message = qs('[data-profile-tags-message]', form);
    var inputs = qsa('[name="comment_tag_label[]"]', form);
    var index = Number.isFinite(Number(data.index)) ? Number(data.index) : -1;
    clearProfileTagError(state);
    if (index >= 0 && inputs[index]) {
      inputs[index].classList.add('is-invalid');
      if (inputs[index].focus) {
        inputs[index].focus();
      }
    } else if (data.value) {
      inputs.some(function (input) {
        if (String(input.value || '').trim().toLowerCase() === String(data.value || '').trim().toLowerCase()) {
          input.classList.add('is-invalid');
          if (input.focus) {
            input.focus();
          }
          return true;
        }
        return false;
      });
    }
    if (message) {
      message.textContent = data.message || (payload && payload.message) || t('loginFailed', '操作失败。');
      message.hidden = false;
      state.tagMessageTimer = window.setTimeout(function () {
        clearProfileTagError(state);
      }, 4200);
    }
  }

  function profileSpecialCount() {
    var count = 0;
    qsa('[name^="comment_special_enabled["]', form).forEach(function (input) {
      if (input.checked) {
        count += 1;
      }
    });
    return count;
  }

  function profileEnabledCustomTagCount() {
    var count = 0;
    qsa('[data-profile-tag-enabled]', form).forEach(function (input) {
      if (input.checked) {
        count += 1;
      }
    });
    return count;
  }

  function profileSelectedTagCount() {
    return profileSpecialCount() + profileEnabledCustomTagCount();
  }

  function enforceProfileSpecialLimit(changedInput) {
    var checked = qsa('[name^="comment_special_enabled["], [data-profile-tag-enabled]', form).filter(function (input) {
      return input.checked;
    });
    if (checked.length <= 2) {
      return;
    }
    var toDisable = checked.find(function (input) {
      return input !== changedInput;
    }) || checked[0];
    if (toDisable) {
      toDisable.checked = false;
    }
  }

  function profileCustomTagCapacity() {
    return Math.max(0, 2 - profileSpecialCount());
  }

  function profileCustomTagStorageLimit() {
    var list = qs('[data-profile-tag-list]', form);
    return Math.max(1, Number(list && list.dataset ? list.dataset.storageLimit || 5 : 5));
  }

  function syncProfileAddTagState() {
    var list = qs('[data-profile-tag-list]', form);
    var add = qs('[data-profile-add-tag]', form);
    if (!list || !add) {
      return;
    }
    var rows = qsa('.reimu-profile-tag-row', list);
    var capacity = profileCustomTagCapacity();
    var selected = 0;
    list.dataset.maxTags = String(capacity);
    add.hidden = false;
    add.disabled = rows.length >= profileCustomTagStorageLimit();
    rows.forEach(function (row) {
      var checkbox = qs('[data-profile-tag-enabled]', row);
      if (checkbox && checkbox.checked) {
        selected += 1;
        if (selected > capacity) {
          checkbox.checked = false;
          selected -= 1;
        }
      }
    });
    rows.forEach(function (row) {
      var checkbox = qs('[data-profile-tag-enabled]', row);
      var hidden = qs('[name="comment_tag_enabled[]"]', row);
      var active = !!(checkbox && checkbox.checked);
      row.classList.toggle('is-disabled', !active);
      if (hidden) {
        hidden.value = active ? '1' : '0';
      }
      if (checkbox) {
        checkbox.disabled = !active && selected >= capacity;
        checkbox.setAttribute('aria-disabled', checkbox.disabled ? 'true' : 'false');
      }
    });
  }

  function profileTagRow(tag) {
    tag = tag || {};
    var row = document.createElement('div');
    row.className = 'reimu-profile-tag-row';
    var enabled = tag.enabled !== '0';
    row.innerHTML = '<input name="comment_tag_id[]" type="hidden" value="' + escapeHtml(tag.id || '') + '">' +
      '<input name="comment_tag_enabled[]" type="hidden" value="' + (enabled ? '1' : '0') + '">' +
      '<label class="reimu-profile-tag-enabled" title="' + escapeHtml(t('enable', '启用')) + '"><input type="checkbox" data-profile-tag-enabled' + (enabled ? ' checked' : '') + '><span></span></label>' +
      '<input name="comment_tag_label[]" type="text" maxlength="8" placeholder="' + escapeHtml(t('commentTag', '标签')) + '" value="' + escapeHtml(tag.label || '') + '">' +
      '<input name="comment_tag_color[]" type="color" value="' + escapeHtml(tag.color || '#ff5252') + '">' +
      '<button type="button" class="reimu-profile-remove-tag" data-profile-remove-tag aria-label="' + escapeHtml(t('remove', '删除')) + '">×</button>';
    return row;
  }

  function renderProfileCustomTags(tags) {
    var list = qs('[data-profile-tag-list]', form);
    if (!list) {
      return;
    }
    list.innerHTML = '';
    tags = Array.isArray(tags) ? tags : [];
    tags.slice(0, profileCustomTagStorageLimit()).forEach(function (tag) {
      if (tag && tag.label) {
        list.appendChild(profileTagRow(tag));
      }
    });
    syncProfileAddTagState();
  }

  function bindPasswordToggles(scope) {
    qsa('[data-password-toggle]', scope).forEach(function (button) {
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
  }

  return {
    normalizeUrlInput: normalizeUrlInput,
    validateProfilePasswords: validateProfilePasswords,
    setProfileAvatarHint: setProfileAvatarHint,
    markProfileAvatarChanged: markProfileAvatarChanged,
    profileAvatarUrlChanged: profileAvatarUrlChanged,
    clearProfileTagError: clearProfileTagError,
    showProfileTagError: showProfileTagError,
    profileSpecialCount: profileSpecialCount,
    profileEnabledCustomTagCount: profileEnabledCustomTagCount,
    profileSelectedTagCount: profileSelectedTagCount,
    enforceProfileSpecialLimit: enforceProfileSpecialLimit,
    profileCustomTagCapacity: profileCustomTagCapacity,
    profileCustomTagStorageLimit: profileCustomTagStorageLimit,
    syncProfileAddTagState: syncProfileAddTagState,
    profileTagRow: profileTagRow,
    renderProfileCustomTags: renderProfileCustomTags,
    bindPasswordToggles: bindPasswordToggles
  };
}
