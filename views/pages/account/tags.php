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

<form action="<?= Routes::i()->ajaxTags() ?>">

  <label for="tag-choice">Добавить тэг</label>
  <select
    id="tag-choice" name="tag_choice"
    class="js-select-tag-choice"
  >
    <?php foreach ($tagOptions as $tagOption): ?>
      <option value="<?= $tagOption['id'] ?>"><?= $tagOption['name'] ?></option>
    <?php endforeach; ?>
  </select>

</form>
