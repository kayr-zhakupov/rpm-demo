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
        this._tagAdd()
      }
    })
  },

  _tagAdd(tag_id) {
    
    console.log(tg.value, this._ajaxSubmitUrl)
  }
};

after_dom(() => {
  TagsWidgetMgmt.init();
})