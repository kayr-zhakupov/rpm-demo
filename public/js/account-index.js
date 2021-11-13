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

class LazyScrollComponent {
  _el = undefined
  _isLoadingMore = false
  _hasFullList = false
  _cbRemoveScrollListener = undefined

  constructor(el) {
    this._el = el
    this._hasFullList = !!this._el.dataset.hasFullList

    this._attachListener()
    this._refreshList()
  }

  _refreshList() {
    const offsetY = this._el.scrollTop
    const offsetYMax = this._el.scrollHeight - this._el.clientHeight

    if (offsetYMax - offsetY <= window.App.infinite_scroll_threshold) {
      this._loadMore(this._el)
    }
  }

  _attachListener() {
    const listener = e => {
      console.log('onscroll')
      if (this._isLoadingMore) return

      const tg = e.target
      this._refreshList(tg)
    }

    this._el.addEventListener('scroll', listener)
    this._cbRemoveScrollListener = () => {
      this._el.removeEventListener('scroll', listener)
    }
  }

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
  }

  _onFullListLoad() {
    if (this._cbRemoveScrollListener) {
      this._cbRemoveScrollListener()
      this._cbRemoveScrollListener = undefined
    }

    this._el.querySelector('.js-load-more').remove()
  }

  _loadMore(el) {
    this._isLoadingMore = true
    const onEnd = () => {
      this._isLoadingMore = false
    }

    const elAppendBefore = el.querySelector('.js-load-more-before')

    const count = (+window.App.friends_slice_count_next)
    const offset = (+el.dataset.offset) || 0

    this._fetchFriendsSlice(count, offset)
      .then(response => {
        if (!response) {
          onEnd(false)
          return;
        }

        const html = response.html

        if (elAppendBefore) {
          elAppendBefore.insertAdjacentHTML('beforebegin', html)
        } else {
          el.insertAdjacentHTML('beforeend', html)
        }

        el.dataset.offset = offset + count

        if (response.is_last_slice) {
          this._onFullListLoad()
        }

        onEnd(true)
      })
  }
}

after_dom(() => {
  document.querySelectorAll('.js-infinite-scroll').forEach((el) => {
    new LazyScrollComponent(el)
  })
});