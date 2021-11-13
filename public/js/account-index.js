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
  _isLoadingMore: false,

  init() {
    this.attachListenerTo(document.querySelector('.js-infinite-scroll'))
  },

  attachListenerTo(el) {
    el.addEventListener('scroll', e => {

      if (this._isLoadingMore) return;

      const tg = e.target
      const offsetY = tg.scrollTop
      const offsetYMax = tg.scrollHeight - tg.clientHeight
      if (offsetYMax - offsetY <= window.App.infiniteScrollThreshold) {
        this.loadMore(tg, () => {
          this._isLoadingMore = false
        })
      }

    })
  },

  loadMore(el, onEnd) {
    const elAppendBefore = el.querySelector('.js-load-more-before')

    if (elAppendBefore) {
      elAppendBefore.insertAdjacentHTML('beforebegin', "<div class='friend-tile'>XXX</div>")
    } else {
      el.insertAdjacentHTML('beforeend', "<div class='friend-tile'>YYY</div>")
    }

    onEnd && onEnd()
  }
}

after_dom(() => {
  InfiniteScrollHandler.init()
});