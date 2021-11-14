/**
 * @var {{ infinite_scroll_threshold: number, }} App
 */
/**
 * @typedef {{ html: string, }} TProfilesListResponse
 */
class LazyScrollComponent {
  _scrollableEl = undefined
  _isBusy = false
  _hasFullList = false
  _cbRemoveScrollListener = undefined

  constructor(el) {
    this._scrollableEl = el
    this._hasFullList = !!this._scrollableEl.dataset.hasFullList

    this._attachScrollListener()
    this._attachSearchFilterListener()
    this._refreshListState()
  }

  _refreshBusyState(flag) {
    if (flag !== undefined) this._isBusy = flag
    this._scrollableEl.classList.toggle('--busy', this._isBusy)
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

    const loadMoreEl = this._scrollableEl.querySelector('.js-load-more')
    if (loadMoreEl) {
      loadMoreEl.style.visibility = this._hasFullList ? 'collapse' : 'visible'
    }
  }

  _clearList() {
    this._scrollableEl.querySelectorAll('.js-friend-tile')
      .forEach(el => el.remove())
  }

  _reloadList() {
    this._refreshBusyState(true)
    const onEnd = () => {
      this._refreshBusyState(false)
    }
    this._clearList()

    this._fetchFriendsSlice(undefined, 0, this._getTagFilterIds())
      .then(response => {
        this._applySliceResponse(response, onEnd)
      })
  }

  /**
   * @param {TProfilesListResponse} response
   * @param cbOnEnd
   * @private
   */
  _applySliceResponse(response, cbOnEnd) {
    if (!response) {
      cbOnEnd(false)
      return
    }

    if (response.html !== undefined) {
      const elAppendBefore = this._scrollableEl.querySelector('.js-load-more-before')
      if (elAppendBefore) {
        elAppendBefore.insertAdjacentHTML('beforebegin', response.html)
      } else {
        this._scrollableEl.insertAdjacentHTML('beforeend', response.html)
      }

      this._scrollableEl.dataset.count = (+response.offset) + (+response.count_real)
    }

    if (response.is_last_slice) {
      this._hasFullList = true
      this._refreshListState()
    }

    this._scrollableEl.dataset.tags = response.tags_str

    cbOnEnd(true)
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
    if (tagIds === undefined) tagIds = this._scrollableEl.dataset.tags || ''

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
  }

  _loadMore() {
    console.log('_loadMore')
    this._refreshBusyState(true)
    const onEnd = () => {
      this._refreshBusyState(false)
    }

    this._fetchFriendsSlice()
      .then(response => {
        this._applySliceResponse(response, onEnd)
      })
  }
}

after_dom(() => {
  document.querySelectorAll('.js-infinite-scroll').forEach((el) => {
    new LazyScrollComponent(el)
  })
});