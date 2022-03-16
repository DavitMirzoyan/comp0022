<?php
    $mysqli = new mysqli("db", "root", "password", "database");
        
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

      
    $sql_genres = "SELECT DISTINCT year FROM database.movies ORDER BY year";
    $result = $mysqli->query($sql_genres);

    echo "<option value=\"None\">None</option>";
  
    if ($result->num_rows > 0) {
          
      while($row = $result->fetch_assoc()) {
        echo $row["year"] . "\n";
        echo "<option value=".$row["year"].">".$row["year"]."</option>";
      }
    } else {
      echo "0 results";
    }
?>