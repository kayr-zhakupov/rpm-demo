<?php
/**
 * @see views/pages/account/index.php:59
 *
 * @var \App\Models\TagRecord[] $all_tags
 * @var \App\Models\TagRecord[] $profile_tags
 */

use App\Repo\Routes;

?>

<div class="js-tags-widget">

  <div class="profile-tags">
    <?php foreach ($profile_tags as $tag): ?>
      <div class="profile-tag-badge"><?= $tag->name ?></div>
    <?php endforeach; ?>
  </div>

  <hr>

  <form
    class="js-tags-widget-form"
    action="<?= Routes::i()->ajaxTags() ?>"
    method="post"
  >

    <label for="tag-new-name">Имя нового тега</label>
    <input name="tag_new_name" id="tag-new-name">

    <button
      class="js-tags-ajax-submit"
      type="submit" name="tag_insert" value="tag_insert"
    >Создать новый тег
    </button>

    <button
      class="js-tags-ajax-submit"
      type="submit" name="tag_insert_and_add" value="tag_insert_and_add"
    >Создать новый тег и назначить
    </button>

    <hr>

    <label for="tag-choice">Добавить тег</label>
    <select
      id="tag-choice" name="tag_choice"
      class="js-select-tag-choice"
    >
      <option value="" selected></option>
      <?php foreach ($all_tags as $tag): ?>
        <option value="<?= $tag->id ?>"><?= $tag->name ?></option>
      <?php endforeach; ?>
    </select>

  </form>

</div>