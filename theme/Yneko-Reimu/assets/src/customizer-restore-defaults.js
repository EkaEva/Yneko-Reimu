(function(window, document) {
  'use strict';

  var config = window.YNEKO_REIMU_CUSTOMIZER_RESTORE || {};
  var groups = config.groups || {};
  var trackingSetting = config.trackingSetting || 'yneko_reimu_customizer_reset_groups';

  function setting(id) {
    if (!window.wp || !wp.customize) {
      return null;
    }

    return wp.customize(id);
  }

  function formatConfirm(label) {
    var template = config.confirmTemplate || 'Restore "%s" to defaults?';
    return template.replace('%s', label);
  }

  function markGroup(groupId) {
    var control = setting(trackingSetting);
    if (!control) {
      return;
    }

    var current = String(control.get() || '').split(',').filter(Boolean);
    if (current.indexOf(groupId) === -1) {
      current.push(groupId);
    }
    control.set(current.join(','));
  }

  function resetGroup(groupId) {
    var group = groups[groupId];
    if (!group || !group.settings) {
      return;
    }

    if (!window.confirm(formatConfirm(group.label || groupId))) {
      return;
    }

    var missing = [];
    Object.keys(group.settings).forEach(function(settingId) {
      var control = setting(settingId);
      if (!control) {
        missing.push(settingId);
        return;
      }
      control.set(group.settings[settingId]);
    });

    if (missing.length && config.missingSetting) {
      window.alert(config.missingSetting);
    }

    markGroup(groupId);
  }

  document.addEventListener('click', function(event) {
    var button = event.target && event.target.closest ? event.target.closest('[data-reset-group]') : null;
    if (!button) {
      return;
    }

    event.preventDefault();
    resetGroup(button.getAttribute('data-reset-group'));
  });
})(window, document);
