<!DOCTYPE html>
<!--
@author Melika Ghanbari
https://github.com/melikaghanbari
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $connect = mysqli_connect($servername, $username, $password, "events");
        
        $filename = "junior_events.json";
        $data = file_get_contents($filename);
        $array = json_decode($data, true);
        
        $sql ="CREATE TABLE tbl_event ("
                . " event_id INTEGER NOT NULL PRIMARY KEY,"
                . " event_name VARCHAR(50),"
                . " participation_fee FLOAT,"
                . " event_date DATE )";
        mysqli_query($connect, $sql);
          
        $sql ="CREATE TABLE tbl_person ("
                . " person_id INTEGER NOT NULL PRIMARY KEY,"
                . " employee_name VARCHAR(50),"
                . " employee_mail VARCHAR(320) )";
        mysqli_query($connect, $sql);
         
        $sql ="CREATE TABLE tbl_participation ("
                . " participation_id INTEGER NOT NULL PRIMARY KEY,"
                . " event_id INTEGER,"
                . " person_id INTEGER, "
                . " FOREIGN KEY (event_id) REFERENCES tbl_event(event_id),"
                . " FOREIGN KEY (person_id) REFERENCES tbl_person(person_id) )";
        mysqli_query($connect, $sql);
        
        $person_id = 0;
        foreach($array as $row)
        { 
            $id = $row["event_id"];
            $sql = "SELECT event_id FROM tbl_event WHERE event_id=$id";
            $result = mysqli_query($connect, $sql);
            if(mysqli_num_rows($result)==0){
                
                $event_id = $row["event_id"];
                $event_name = $row["event_name"];
                $participation_fee = $row["participation_fee"];
                $event_date = $row["event_date"];
                
                $sql = "INSERT INTO tbl_event VALUES (?, ?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("isds", $event_id, $event_name, $participation_fee, $event_date);
                $stmt->execute();
            }
            
            $employee_mail = $row["employee_mail"];
            $sql = "SELECT DISTINCT person_id FROM tbl_person"
                    . " WHERE employee_mail = '$employee_mail' ";
            $result = mysqli_query($connect, $sql);
            
            if(mysqli_num_rows($result)==0){
                $person_id++;
                $employee_name= $row["employee_name"];
                
                $sql = "INSERT INTO tbl_person VALUES(?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("iss", $person_id, $employee_name, $employee_mail);
                $stmt->execute();
                $final_id = $person_id;
                
            }else{
                $result_row = $result->fetch_array();
                $final_id = $result_row["person_id"];
            }
            
            $participation_id= $row["participation_id"];
            $event_id= $row["event_id"];
            
            $sql = "INSERT INTO tbl_participation VALUES(?, ?, ?)";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("iii", $participation_id, $event_id, $final_id);
            $stmt->execute();
            
        }
        echo "done";
        ?>
    </body>
</html>
