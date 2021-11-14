<?php
/**
 *
 */

use App\Repo\Routes;

$tagOptions = [
  ['id' => 1, 'name' => 'first_tag'],
  ['id' => 2, 'name' => 'second_tag'],
  ['id' => 3, 'name' => 'third_tag'],
];

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
