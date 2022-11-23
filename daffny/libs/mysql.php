<?php


class mysql
{
    public $host = "localhost";
    public $user = "root";
    public $pass = "";
    public $base = "mysql";
    public $pref = "";
    public $charset = "utf8";
    public $connection_id = null;
    /**
     * @var mysqli_result
     */
    public $query_id = "";
    public $record_row = array();
    public $server_info = "";
    public $server_vers = 3;
    public $queries = array();

    public $isDebug = false;
    public $isError = false;
    public $errorStr = array();
    public $errorQuery = "no errors";
    public $isTransaction = false;

    /*--------------------------------------*/
    // Connect to the database
    /*--------------------------------------*/
    public function connect($host, $user, $pass, $base, $pref = "")
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->base = $base;
        $this->pref = $pref;

        $this->connection_id = @mysqli_connect($this->host, $this->user, $this->pass);

        if (!$this->connection_id) {
            $this->error("<b>MySQL error:</b> Can't connect to database<br><b>Debug:</b> " . mysqli_error($this->connection_id));
            return false;
        }

        if (!mysqli_select_db($this->connection_id, $this->base)) {
            $this->error("<b>MySQL error:</b> Can't select database<br><b>Debug:</b> " . mysqli_error($this->connection_id));
            return false;
        }

        $this->server_info = mysqli_get_server_info($this->connection_id);
        $tmp = explode("-", $this->server_info);
        $this->server_vers = $tmp[0];

        if ($this->server_vers > 4) {
            $this->query("set character_set_client='" . $this->charset . "'");
            $this->query("set character_set_results='" . $this->charset . "'");
            $this->query("set collation_connection='" . $this->charset . "_general_ci'");
        }

