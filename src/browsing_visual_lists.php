<?php
    $mysqli = new mysqli("db", "root", "password", "database");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $genres = ($_POST["genres"]);
        $rating = ($_POST["rating"]);
        $movie_name = ($_POST["movie_name"]);
        $start_year = ($_POST["start_year"]);
        $end_year = ($_POST["end_year"]);

        $year_sort = ($_POST["year_sort"]);
        $rating_sort = ($_POST["rating_sort"]);

        //$year = -1;


        $query_movie_name = "SELECT movieId, title, year FROM database.movies WHERE year >= $start_year AND year <= $end_year";
        $result =$mysqli->query($query_movie_name);


        echo "<h2>Each tag vote</h2>
                <table border='1'>
                <tr>
                <th>movieId</th>
                <th>title</th>
                <th>year</th>
            </tr>";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $id = $row['movieId'];
                $name = $row["title"];
                $year = $row['year'];
                echo "<tr>", "<td> $id </td>", "<td> <a href=\"movie_info.php?id=$id\"> $name </a> </td>", "<td> $year </td>", "</tr>";
            }
        } else {
            echo "0";
        }
                
                
        echo "</table>
                <br><br>";

    }
    
?>