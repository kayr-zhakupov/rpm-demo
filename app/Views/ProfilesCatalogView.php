<?php

namespace App\Views;

use App\Profile\ProfilesSliceRequest;

class ProfilesCatalogView
{
  protected ProfilesSliceRequest $request;

  public function __construct(ProfilesSliceRequest $request)
  {
    $this->request = $request;
  }

  public function getTitle(): string
  {
    return 'Все друзья';
  }

  public function getTotalCount(): int
  {
    return $this->request->getTotalCount();
  }

  /**
   * @return array
   */
  public function getItems()
  {
    return $this->request->getItems();
  }

  public function renderHead(): string
  {
    ob_start();

    ?>
    <div class="profiles-catalog-head js-profiles-catalog-head">
      <span><?= $this->getTitle() ?></span> <i><?= $this->request->getTotalCount() ?></i>
    </div>
    <?php

    return ob_get_clean();
  }

  /**
   * @return int
   */
  public function getSliceCount(): int
  {
    return $this->sliceCount;
  }

  /**
   * @param int $sliceCount
   */
  public function setSliceCount(int $sliceCount)
  {
    $this->sliceCount = $sliceCount;
    return $this;
  }

  public function getRequest(): ProfilesSliceRequest
  {
    return $this->request;
  }
}