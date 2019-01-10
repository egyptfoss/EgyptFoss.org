<?php

class SessionSaveHandler {

  public function __construct() {
    session_set_save_handler(
      array($this, "_open"), array($this, "_close"), array($this, "_read"), array($this, "_write"), array($this, "_destroy"), array($this, "_clean")
    );
  }

  public function _open() {
    global $_sess_db;
    // var_dump("sdad");
    $_sess_db = mysql_connect('127.0.0.1', 'foss', 'F0$$');
    mysql_select_db('foss', $_sess_db);
  }

  public function _close() {
    global $_sess_db;

    mysql_close($_sess_db);
  }

  public function _read($id) {
    global $_sess_db, $table_prefix;
   
    $id = mysql_real_escape_string($id);

    $sql = "SELECT data
            FROM   {$table_prefix}sessions
            WHERE  id = '$id'";

    if ($result = mysql_query($sql, $_sess_db)) {
      if (mysql_num_rows($result)) {
        $record = mysql_fetch_assoc($result);

        return $record['data'];
      }
    }

    return '';
  }

  public function _write($id, $data) {
    global $_sess_db, $table_prefix;
    $access = time();

    $id = mysql_real_escape_string($id);
    $access = mysql_real_escape_string($access);
    $data = mysql_real_escape_string($data);

    $sql = "REPLACE
            INTO    {$table_prefix}sessions
            VALUES  ('$id', '$access', '$data')";

    return mysql_query($sql, $_sess_db);
  }

  public function _destroy($id) {
    global $_sess_db, $table_prefix;

    $id = mysql_real_escape_string($id);

    $sql = "DELETE
            FROM   {$table_prefix}sessions
            WHERE  id = '$id'";

    return mysql_query($sql, $_sess_db);
  }

  public function _clean($max) {
    global $_sess_db, $table_prefix;

    $old = time() - $max;
    $old = mysql_real_escape_string($old);

    $sql = "DELETE
            FROM   {$table_prefix}sessions
            WHERE  access < '$old'";

    return mysql_query($sql, $_sess_db);
  }

}
