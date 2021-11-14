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

function build_form_query(params) {
  return Object.entries(params).map(([k,v])=>{return k+'='+v}).join('&')
}

function ajax_response_pipe(raw) {
  if (!!(raw && (raw.status === 200))) {
    return raw
      .json()
      .then(response => {
        if (response.toasts) response.toasts.forEach(Toasts.pushToastHtml)
        return response
      })
  }

  document.querySelector('.js-general-server-error').classList.add('--show')
  return undefined;
}