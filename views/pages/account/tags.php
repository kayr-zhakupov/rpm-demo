<?php
/**
 * @see views/pages/account/index.php:57
 *
 * @var array $all_tags
 */

use App\Repo\Routes;

?>

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
  >Создать новый тег</button>

  <button
    class="js-tags-ajax-submit"
    type="submit" name="tag_insert_and_add" value="tag_insert_and_add"
  >Создать новый тег и назначить</button>

  <hr>

  <label for="tag-choice">Добавить тег</label>
  <select
    id="tag-choice" name="tag_choice"
    class="js-select-tag-choice"
  >
    <?php foreach ($tagOptions as $tagOption): ?>
      <option value="<?= $tagOption['id'] ?>"><?= $tagOption['name'] ?></option>
    <?php endforeach; ?>
  </select>

</form>
