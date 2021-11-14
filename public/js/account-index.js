class LazyScrollComponent {
  _scrollableEl = undefined
  _isBusy = false
  _cvAfterBusyUnlock = undefined
  _hasFullList = false
  _cbRemoveScrollListener = undefined

  constructor(el) {
    this._scrollableEl = el
    this._hasFullList = !!this._scrollableEl.dataset.hasFullList

    this._attachScrollListener()
    this._attachSearchFilterListener()
    this._refreshListState()
  }

  _refreshListState() {
    const offsetY = this._scrollableEl.scrollTop
    const offsetYMax = this._scrollableEl.scrollHeight - this._scrollableEl.clientHeight

    if (offsetYMax - offsetY <= window.App.infinite_scroll_threshold) {
      (!this._isBusy) && (!this._hasFullList) && this._loadMore()
    }

    if (this._hasFullList) {
      this._onFullListLoad()
    }
  }

  _reloadList() {
    this._fetchFriendsSlice(undefined, 0)
      .then(response => {
        // console.log(response)
      })
  }

  _attachScrollListener() {
    const listener = () => {
      if (this._isBusy) return

      this._refreshListState()
    }

    this._scrollableEl.addEventListener('scroll', listener)
    this._cbRemoveScrollListener = () => {
      this._scrollableEl.removeEventListener('scroll', listener)
    }
  }

  _attachSearchFilterListener() {
    document.addEventListener('click', (e) => {
      const tg = e.target
      if (tg.matches('.js-tag-filter-submit')) {
        (!this._isBusy) && this._reloadList()
      }
    })
  }

  _getTagFilterIds() {
    const ids = []
    const checkboxEls = document.querySelectorAll('.js-tags-filter .js-tag-filter-checkbox')
    checkboxEls.forEach(el => {
      el.checked && ids.push(el.dataset.id)
    })
    return ((checkboxEls.length === ids.length) || (!ids.length)) ? '' : ids.join(',')
  }

  _fetchFriendsSlice(count, offset, tagIds) {
    if (count === undefined) count = (+window.App.friends_slice_count_next)
    if (offset === undefined) offset = (+this._scrollableEl.dataset.count) || 0
    if (tagIds === undefined) tagIds = this._getTagFilterIds()

    console.log(tagIds)

    const url = new URL(window.App.ajax_get_friends_slice_url)
    url.searchParams.append('count', count)
    url.searchParams.append('offset', offset)
    url.searchParams.append('tags', tagIds)

    return fetch(url, {
      method: 'get',
    })
      .then(ajax_response_pipe)
  }

  _onFullListLoad() {
    if (this._cbRemoveScrollListener) {
      this._cbRemoveScrollListener()
      this._cbRemoveScrollListener = undefined
    }

    this._scrollableEl.querySelector('.js-load-more').remove()
  }

  _loadMore() {
    const el = this._scrollableEl
    this._isBusy = true
    const onEnd = () => {
      this._isBusy = false
    }

    const elAppendBefore = el.querySelector('.js-load-more-before')

    this._fetchFriendsSlice()
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

        el.dataset.count = (+response.offset) + (+response.count_real)

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