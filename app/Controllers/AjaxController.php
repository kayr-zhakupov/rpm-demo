<?php

namespace App\Controllers;

use App\Middleware\Auth;
use App\Repo\Profiles;
use App\Repo\Tags;

class AjaxController
{
  public function friends()
  {
    $count = $_GET['count'] ?? null;
    $offset = $_GET['offset'] ?? 0;

    $slice = Profiles::i()->fetchFriendsListSlice($count, $offset);
    $sliceItems = $slice['items'];

    $html = implode('', array_map(function (array $profileData) {
      return view_html('pages/account/friend-tile', [
        'profile' => $profileData,
      ]);
    }, $sliceItems));

    return [
      'html' => $html,
      'count_real' => ($realCount = count($sliceItems)),
      'is_last_slice' => ($realCount < $count),
    ];
  }

  public function tags()
  {
    $result = [];
    $error = null;
    $successMessage = null;

    try {
      $ownerId = Auth::i()->getCurrentUserId();
      if (empty($ownerId)) throw new \Exception('Not authorized');

      $action = $_POST['action'] ?? null;
      $targetId = $_POST['target_id'] ?? null;

      switch ($action) {
        case 'insert_tag_to_user':
          $tagId = $_POST['id'] ?? null;
          Tags::i()->insertTagToUser([
            'tag_id' => $tagId,
            'target_id' => $targetId,
          ]);
          $successMessage = "Тэг добавлен";
          break;

        case 'insert_tag':
          $name = trim($_POST['name'] ?? '');
          if (empty($name)) throw new \Exception('Name is empty');

          Tags::i()->insert([
            'owner_id' => $ownerId,
            'name' => $name,
          ]);
          $successMessage = sprintf("Тэг `%s` создан", $name);
          break;
      }

    } catch (\Throwable $e) {
      $error = $e->getMessage();
    }

    if ($error) {
      $result['error'] = $error;
      $result['toasts'] = [
        view_html('components/toast', [
          'type' => 'error',
          'text' => $error,
        ]),
      ];
    }

    if ($successMessage) {
      $result['toasts'] = [
        view_html('components/toast', [
          'type' => 'success',
          'text' => $successMessage,
        ]),
      ];
    }

    return $result;
  }
}