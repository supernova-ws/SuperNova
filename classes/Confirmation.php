<?php

use DBAL\db_mysql;

/**
 * User: Gorlum
 * Date: 17.09.2015
 * Time: 14:11
 */
class Confirmation {

  /**
   * @var db_mysql
   */
  protected $db = null;

  public function __construct($db) {
    $this->db = $db;
  }

  // TODO - НЕ ОБЯЗАТЕЛЬНО ОТПРАВЛЯТЬ ЧЕРЕЗ ЕМЕЙЛ! ЕСЛИ ЭТО ФЕЙСБУЧЕК ИЛИ ВКШЕЧКА - МОЖНО ЧЕРЕЗ ЛС ПИСАТЬ!!
  // TODO - OK 4.6
  public function db_confirmation_get_latest_by_type_and_email($confirmation_type_safe, $email_unsafe) {
    $email_safe = $this->db->db_escape($email_unsafe);

    return $this->db->doQueryAndFetch(
      "SELECT * 
      FROM {{confirmations}} 
      WHERE
        `type` = {$confirmation_type_safe} 
        AND `email` = '{$email_safe}' 
      ORDER BY create_time DESC 
      LIMIT 1;"
    );
  }
  // TODO - OK 4.6
  public function db_confirmation_delete_by_type_and_email($confirmation_type_safe, $email_unsafe) {
    $email_safe = $this->db->db_escape($email_unsafe);

    return $this->db->doquery(
      "DELETE FROM {{confirmations}} WHERE `type` = {$confirmation_type_safe} AND `email` = '{$email_safe}'"
    );
  }
  // TODO - OK 4.6
  public function db_confirmation_get_unique_code_by_type_and_email($confirmation_type_safe, $email_unsafe) {
    $email_safe = $this->db->db_escape($email_unsafe);

    do {
      // Ну, если у нас > 999.999 подтверждений - тут нас ждут проблемы...
      $confirm_code_safe = $this->db->db_escape($confirm_code_unsafe = $this->make_password_reset_code());
      // $query = static::$db->doquery("SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' AND `type` = {$confirmation_type_safe} FOR UPDATE", true);
      // Тип не нужен для проверки - код подтверждения должен быть уникален от слова "совсем"
      $query = $this->db->doQueryAndFetch(
        "SELECT `id` FROM {{confirmations}} WHERE `code` = '{$confirm_code_safe}' FOR UPDATE"
      );
    } while($query);

    $this->db->doquery(
      "REPLACE INTO {{confirmations}}
        SET `type` = {$confirmation_type_safe}, `code` = '{$confirm_code_safe}', `email` = '{$email_safe}';"
    );

    return $confirm_code_unsafe;
  }
  // TODO - OK 4.6
  public function db_confirmation_get_by_type_and_code($confirmation_type_safe, $confirmation_code_unsafe) {
    $confirmation_code_safe = $this->db->db_escape($confirmation_code_unsafe);

    return $this->db->doQueryAndFetch(
      "SELECT * 
      FROM {{confirmations}} 
      WHERE
        `type` = {$confirmation_type_safe} 
        AND `code` = '{$confirmation_code_safe}' 
      ORDER BY create_time DESC 
      LIMIT 1 FOR UPDATE"
    );
  }

  protected function make_password_reset_code() {
    return sys_random_string(LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH, SN_SYS_SEC_CHARS_CONFIRMATION);
  }

}
