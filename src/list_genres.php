<?php
    echo "hello";
    //$mm = new mysqli("127.0.0.1", "db_user", "db_user_pass");
    $mysqli = new mysqli("db", "root", "password", "database");
    
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
      
    $sql_genres = "SELECT genres FROM database.genres";
    $result = $mysqli->query($sql_genres);
    echo "<option value=\"None\">None</option>";

    if ($result->num_rows > 0) {
        
        while($row = $result->fetch_assoc()) {
          echo $row["genres"] . "\n";
          echo "<option value=".$row["genres"].">".$row["genres"]."</option>";
        }
    } else {
        echo "0 results";
    }
     
    /*
    function list_genres(){
        global $mysqli;
      
        $sql_genres = "SELECT genres FROM database.genres";
        $result = $mysqli->query($sql_genres);
        echo "<option value=\"\"></option>";
    
        if ($result->num_rows > 0) {
            
            while($row = $result->fetch_assoc()) {
              echo $row["genres"] . "\n";
              echo "<option value=".$row["genres"].">".$row["genres"]."</option>";
            }
          } else {
            echo "0 results";
          }
    }

    function list_all_ratings(){
      global $mysqli;
      
      $sql_genres = "SELECT DISTINCT rating FROM database.ratings ORDER BY rating";
      $result = $mysqli->query($sql_genres);

      echo "<option value=\"\"></option>";
  
      if ($result->num_rows > 0) {
          
          while($row = $result->fetch_assoc()) {
            echo $row["rating"] . "\n";
            echo "<option value=".$row["rating"].">".$row["rating"]."</option>";
          }
        } else {
          echo "0 results";
        }
    }*/
?>