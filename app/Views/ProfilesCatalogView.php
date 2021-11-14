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

  public function getRequest(): ProfilesSliceRequest
  {
    return $this->request;
  }
}