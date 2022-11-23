<?php

class Log {
    
    const TABLE = "cp_log";
    
    // function to log request in log file
    function log_request() {
        $sql = "SELECT accessKey FROM api_access_key WHERE status = 1";
        //$ci=& get_instance();
        //$query = $ci->db->query($sql);
        //$row = $query->result();
    }
}

?>