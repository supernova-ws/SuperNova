<?php

function sys_maintenance()
{
  // TODO: Move here some cleaning procedures from admin/maintenance.php
  $queries = array(
    // Cleaning outdated records from bashing table
    array('query' => "DELETE FROM {{bashing}} WHERE bashing_time < {$bashing_time_limit};", 'result' => false, 'error' => '', 'affected_rows' => 0),
    // Cleaning ACS table from empty records
    array('query' => 'DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});', 'result' => false, 'error' => '', 'affected_rows' => 0),
  );

  $time_now = &$GLOBALS['time_now'];
  $bashing_time_limit = $time_now - BASH_INTERVAL;

  foreach($queries as &$query)
  {
    $query['result'] = doquery($query['query']);
    $query['error']  = mysql_error();
    $query['affected_rows']  = mysql_affected_rows();
  }

  return $queries;
}

?>
