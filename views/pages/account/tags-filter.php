<?php
/**
 * @see views/pages/account/index.php:58
 *
 * @var \App\Models\TagRecord[] $all_tags
 */
?>

<?php if ($all_tags): ?>

  <div class="js-tags-filter">

    <div class="tags-filter">
      <?php
      foreach ($all_tags as $tag):
        $checkboxId = 'tag-filter-' . $tag->id;
        $name = 'tag_filter_' . $tag->id;
        ?>
        <div class="tag-filter-badge">
          <input
            class="tag-filter-checkbox"
            type="checkbox"
            id="<?= $checkboxId ?>"
            name="<?= $name ?>"
            checked
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

  </div>

<?php endif; ?>