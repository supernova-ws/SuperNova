<?php
/** Created by Gorlum 19.05.2023 18:33 */

/* DEPRECATED FUNCTION for back-compatibility with old modules ------------------------------------------------------ */

/** @deprecated */
function db_error() {
  return SN::$db->db_error();
}
/** @deprecated */
function sn_db_connect() {
  return SN::$db->sn_db_connect();
}
/** @deprecated */
function sn_db_disconnect() {
  return SN::$db->db_disconnect();
}

/** @deprecated */
function sn_db_transaction_check($transaction_should_be_started = null) {
  return SN::db_transaction_check($transaction_should_be_started);
}
/** @deprecated */
function sn_db_transaction_start($level = '') {
  return SN::db_transaction_start($level);
}
/** @deprecated */
function sn_db_transaction_commit() {
  return SN::db_transaction_commit();
}
/** @deprecated */
function sn_db_transaction_rollback() {
  return SN::db_transaction_rollback();
}

/** @deprecated */
function db_fetch_row(&$query) {
  return SN::$db->db_fetch_row($query);
}

/** @deprecated */
function db_insert_id() {
  return SN::$db->db_insert_id();
}
/** @deprecated */
function db_num_rows(&$result) {
  return SN::$db->db_num_rows($result);
}
/** @deprecated */
function db_affected_rows() {
  return SN::$db->db_affected_rows();
}

/** @deprecated */
function db_get_client_info() {
  return SN::$db->getClientInfo();
}
function db_get_server_info() {
  return SN::$db->getServerInfo();
}
/** @deprecated */
function db_get_host_info() {
  return SN::$db->getHostInfo();
}
/** @deprecated */
function db_server_stat() {
  return SN::$db->getServerStat();
}
