<?php
/**
 * @see views/pages/account/index.php:58
 *
 * @var \App\Models\TagRecord[] $all_tags
 */
?>

<?php if ($all_tags): ?>

  <hr>

  <div class="js-tags-filter">

    <div>Фильтр по тегам</div>
    <br>

    <div class="tags-filter">
      <?php
      foreach ($all_tags as $tag):
        $checkboxId = 'tag-filter-' . $tag->id;
        $name = 'tag_filter_' . $tag->id;
        ?>
        <div class="tag-filter-badge">
          <input
            class="tag-filter-checkbox js-tag-filter-checkbox"
            type="checkbox"
            id="<?= $checkboxId ?>"
            name="<?= $name ?>"
            data-id="<?= $tag->id ?>"
          >
          <label
            for="<?= $checkboxId ?>"
            class="tag-filter-label"
          >
            <?= $tag->name ?>
          </label>
        </div>
      <?php endforeach; ?>
    </div>

    <br>
    <div>
      <button
        class="js-tag-filter-select-none" type="button"
      >Сбросить все
      </button>
      <button
        class="js-tag-filter-select-all" type="button"
      >Выбрать все
      </button>
    </div>

    <br>
    <button
      class="js-tag-filter-submit"
      type="submit"
      name="tag_filter_submit"
    >Применить фильтр
    </button>

  </div>

<?php endif; ?>