export function debounce(fn, delay) {
  var timer = null;
  return function () {
    var args = arguments;
    window.clearTimeout(timer);
    timer = window.setTimeout(function () {
      fn.apply(null, args);
    }, delay);
  };
}

export function dispatchReimuEvent(name, detail) {
  var event;
  try {
    event = new CustomEvent(name, { detail: detail || {} });
  } catch (error) {
    event = document.createEvent('CustomEvent');
    event.initCustomEvent(name, false, false, detail || {});
  }
  window.dispatchEvent(event);
  document.dispatchEvent(event);
}

export function dispatchInputEvent(element) {
  var event;
  try {
    event = new Event('input', { bubbles: true });
  } catch (error) {
    event = document.createEvent('Event');
    event.initEvent('input', true, false);
  }
  element.dispatchEvent(event);
}
