/**
 * Функция из исходников Bootstrap - гарантированный запуск логики только после загрузки DOM.
 * @param callback
 */
function after_dom(callback) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', callback)
  } else {
    callback()
  }
}