        return true;
    }
    
    public function hardQuery($the_query)
    {

        $response = mysqli_query($this->connection_id, $the_query);

        return $response;
    }

    /*--------------------------------------*/
    // Process a manual query
    /*--------------------------------------*/
    public function query($the_query)
    {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $starttime = $mtime[1] + $mtime[0];

        $this->query_id = mysqli_query($this->connection_id, $the_query);

        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $endtime = $mtime[1] + $mtime[0];

        $this->queries[] = array('query' => $the_query, 'time' => round(($endtime - $starttime), 2));

        if (!$this->query_id) {
            $this->isError = true;
            $this->errorStr = mysqli_error($this->connection_id);
            $this->errorQuery = $the_query;

            $this->error("<b>Query:</b> $the_query<br><b>Debug:</b> " . $this->errorStr);

            // write log file
            if (defined('ROOT_PATH') && defined('FILES_DIR')) {
                $logFile = ROOT_PATH . FILES_DIR . "/mysql-error.sql";
                if ($handle = fopen($logFile, 'a+')) {
                    $error = "--" . date("r") . "\r\n--Error:" . $this->errorStr . "\r\n$the_query\r\n\r\n";
                    fwrite($handle, $error);
                    fclose($handle);
                }
                if ($this->errorStr != "") {
                    $str = " <br>Query : " . $this->errorQuery . " <br><br>Error : " . $this->errorStr;
                    $_SESSION['queryError'] = $str;
                    $querylog = "insert into app_log(description,user_id,type)values('" . addslashes($str) . "','" . $_SESSION['member']['id'] . "',2)";
                    $this->queryLog($querylog);
                }
            }
        }
        return $this->query_id;
    }

    /*--------------------------------------*/
    // Fetch a row based on the last query
    /*--------------------------------------*/
    /**
     * @param mysqli_result $query_id
     * @param int $type
     * @return array|bool|null
     */
    public function fetch_row($query_id = null, $type = MYSQLI_ASSOC)
    {
        if ($query_id == "") {
            $query_id = $this->query_id;
        }

        if (!$query_id) {
            return false;
        }

        $this->record_row = mysqli_fetch_array($query_id, $type);

        return $this->record_row;
    }

    /*--------------------------------------*/
    // UPDATE
    /*--------------------------------------*/
    public function update($tbl, $arr, $where = "")
    {
        $values = "";

        foreach ($arr as $k => $v) {
            if ($v == "NULL"
                or is_null($v)
                or $v == "now()"
                or preg_match('/ENCRYPT\(/', $v)) {
                if (is_null($v)) {
                    $v = "NULL";
                }

                $values .= $k . " = " . $v . ", ";
            } else {
                $values .= $k . " = '" . mysqli_real_escape_string($this->connection_id, $v) . "', ";
            }
        }

        $values = substr($values, 0, -2);

        $query = "UPDATE " . $this->pref . "$tbl SET $values";

        if ($where != "") {
            $query .= " WHERE " . $where;
        }

        return $this->query($query);
    }

    /*--------------------------------------*/
    // INSERT
    /*--------------------------------------*/
    public function insert($tbl, $arr)
    {
        $fields = $values = "";

        foreach ($arr as $k => $v) {
            if ($tbl == 'chat') {
                $fields .= "`" . $k . "`, ";
            } else {
                $fields .= $k . ", ";
            }
            if ($v == "NULL"
                or is_null($v)
                or $v == "now()"
                or preg_match('/ENCRYPT\(/', $v)) {
                if (is_null($v)) {
                    $v = "NULL";
                }
                $values .= $v . ", ";
            } else {
                $values .= "'" . mysqli_real_escape_string($this->connection_id, $v) . "', ";
            }
        }

        $fields = substr($fields, 0, -2);
        $values = substr($values, 0, -2);

        $query = "INSERT INTO " . $this->pref . "$tbl($fields)\nVALUES($values)";

        return $this->query($query);
    }

    public function insertOrUpdate($tbl, $data)
    {
        $fields = $values = "";

        foreach ($data as $k => $v) {
            $fields .= $k . ", ";

            if ($v == "NULL"
                or is_null($v)
                or $v == "now()"
                or preg_match('/ENCRYPT\(/', $v)) {
                if (is_null($v)) {
                    $v = "NULL";
                }
                $values .= $v . ", ";
            } else {
                $values .= "'" . mysqli_real_escape_string($this->connection_id, $v) . "', ";
            }
        }

        $fields = substr($fields, 0, -2);
        $values = substr($values, 0, -2);

        $query = "INSERT INTO " . $this->pref . "$tbl($fields)\nVALUES($values) on duplicate key update " . $this->_getFieldForDupUpdate($data);

        return $this->query($query);
    }

    protected function _getFieldForDupUpdate($fields)
    {
        $data = array();
        foreach ($fields as $field => $val) {
            $data[] = ($val === 'NULL') ? sprintf("%s = NULL", $field) : sprintf("%s = '%s'", $field, mysqli_real_escape_string($this->connection_id, $val));
        }

        return implode(",\n", $data);
    }

    /*--------------------------------------*/
    // DELETE
    /*--------------------------------------*/
    public function delete($table, $where = "")
    {

        $query = "DELETE FROM " . $this->pref . $table;

        if ($where != "") {
            $query .= " WHERE " . $where;
        }

        return $this->query($query);
    }

    /*--------------------------------------*/
    // SELECT
    /*--------------------------------------*/
    public function select($fields, $table, $add = "")
    {
        return $this->query("SELECT " . $fields . " FROM " . $this->pref . $table . " " . $add);
    }

    /*--------------------------------------*/
    // SELECT one row
    /*--------------------------------------*/
    public function select_one($fields, $table, $add = "")
    {
        $this->select($fields, $table, $add);

        return $this->fetch_row();
    }

    /*--------------------------------------*/
    // SELECT one row
    /*--------------------------------------*/
    public function selectRow($fields_or_sql, $table = "", $add = "")
    {
        if ($table == "") {
            $this->query($fields_or_sql);

            return $this->fetch_row();
        }

        return $this->select_one($fields_or_sql, $table, $add);
    }

    /*--------------------------------------*/
    // SELECT many rows
    /*--------------------------------------*/
    public function selectRows($fields_or_sql, $table = "", $add = "", $keyField = "")
    {
        $rows = array();

        if ($table == "") {
            $query = $this->query($fields_or_sql);
        } else {
            $query = $this->select($fields_or_sql, $table, $add);
        }

        if ($keyField != "") {
            while ($row = $this->fetch_row($query)) {
                $rows[$row[$keyField]] = $row;
            }
        } else {
            while ($row = $this->fetch_row($query)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /*--------------------------------------*/
    // SELECT one field
    /*--------------------------------------*/
    public function selectField($field, $table, $add = "")
    {
        $row = $this->select_one($field, $table, $add);

        if (!empty($row)) {
            return $row[$field];
        }

        return false;
    }
    
    public function selectValue($field, $table, $add = "")
    {
        $query_id = $this->select($field, $table, $add);
        $row = $this->fetch_row($query_id, MYSQLI_NUM);

        if (!empty($row) && isset($row[0])) {
            return $row[0];
        }

        return false;
    }

    /*--------------------------------------*/
    // Fetch the number of rows affected by the last query
    /*--------------------------------------*/
    public function get_affected_rows()
    {
        return mysqli_affected_rows($this->connection_id);
    }

    /*--------------------------------------*/
    // Fetch the number of rows in a result set
    /*--------------------------------------*/
    public function num_rows()
    {
        return mysqli_num_rows($this->query_id);
    }

    /*--------------------------------------*/
    // Fetch the last insert id from an sql autoincrement
    /*--------------------------------------*/
    public function get_insert_id()
    {
        return mysqli_insert_id($this->connection_id);
    }

    /*--------------------------------------*/
    // Free the result set from MySQLs memory
    /*--------------------------------------*/
    /**
     * @param mysqli_result $query_id
     */
    public function free_result($query_id = null)
    {
        if ($query_id == "") {
            $query_id = $this->query_id;
        }

        @mysqli_free_result($query_id);
    }

    public function getTableFields($table)
    {
        $fields = array();

        if (!$table) {
            return $fields;
        }

        $this->query("SHOW COLUMNS FROM " . $this->pref . $table);
        while ($row = $this->fetch_row()) {
            $field = $row['Field'];
            unset($row['Field'], $row['Key'], $row['Extra']);

            $row['Type'] = preg_replace('/\(.*\)/', "", $row['Type']);

            $fields[$field] = $row;
        }

        return $fields;
    }

    /*--------------------------------------*/
    // MySQL Error...
    /*--------------------------------------*/
    public function error($err)
    {
        if ($this->isDebug) {
            die("<strong style='font-size: 20px;'>Mysql Error</strong><br />$err");
        }
    }

    /*--------------------------------------*/
    // Shut down the database
    /*--------------------------------------*/
    public function disconnect()
    {
        if ($this->connection_id) {
            return @mysqli_close($this->connection_id);
        }
        return false;
    }

    /**
     * put your comment there...
     *
     * @param $TableName
     * @param $FiledsValues
     * @internal param mixed $tableName
     * @internal param string $sql_arr
     * @return mixed
     */
    public function PrepareSql($TableName, $FiledsValues)
    {
        $NewFiledsValues = array();

        $this->query("SHOW COLUMNS FROM " . $this->pref . $TableName);
        while ($row = $this->fetch_row()) {
            if (!isset($FiledsValues[$row['Field']])) {
                continue;
            }

            $FieldName = $row['Field'];
            $FieldType = preg_replace('/\(.*?\)/', "", $row['Type']);
            $FieldNull = $row['Null'] == "YES" ? true : false;
            $FieldValue = $FiledsValues[$FieldName];

            switch ($FieldType) {
                case 'char':
                case 'varchar':
                    {
                        preg_match('/^[a-z]+\(([0-9]+)\)$/', $row['Type'], $res);
                        $FieldLength = $res[1];

                        if (strlen($FieldValue) > $FieldLength && $FieldValue != 'NULL') {
                            $FieldValue = substr($FieldValue, 0, $FieldLength);
                        }

                        $NewFiledsValues["`" . $FieldName . "`"] = $FieldValue;
                        break;
                    }

                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                    {
                        /**
                         * @link http://dev.mysql.com/doc/refman/5.0/en/numeric-types.html
                         */
                        $lengths = array(
                            'tinyint' => array(-128, 127)
                            , 'smallint' => array(-32768, 32767)
                            , 'mediumint' => array(-8388608, 8388607)
                            , 'int' => array(-2147483648, 2147483647)
                            , 'bigint' => array(-9223372036854775808, 9223372036854775807),
                        );

                        if ($FieldValue >= $lengths[$FieldType][0] && $FieldValue <= $lengths[$FieldType][1]) {
                            $NewFiledsValues["`" . $FieldName . "`"] = (int) $FieldValue;
                        }

                        break;
                    }

                case 'date':
                case 'datetime':
                    {
                        if ($FieldValue != "") {
                            $NewFiledsValues["`" . $FieldName . "`"] = $FieldValue;
                        } else if ($FieldNull) {
                            $NewFiledsValues["`" . $FieldName . "`"] = "NULL";
                        }

                        break;
                    }

                case 'decimal':
                    {
                        preg_match('/^decimal\(([0-9]+),([0-9]+)\)$/', $row['Type'], $res);
                        $FieldLength = $res[1];
                        $Decimals = $res[2];

                        $FieldValue = (float) str_replace(",", "", $FieldValue);
                        $FieldValue = number_format($FieldValue, $Decimals, ".", "");

                        if (strlen($FieldValue) <= $FieldLength) {
                            $NewFiledsValues["`" . $FieldName . "`"] = $FieldValue;
                        }

                        break;
                    }

                case 'enum':
                    {
                        $allowedValues = explode("','", preg_replace('/(enum|set)\(\'(.+?)\'\)/', "\\2", $row['Type']));

                        if (in_array($FiledsValues[$FieldName], $allowedValues)) {
                            $NewFiledsValues["`" . $FieldName . "`"] = $FiledsValues[$FieldName];
                        } else if ($FieldNull) {
                            $NewFiledsValues["`" . $FieldName . "`"] = "NULL";
                        }

                        break;
                    }

                case 'text':
                    {
                        $NewFiledsValues["`" . $FieldName . "`"] = $FieldValue;

                        break;
                    }

                default:
                    {
                        $NewFiledsValues["`" . $FieldName . "`"] = $FieldValue;

                        break;
                    }
            }

        }
        return $NewFiledsValues;
    }

    public function transaction($mode = "start")
    {
        $mode = strtolower($mode);
        switch ($mode) {
            case 'commit':
            case 'comit':
                $this->isTransaction = false;
                $this->query("COMMIT");
                break;

            case 'rollback':
            case 'rolback':
            case 'back':
                $this->isTransaction = false;
                $this->query("ROLLBACK");
                break;

            default:
                $this->isTransaction = true;
                $this->query("START TRANSACTION");
                break;
        }
    }

    public function queryLog($the_query)
    {
        mysqli_query($this->connection_id, $the_query);
    }

    public function selectQueueRows($field, $table, $where)
    {
        if ($table == "") {
            $query = " select * from " . $table . " " . $where;
        } else {
            $query = " select * from " . $table . " " . $where;
        }

        $rows = array();
        $result = $this->daffny->DB->query($query);
        while ($row = $this->daffny->DB->fetch_row($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

}
