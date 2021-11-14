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
        this._tagAdd(tg.value)
      }
    })
  },

  _ajaxResponsePipe(raw) {
    if (!!(raw && (raw.status === 200))) {
      return raw.json && raw.json()
    }

    document.querySelector('.js-general-server-error').classList.add('--show')
    return undefined
  },

  _tagAdd(tagId) {
    const url = this._ajaxSubmitUrl
    return fetch(url, {
      method: 'post',
      body: build_form_query({
        action: 'add',
        id: tagId,
        target_id: this._targetUserId,
      }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
      .then(raw => this._ajaxResponsePipe(raw))
      .then(response => {
        if (response === undefined) return

        const toasts = response.toasts
        if (toasts) {
          toasts.forEach(Toasts.pushToastHtml)
        }

        console.log(response)
      })
  }
};

after_dom(() => {
  TagsWidgetMgmt.init();
})