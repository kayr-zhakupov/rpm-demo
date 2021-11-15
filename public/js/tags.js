/**
 *
 */
const TagsWidgetMgmt = {
  _ajaxSubmitUrl: undefined,
  _targetUserId: undefined,
  _widgetEl: undefined,
  _isBusy: false,

  init() {
    this._ajaxSubmitUrl = window.App.ajax_tags_submit_url
    this._targetUserId = window.App.tags_target_user_id
    this._resetWidgetElement()

    document.addEventListener('input', e => {
      const tg = e.target
      if (tg.matches('.js-select-tag-choice')) {
        this._insertTagToUser(tg.value)
      }
    })

    document.addEventListener('click', e => {
      const tg = e.target
      if (tg.matches('.js-tag-insert-ajax-submit')) {
        e.preventDefault()
        this._insertTag(tg.closest('form'), (tg.name === 'tag_insert_and_add'))
        return
      }
      if (tg.matches('.js-tag-to-user-delete-submit')) {
        e.preventDefault()
        this._deleteTagToUser(tg.dataset.tagId)
      }
    })
  },

  _resetWidgetElement() {
    this._widgetEl = document.querySelector('.js-tags-widget')
  },

  _refreshBusyState(flag) {
    if (flag !== undefined) this._isBusy = flag
    this._widgetEl.classList.toggle('--busy', flag)
  },

  _fetchAction(action, params) {
    const url = this._ajaxSubmitUrl
    if (this._isBusy) return
    this._refreshBusyState(true)
    const onEnd = () => this._refreshBusyState(false)

    return fetch_post(url, Object.assign({
      action,
      target_id: this._targetUserId,
    }, params))
      .then(ajax_response_pipe)
      .then(response => {
        this._applyAjaxResponse(response, onEnd)
      })
  },

  _insertTag(form, doInsertTagToUser = false) {
    const name = form.querySelector('[name=tag_new_name]').value
    if (!name) {
      alert("Name is empty")
      return
    }
    return this._fetchAction('insert_tag', {
      name,
      do_insert_tag_to_user: (doInsertTagToUser ? '1' : ''),
    })
  },

  _insertTagToUser(tagId) {
    return this._fetchAction('insert_tag_to_user', {
      id: tagId,
    })
  },

  _deleteTagToUser(tagId) {
    return this._fetchAction('delete_tag_to_user', {
      id: tagId,
    })
  },

  _applyAjaxResponse(response, cbOnEnd) {
    if (!response) {
      cbOnEnd(false)
      return
    }

    if (response.html_widget) {
      this._widgetEl.outerHTML = response.html_widget
      this._resetWidgetElement()
    }

    cbOnEnd(true)
  },
};

after_dom(() => {
  TagsWidgetMgmt.init();
})