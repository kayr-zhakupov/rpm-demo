class LazyScrollComponent {
  _el = undefined
  _isLoadingMore = false
  _hasFullList = false
  _cbRemoveScrollListener = undefined

  constructor(el) {
    this._el = el
    this._hasFullList = !!this._el.dataset.hasFullList

    this._attachScrollListener()
    this._refreshListState()
  }

  _refreshListState() {
    const offsetY = this._el.scrollTop
    const offsetYMax = this._el.scrollHeight - this._el.clientHeight

    if (offsetYMax - offsetY <= window.App.infinite_scroll_threshold) {
      (!this._isLoadingMore) && (!this._hasFullList) && this._loadMore(this._el)
    }

    if (this._hasFullList) {
      this._onFullListLoad()
    }
  }

  _attachScrollListener() {
    const listener = () => {
      if (this._isLoadingMore) return

      this._refreshListState()
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
    const offset = (+el.dataset.count) || 0

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

        el.dataset.count = offset + (+response.count_real)

        if (response.is_last_slice) {
          this._hasFullList = true
          this._refreshListState()
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