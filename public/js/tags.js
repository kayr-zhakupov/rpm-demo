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
      if (tg.matches('.js-tag-insert-ajax-submit')) {
        e.preventDefault()
        this._insertTag(tg.closest('form'))
      }
    })
  },

  _insertTag(form) {
    const name = form.querySelector('[name=tag_new_name]').value
    if (!name) {
      alert("Name is empty")
      return
    }

    const url = this._ajaxSubmitUrl
    return fetch(url, {
      method: 'post',
      body: build_form_query({
        action: 'insert_tag',
        name: name,
      }),
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
    })
      .then(ajax_response_pipe)
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
      .then(ajax_response_pipe)
  }
};

after_dom(() => {
  TagsWidgetMgmt.init();
})