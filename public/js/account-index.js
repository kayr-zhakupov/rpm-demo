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

const InfiniteScrollHandler = {

  init() {
    this.attachListenerTo(document.querySelector('.js-infinite-scroll'))
  },

  attachListenerTo(el) {
    el.addEventListener('scroll', e => {
      const tg = e.target
      const offsetY = tg.scrollTop
      const offsetYMax = tg.scrollHeight - tg.clientHeight
      if (offsetYMax - offsetY < 64) console.log('BOTTOM');
    })
  },
}

after_dom(() => {
  InfiniteScrollHandler.init()
});