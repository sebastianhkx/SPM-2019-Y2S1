<?php

class ConnectionManager {
   
    public function getConnection() {
        
        $host = "localhost";
        $username = "root";
    
        // $password = "";                // WAMP
        $port = 3306;                  // WAMP

        $password = "root";         // MAMP   

        // $password = "oAoW79DW2TSy"; // AWS
        // $port = 8888;               // AWS

        $dbname = "g6t6";

        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";
        
        $conn = new PDO($url, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        return $conn;  
        
    }
    
}
