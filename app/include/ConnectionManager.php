<?php

class ConnectionManager {
   
    public function getConnection() {
        
        $host = "localhost";
        $username = "root";

        ## check if machine is windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $password = "";                
            $port = 3306;                  
        }

        ## check if machine is mac
        elseif (PHP_OS === 'Darwin') {
            $password = "root";       
            $port = 3306;                      
        }

        ## check if machine is AWS
        elseif (PHP_OS === 'Linux') {
            $password = "oAoW79DW2TSy"; 
            $port = 8888;               
        }

        $dbname = "g6t6";

        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";
        
        $conn = new PDO($url, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        return $conn;  
        
    }
    
}
