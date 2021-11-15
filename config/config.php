<?php

return [
  /**
   * Количество профилей, подгружаемых изначально.
   */
  'friends_slice_count_initial' => 10,

  /**
   * Количество профилей, подгружаемых далее.
   */
  'friends_slice_count_next' => 10,

  /**
   * Для тестирования lazy-load при малом количестве результатов.
   * При значении unset высота составляет 116px.
   */
//  'friend_tile_height' => 'unset',
//  'friend_tile_height' => '500px',

  /**
   * Threshold в px для подгрузки.
   */
  'load_more_offset' => 128,
];