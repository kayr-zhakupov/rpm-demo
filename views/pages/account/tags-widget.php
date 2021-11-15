<?php
/**
 * @see views/pages/account/index.php:62
 *
 * @var \App\Models\TagRecord[] $all_tags
 * @var \App\Models\TagRecord[] $profile_tags
 */

use App\Repo\Routes;
use App\Repo\Tags;

$unsetTags = Tags::i()->subtractTagSets($all_tags, $profile_tags);

?>

<div class="js-tags-widget">

  <form
    class="js-tags-widget-form"
    action="<?= Routes::i()->ajaxTags() ?>"
    method="post"
  >

    <div class="tags-for-profile">
      <?php foreach ($profile_tags as $tag): ?>
        <div class="tag-for-profile-badge">
          <?= $tag->name ?>
          <button
            type="submit"
            class="js-tag-to-user-delete-submit"
            data-tag-id="<?= $tag->id ?>"
          >[X]
          </button>
        </div>
      <?php endforeach; ?>
    </div>

    <hr>

    <label for="tag-new-name">Имя нового тега</label>
    <input name="tag_new_name" id="tag-new-name">

    <button
      class="js-tag-insert-ajax-submit"
      type="submit" name="tag_insert" value="tag_insert"
    >Создать новый тег
    </button>

    <button
      class="js-tag-insert-ajax-submit"
      type="submit" name="tag_insert_and_add" value="tag_insert_and_add"
    >Создать новый тег и назначить
    </button>

    <?php if ($unsetTags): ?>

      <hr>

      <label for="tag-choice">Добавить тег</label>
      <select
        id="tag-choice" name="tag_choice"
        class="js-select-tag-choice"
      >
        <option value="" selected></option>
        <?php foreach ($unsetTags as $tag): ?>
          <option value="<?= $tag->id ?>"><?= $tag->name ?></option>
        <?php endforeach; ?>
      </select>

    <?php endif; ?>

  </form>

</div>