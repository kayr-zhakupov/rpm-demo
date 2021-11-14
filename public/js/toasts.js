/**
 *
 */
const Toasts = {

  init() {
    document.addEventListener('click', (e) => {
      const tg = e.target
      if (tg.matches('.js-close-toast')) {
        const toastEl = tg.closest('.js-toast')
        toastEl && toastEl.classList.remove('--show')
      }
    })
  },

  pushToastHtml(html) {
    const containerEl = document.querySelector('.js-toast-container')
    containerEl && containerEl.insertAdjacentHTML('beforeend', html)
  },
}

after_dom(() => {
  Toasts.init()
})