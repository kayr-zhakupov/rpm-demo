after_dom(() => {
  document.addEventListener('click', (e) => {
    const tg = e.target
    if (tg.matches('.js-close-toast')) {
      const toastEl = tg.closest('.js-toast')
      toastEl && toastEl.classList.remove('--show')
    }
  })
})