export function createAuthForms(deps) {
  deps = deps || {};
  var qs = deps.qs;
  var qsa = deps.qsa;
  var t = deps.t;
  var storageGet = deps.storageGet;
  var storageSet = deps.storageSet;
  var storageRemove = deps.storageRemove;
  var getConfig = typeof deps.getConfig === 'function' ? deps.getConfig : function () { return {}; };
  var refreshCommentLoginState = deps.refreshCommentLoginState || function () { return Promise.resolve(false); };
  var setLoginModalOpen = deps.setLoginModalOpen || function () {};

  function bindPasswordToggles(modal) {
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
  }

  function bindLoginForm(form, modal, setPanel) {
    if (!form) {
      return;
    }
    var config = getConfig();
    var message = qs('[data-login-message]', modal);
    var submit = qs('.reimu-login-submit', modal);
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
      config = getConfig();
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

  function bindCodeButton(authForm, selector, action, nonceKey, fields, messageSelector, timerState) {
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
      var config = getConfig();
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
          window.clearInterval(timerState.value);
          setCodeCountdown(button, 60, function (timer) {
            timerState.value = timer;
          });
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

  function bindSimpleAuthForm(authForm, loginForm, action, nonceKey, messageSelector, setPanel) {
    if (!authForm) {
      return;
    }
    var authMessage = qs(messageSelector, authForm);
    var authSubmit = qs('[type="submit"]', authForm);
    authForm.addEventListener('submit', function (event) {
      var config = getConfig();
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
          var loginEmail = qs('[name="log"]', loginForm);
          var loginPassword = qs('[name="pwd"]', loginForm);
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

  function bindAuthForms(modal, options) {
    options = options || {};
    var form = options.form || qs('[data-reimu-login-form]', modal);
    var registerForm = options.registerForm || qs('[data-reimu-register-form]', modal);
    var lostForm = options.lostForm || qs('[data-reimu-lost-form]', modal);
    var setPanel = typeof options.setPanel === 'function' ? options.setPanel : function () {};
    var registerCodeTimer = { value: null };
    var lostCodeTimer = { value: null };
    bindLoginForm(form, modal, setPanel);
    bindPasswordToggles(modal);
    bindCodeButton(registerForm, '[data-register-code-send]', 'yneko_reimu_register_code', 'registerCodeNonce', ['display_name', 'user_email'], '[data-register-message]', registerCodeTimer);
    bindCodeButton(lostForm, '[data-lost-code-send]', 'yneko_reimu_lostpassword_code', 'lostCodeNonce', ['user_login'], '[data-lost-message]', lostCodeTimer);
    bindSimpleAuthForm(registerForm, form, 'yneko_reimu_register', 'registerNonce', '[data-register-message]', setPanel);
    bindSimpleAuthForm(lostForm, form, 'yneko_reimu_lostpassword', 'lostNonce', '[data-lost-message]', setPanel);
  }

  return {
    bindAuthForms: bindAuthForms,
    bindLoginForm: bindLoginForm,
    bindPasswordToggles: bindPasswordToggles,
    bindCodeButton: bindCodeButton,
    bindSimpleAuthForm: bindSimpleAuthForm
  };
}
