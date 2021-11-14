/**
 *
 */
const TagsWidgetMgmt = {
  _ajaxSubmitUrl: undefined,

  init() {

    this._ajaxSubmitUrl = window.App.ajax_tags_submit_url

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
      }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
      .then(raw => this._ajaxResponsePipe(raw))
      .then(response => {
        if (response === undefined) return

        console.log(response)
      })

    console.log(tg.value,)
  }
};

after_dom(() => {
  TagsWidgetMgmt.init();
})