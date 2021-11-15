<?php

namespace App\Controllers;

use App\Middleware\Auth;
use App\Profile\ProfilesSliceRequest;
use App\Repo\Tags;
use App\Views\ProfilesCatalogView;

class AjaxController
{
  public function profiles()
  {
    $profilesSliceRequest = new ProfilesSliceRequest([
      'count' => $_GET['count'] ?? null,
      'offset' => $_GET['offset'] ?? 0,
      'tags' => $_GET['tags'],
      'friends_of_id' => Auth::i()->getCurrentUserId(),
      'friends_with' => $_GET['friends_with'] ?? null,
    ]);
    $catalogView = new ProfilesCatalogView($profilesSliceRequest);
    $doIncludeSliceData = array_key_exists('dbg', $_GET);

    $html_head = $catalogView->renderHead();

    $html_slice = implode('', array_map(function ($profileData) {
      return view_html('pages/account/friend-tile', [
        'profile' => $profileData,
      ]);
    }, $catalogView->getItems()));

    return [
        'html_slice' => $html_slice,
        'html_head' => $html_head,
        'offset' => $profilesSliceRequest->getOffset(),
        'slice_length' => $profilesSliceRequest->getItemsLength(),
        'tags_str' => $profilesSliceRequest->tagsToString(),
        'is_last_slice' => ($profilesSliceRequest->getItemsLength() < $profilesSliceRequest->getRequestedCount()),
      ] + (
      $doIncludeSliceData ? [
        'slice' => [
          'total_count' => $profilesSliceRequest->getTotalCount(),
          'items' => $profilesSliceRequest->getItems(),
        ],
      ] : []
      );
  }

  public function tags()
  {
    $result = [];
    $error = null;
    $successMessage = null;
    $targetId = $_POST['target_id'] ?? null;
    $tagId = $_POST['id'] ?? null;

    try {
      $ownerId = Auth::i()->getCurrentUserId();
      if (empty($ownerId)) throw new \Exception('Not authorized');

      $action = $_POST['action'] ?? null;

      switch ($action) {
        case 'insert_tag_to_user':
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
          ], $newTagId);

          if ($_POST['do_insert_tag_to_user'] ?? false) {
            Tags::i()->insertTagToUser([
              'tag_id' => $newTagId,
              'target_id' => $targetId,
            ]);
          };

          $successMessage = sprintf("Тэг `%s` создан", $name);
          break;

        case 'delete_tag_to_user':
          Tags::i()->deleteTagToUser($tagId, $targetId);
          $successMessage = "Тэг удалён";
          break;
      }

    } catch (\Throwable $e) {
      error_log($e);
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
      return $result;
    }

    if ($successMessage) {
      $result['toasts'] = [
        view_html('components/toast', [
          'type' => 'success',
          'text' => $successMessage,
        ]),
      ];
    }

    $result['html_widget'] = view_html('pages/account/tags-widget', [
      'all_tags' => Tags::i()->getAllMyTags(),
      'profile_tags' => Tags::i()->tagsForProfile($targetId),
    ]);

    return $result;
  }
}