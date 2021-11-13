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
      if (offsetYMax - offsetY <= window.App.infinite_scroll_threshold) {
        this.loadMore(tg, () => {
          this._isLoadingMore = false
        })
      }
    })
  },

  _fetchFriendsSlice(count, offset) {
    const url = new URL(window.App.ajax_get_friends_slice_url)
    url.searchParams.append('count', count)
    url.searchParams.append('offset', offset)

    return fetch(url, {
      method: 'get',
    })
      .then(raw => {
        const isOk = !!(raw && (raw.status === 200))

        if (isOk) {
          return raw.json && raw.json()
        }

        return undefined
      })
  },

  loadMore(el, onEnd) {
    const elAppendBefore = el.querySelector('.js-load-more-before')

    const count = window.App.friends_slice_count_next
    const offset = el.dataset.offset || 0

    this._fetchFriendsSlice(count, offset)
      .then(response => {
        if (!response) {
          onEnd(false)
          return;
        }

        console.log(response)

        onEnd(true)
      })

    const html = "<div class='friend-tile'>X<br>Y<br>Z<br>A<br>B</div>".repeat(count)

    if (elAppendBefore) {
      elAppendBefore.insertAdjacentHTML('beforebegin', html)
    } else {
      el.insertAdjacentHTML('beforeend', html)
    }

    el.dataset.offset = offset + count

    onEnd && onEnd()
  }
}

after_dom(() => {
  InfiniteScrollHandler.init()
});