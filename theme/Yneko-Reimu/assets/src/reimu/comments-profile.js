import { createAuthForms } from './auth-forms.js';
import { createCommentList } from './comment-list.js';
import { createCommentMutations } from './comment-mutations.js';
import { createLoginStateRuntime } from './login-state.js';
import { createCommentMedia } from './comment-media.js';
import { createCommentUploadRuntime } from './comment-upload.js';
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

  var commentUploadRuntime = createCommentUploadRuntime({
    qs: qs,
    qsa: qsa,
    t: t,
    showTooltip: showTooltip,
    getConfig: function () {
      return config;
    },
    commentMediaEntries: commentMediaEntries,
    confirmCommentMediaReplace: confirmCommentMediaReplace,
    insertCommentMedia: insertCommentMedia
  });
  var initCommentUploadRows = commentUploadRuntime.initCommentUploadRows;

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

  var commentMutations = createCommentMutations({
    qs: qs,
    qsa: qsa,
    t: t,
    escapeHtml: escapeHtml,
    dispatchInputEvent: dispatchInputEvent,
    showTooltip: showTooltip,
    requestThemeConfirm: requestThemeConfirm,
    resolveCommentMediaTokens: resolveCommentMediaTokens,
    commentMediaLimitOk: commentMediaLimitOk,
    setCommentToolState: setCommentToolState,
    closeCommentPopovers: closeCommentPopovers,
    sortCommentList: sortCommentList,
    getActiveCommentSortMode: getActiveCommentSortMode,
    syncLoadMoreRoot: syncLoadMoreRoot,
    getConfig: function () {
      return config;
    },
    initWordPressCommentForm: function () {
      initWordPressCommentForm();
    }
  });
  var updateCommentCount = commentMutations.updateCommentCount;
  var clearCommentForm = commentMutations.clearCommentForm;
  var appendSubmittedComment = commentMutations.appendSubmittedComment;
  var initAjaxCommentSubmit = commentMutations.initAjaxCommentSubmit;
  var initCommentLikes = commentMutations.initCommentLikes;
  var initCommentOwnerActions = commentMutations.initCommentOwnerActions;
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

    createAuthForms({
      qs: qs,
      qsa: qsa,
      t: t,
      storageGet: storageGet,
      storageSet: storageSet,
      storageRemove: storageRemove,
      getConfig: function () {
        return config;
      },
      refreshCommentLoginState: function () {
        return refreshCommentLoginState();
      },
      setLoginModalOpen: function (open) {
        setLoginModalOpen(open);
      }
    }).bindAuthForms(modal, {
      form: form,
      registerForm: registerForm,
      lostForm: lostForm,
      setPanel: setPanel
    });
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
    var profileInitialState = {};
    var profileTwoFactorActive = false;
    var profileTwoFactorSetupRequested = false;
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

    function profileFieldValue(name) {
      var input = form ? qs('[name="' + name + '"]', form) : null;
      if (!input) {
        return '';
      }
      if (input.type === 'checkbox') {
        return input.checked ? '1' : '0';
      }
      return String(input.value || '').trim();
    }

    function captureProfileInitialState(data) {
      data = data || {};
      profileInitialState = {
        displayName: String(data.displayName || '').trim(),
        profileUrl: String(data.profileUrl || '').trim(),
        email: String(data.email || '').trim(),
        avatarFrameEnabled: data.avatarFrameEnabled !== false ? '1' : '0',
        twoFactor: data.twoFactor ? '1' : '0'
      };
    }

    function hasGeneralProfileChanges() {
      var newEmail = profileFieldValue('user_email');
      return profileFieldValue('display_name') !== profileInitialState.displayName ||
        profileFieldValue('profile_url') !== profileInitialState.profileUrl ||
        (newEmail && newEmail !== profileInitialState.email) ||
        !!profileFieldValue('new_password') ||
        profileFieldValue('avatar_frame_enabled') !== profileInitialState.avatarFrameEnabled ||
        profileFieldValue('totp_enabled') !== profileInitialState.twoFactor;
    }

    function syncTwoFactorSetup() {
      var wrap = qs('.reimu-profile-2fa', form);
      var toggle = qs('[data-profile-2fa-toggle]', form);
      var setup = qs('[data-profile-2fa-setup]', form);
      if (!wrap || !toggle || !setup) {
        return;
      }
      wrap.setAttribute('data-profile-2fa-active', profileTwoFactorActive ? '1' : '0');
      var showSetup = !profileTwoFactorActive && profileTwoFactorSetupRequested && toggle.checked;
      setup.hidden = !showSetup;
      if (profileTwoFactorActive || !showSetup) {
        var secret = qs('[data-profile-2fa-secret]', setup);
        var qr = qs('[data-profile-2fa-qr]', setup);
        var code = qs('[name="totp_code"]', setup);
        if (secret) {
          secret.textContent = '';
        }
        if (qr) {
          qr.removeAttribute('src');
          qr.hidden = true;
        }
        if (code) {
          code.value = '';
        }
        qsa('input, button', setup).forEach(function (control) {
          control.disabled = true;
        });
      } else {
        qsa('input, button', setup).forEach(function (control) {
          control.disabled = false;
        });
      }
    }

    function addGeneralProfileStatus(data) {
      var statuses = Object.assign({}, data && data.reviewStatuses ? data.reviewStatuses : {});
      statuses.profile = { status: 'updated' };
      return Object.assign({}, data || {}, { reviewStatuses: statuses });
    }

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
        profileTwoFactorSetupRequested = false;
        syncTwoFactorSetup();
        refreshProfile();
      } else if (form) {
        form.reset();
        profileTwoFactorSetupRequested = false;
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
        twoFactor.defaultChecked = !!data.twoFactor;
      }
      profileTwoFactorActive = !!data.twoFactor;
      profileTwoFactorSetupRequested = false;
      var twoFactorWrap = qs('.reimu-profile-2fa', form);
      if (twoFactorWrap) {
        twoFactorWrap.setAttribute('data-profile-2fa-active', profileTwoFactorActive ? '1' : '0');
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
      captureProfileInitialState(data);
      syncTwoFactorSetup();
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
      syncTwoFactorSetup();
      twoFactorToggle.addEventListener('change', function () {
        profileTwoFactorSetupRequested = !profileTwoFactorActive && twoFactorToggle.checked;
        syncTwoFactorSetup();
      });
    }

    var generate2fa = qs('[data-profile-2fa-generate]', form);
    if (generate2fa) {
      generate2fa.addEventListener('click', function () {
        profileTwoFactorSetupRequested = !profileTwoFactorActive;
        syncTwoFactorSetup();
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
        var generalChanged = hasGeneralProfileChanges();
        postProfileAction('yneko_reimu_profile_save', data).then(function (payload) {
          setMessage(payload && payload.data && payload.data.message ? payload.data.message : '', payload && payload.success);
          if (payload && payload.success) {
            clearProfileTagError();
            var profileData = generalChanged ? addGeneralProfileStatus(payload.data) : payload.data;
            var stillPending = applyProfilePayload(profileData, { autohide: true, forceFill: true });
            if (stillPending || hasPendingProfileStatus(profileData && profileData.reviewStatuses)) {
              startProfileStatusPolling();
            } else if (avatarChanged && payload.data && payload.data.avatarUrl) {
              applyInlineProfileStatus(profileData, { autohide: true });
            }
            setOpen(false);
            Promise.resolve(profileData && profileData.identity ? true : refreshCommentLoginState()).then(function () {
              initCommentAjaxLogout();
              applyInlineProfileStatus(profileData, { autohide: true });
            });
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

  var loginStateRuntime = createLoginStateRuntime({
    qs: qs,
    qsa: qsa,
    t: t,
    escapeHtml: escapeHtml,
    showTooltip: showTooltip,
    getConfig: function () {
      return config;
    },
    initCommentUploadRows: initCommentUploadRows,
    initProfileModal: function () {
      initProfileModal();
    },
    initLoginModal: function () {
      initLoginModal();
    },
    setLoginModalOpen: function (open) {
      setLoginModalOpen(open);
    }
  });
  var applyCommentLoggedInState = loginStateRuntime.applyCommentLoggedInState;
  var applyCommentLoggedOutState = loginStateRuntime.applyCommentLoggedOutState;
  function initProfileOpenDelegation() {
    return loginStateRuntime.initProfileOpenDelegation();
  }
  function refreshCommentLoginState() {
    return loginStateRuntime.refreshCommentLoginState();
  }
  var initGithubPopupLogin = loginStateRuntime.initGithubPopupLogin;
  var initAuthPopupLinks = loginStateRuntime.initAuthPopupLinks;
  var initCommentLoginTriggers = loginStateRuntime.initCommentLoginTriggers;
  var initCommentAjaxLogout = loginStateRuntime.initCommentAjaxLogout;
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
