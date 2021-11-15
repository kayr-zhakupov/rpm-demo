/**
 * @var {{ infinite_scroll_threshold: number, }} App
 */
/**
 * @typedef {{ html_slice: string, html_head: string, }} TProfilesListResponse
 */
class LazyScrollComponent {
  _scrollableEl = undefined
  _isBusy = false
  _hasFullList = false

  constructor(el) {
    this._scrollableEl = el
    this._hasFullList = !!this._scrollableEl.dataset.hasFullList

    this._initScrollListener()
    this._attachSearchFilterListener()
    this._refreshListState()
  }

  _refreshBusyState(flag) {
    if (flag !== undefined) this._isBusy = flag

    const loadMoreEl = this._scrollableEl.querySelector('.js-load-more')
    if (loadMoreEl) {
      loadMoreEl.style.visibility = (!this._isBusy && this._hasFullList) ? 'collapse' : 'visible'
    }
  }

  _refreshListState() {
    const offsetY = this._scrollableEl.scrollTop
    const offsetYMax = this._scrollableEl.scrollHeight - this._scrollableEl.clientHeight

    if (offsetYMax - offsetY <= window.App.infinite_scroll_threshold) {
      (!this._isBusy) && (!this._hasFullList) && this._loadMore()
    }

    this._refreshBusyState()
  }

  _clearList() {
    this._scrollableEl.querySelectorAll('.js-friend-tile')
      .forEach(el => el.remove())
  }

  _reloadList() {
    this._hasFullList = false
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

    if (response.html_slice !== undefined) {
      const elAppendBefore = this._scrollableEl.querySelector('.js-load-more-before')
      if (elAppendBefore) {
        elAppendBefore.insertAdjacentHTML('beforebegin', response.html_slice)
      } else {
        this._scrollableEl.insertAdjacentHTML('beforeend', response.html_slice)
      }

      this._scrollableEl.dataset.count = (+response.offset) + (+response.slice_length)
    }

    if (response.html_head !== undefined) {
      document.querySelector('.js-profiles-catalog-head')
        .outerHTML = response.html_head
    }

    if (response.is_last_slice) {
      this._hasFullList = true
    }

    this._scrollableEl.dataset.tags = response.tags_str

    cbOnEnd(true)
    this._refreshListState()
  }

  _initScrollListener() {
    this._scrollableEl.addEventListener('scroll', () => {
      if (this._hasFullList) return
      if (this._isBusy) return
      this._refreshListState()
    })
  }

  _attachSearchFilterListener() {
    document.addEventListener('click', (e) => {
      const tg = e.target
      if (tg.matches('.js-tag-filter-submit')) {
        e.preventDefault();
        (!this._isBusy) && this._reloadList()
        return
      }
      if (tg.matches('.js-tag-filter-select-none')) {
        e.preventDefault()
        this._toggleTagsAll(false)
        return
      }
      if (tg.matches('.js-tag-filter-select-all')) {
        e.preventDefault()
        this._toggleTagsAll(true)
        return
      }
    })
  }

  _toggleTagsAll(flag) {
    document
      .querySelectorAll('.js-tags-filter .js-tag-filter-checkbox')
      .forEach(el => {
        el.checked = flag
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
    url.searchParams.append('friends_with', window.App.tags_target_user_id || '')

    return fetch(url, {
      method: 'get',
    })
      .then(ajax_response_pipe)
  }

  _loadMore() {
    this._refreshBusyState(true)
    const onEnd = () => this._refreshBusyState(false)

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