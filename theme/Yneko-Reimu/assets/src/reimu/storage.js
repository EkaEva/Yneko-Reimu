export function storageGet(key) {
  try {
    return window.localStorage ? localStorage.getItem(key) || '' : '';
  } catch (error) {
    return '';
  }
}

export function storageSet(key, value) {
  try {
    if (window.localStorage) {
      localStorage.setItem(key, value || '');
    }
  } catch (error) {}
}

export function storageRemove(key) {
  try {
    if (window.localStorage) {
      localStorage.removeItem(key);
    }
  } catch (error) {}
}
