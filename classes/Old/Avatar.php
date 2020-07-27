<?php
/**
 * Created by Gorlum 15.08.2019 6:16
 */

namespace Old;

use Exception;

class Avatar {

  public static function sys_avatar_upload($subject_id, &$avatar_field, $prefix = 'avatar') {
    global $config, $lang, $user;

    try {
      $avatar_filename = $fullsize_filename = SN_ROOT_PHYSICAL . 'images/avatar/' . $prefix . '_' . $subject_id;
      $avatar_filename .= '.png';
      $fullsize_filename .= '_full.png';
      if (sys_get_param_int('avatar_remove')) {
        if (file_exists($avatar_filename) && !unlink($avatar_filename)) {
          throw new Exception($lang['opt_msg_avatar_error_delete'], ERR_ERROR);
        }
        $avatar_field = 0;
        throw new Exception($lang['opt_msg_avatar_removed'], ERR_NONE);
      } elseif ($_FILES['avatar']['size']) {
        if (!in_array($_FILES['avatar']['type'], array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png')) || $_FILES['avatar']['size'] > 204800) {
          throw new Exception($lang['opt_msg_avatar_error_unsupported'], ERR_WARNING);
        }

        if ($_FILES['avatar']['error']) {
          throw new Exception(sprintf($lang['opt_msg_avatar_error_upload'], $_FILES['avatar']['error']), ERR_ERROR);
        }

        if (!($avatar_image = imagecreatefromstring(file_get_contents($_FILES['avatar']['tmp_name'])))) {
          throw new Exception($lang['opt_msg_avatar_error_unsupported'], ERR_WARNING);
        }

        $avatar_size = getimagesize($_FILES['avatar']['tmp_name']);
        $avatar_max_width = $config->avatar_max_width;
        $avatar_max_height = $config->avatar_max_height;
        if ($avatar_size[0] > $avatar_max_width || $avatar_size[1] > $avatar_max_height) {
          $aspect_ratio = min($avatar_max_width / $avatar_size[0], $avatar_max_height / $avatar_size[1]);
          $avatar_image_new = imagecreatetruecolor($avatar_size[0] * $aspect_ratio, $avatar_size[0] * $aspect_ratio);
          $result = imagecopyresized($avatar_image_new, $avatar_image, 0, 0, 0, 0, $avatar_size[0] * $aspect_ratio, $avatar_size[0] * $aspect_ratio, $avatar_size[0], $avatar_size[1]);
          imagedestroy($avatar_image);
          $avatar_image = $avatar_image_new;
        }

        if (file_exists($avatar_filename) && !unlink($avatar_filename)) {
          throw new Exception($lang['opt_msg_avatar_error_delete'], ERR_ERROR);
        }

        if (!imagepng($avatar_image, $avatar_filename, 9)) {
          throw new Exception($lang['opt_msg_avatar_error_writing'], ERR_ERROR);
        }

        $avatar_field = 1;
        imagedestroy($avatar_image);
        throw new Exception($lang['opt_msg_avatar_uploaded'], ERR_NONE);
      }
    } catch (Exception $e) {
      return array(
        'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
        'MESSAGE' => $e->getMessage()
      );
    }
  }

}
