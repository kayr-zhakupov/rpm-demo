/**
 *
 */
const TagsWidgetMgmt = {
  _ajaxSubmitUrl: undefined,
  _targetUserId: undefined,

  init() {

    this._ajaxSubmitUrl = window.App.ajax_tags_submit_url
    this._targetUserId = window.App.tags_target_user_id

    document.addEventListener('input', e => {
      const tg = e.target
      if (tg.matches('.js-select-tag-choice')) {
        this._insertTagToUser(tg.value)
      }
    })

    document.addEventListener('click', e => {
      const tg = e.target
      if (tg.matches('.js-tags-ajax-submit')) {
        e.preventDefault()
        this._insertTag(tg)
      }
    })
  },

  _ajaxResponsePipe(raw) {
    let response = undefined

    if (!!(raw && (raw.status === 200))) {
      response = raw.json && raw.json()
    } else {
      document.querySelector('.js-general-server-error').classList.add('--show')
      return response
    }

    if (response.toasts) response.toasts.forEach(Toasts.pushToastHtml)

    return response
  },

  _insertTag(form) {
    const name = form.querySelector('[name=tag_new_name]')
    const url = this._ajaxSubmitUrl
    return fetch(url, {
      method: 'post',
      body: build_form_query({
        action: 'insert_tag',
        name: name,
      }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
      .then(raw => this._ajaxResponsePipe(raw))
  },

  _insertTagToUser(tagId) {
    const url = this._ajaxSubmitUrl
    return fetch(url, {
      method: 'post',
      body: build_form_query({
        action: 'insert_tag_to_user',
        id: tagId,
        target_id: this._targetUserId,
      }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
      .then(raw => this._ajaxResponsePipe(raw))
  }
};

after_dom(() => {
  TagsWidgetMgmt.init();
})