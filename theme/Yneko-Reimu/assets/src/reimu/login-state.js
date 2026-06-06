export function createLoginStateRuntime(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var escapeHtml = deps.escapeHtml;
  var showTooltip = deps.showTooltip;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var initCommentUploadRows = deps.initCommentUploadRows || function () {};
  var initProfileModal = deps.initProfileModal || function () {};
  var initLoginModal = deps.initLoginModal || function () {};
  var setLoginModalOpen = deps.setLoginModalOpen || function () {};

  function applyCommentLoggedInState(data) {
    var config = getConfig();
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
    var config = getConfig();
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
    var config = getConfig();
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
        var config = getConfig();
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

  return {
    applyCommentLoggedInState: applyCommentLoggedInState,
    applyCommentLoggedOutState: applyCommentLoggedOutState,
    initProfileOpenDelegation: initProfileOpenDelegation,
    refreshCommentLoginState: refreshCommentLoginState,
    openAuthPopup: openAuthPopup,
    initGithubPopupLogin: initGithubPopupLogin,
    initAuthPopupLinks: initAuthPopupLinks,
    initCommentLoginTriggers: initCommentLoginTriggers,
    initCommentAjaxLogout: initCommentAjaxLogout
  };
}
