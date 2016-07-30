<?php

function getConnection() {
    //database login credentials
	$host = "localhost";
    $dbname = "tic_tac_toe";
    $username = "root";
    $password = "";
    try{
	    $dbConn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); 
	    // Check for ERROR 
	    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Return Connection 
   		//echo "Connected successfully";
   		return $dbConn; 
    }
    catch(PDOException $e)
    {
    	echo "Connection failed: " . $e->getMessage();
    }
}

//used to UPDATE, INSERT, and SELECT statements for database
function sendQuery($sql, $namedParameters){
        $dbConn = getConnection();
        $stmt = $dbConn->prepare($sql); 
        if($namedParameters != NULL){
            $stmt->execute($namedParameters);
        }
        else{
            $stmt->execute();
        }

        if(strtoupper(strtok($sql, " ")) == "SELECT"){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
}
?>