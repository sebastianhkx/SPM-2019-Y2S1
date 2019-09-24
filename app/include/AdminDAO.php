<?php

class AdminDAO {
       
    function retrieve($userid) {
        $sql = 'SELECT userid, password FROM admin where userid = :userid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Admin($row['userid'], $row['password']);
        }

        $stmt = null;
        $conn = null; 
    }
    
    
}