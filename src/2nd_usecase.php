<?php
    $mysqli = new mysqli("db", "root", "password", "database");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $movieId = "$movieId";

    $query_movie_name = "SELECT title FROM database.movies WHERE movieId = $movieId";
    $result =$mysqli->query($query_movie_name);

    if ($result->num_rows > 0) {
        
        while($row = $result->fetch_assoc()) {
            $movieName = $row["title"];
        }
    } else {
        echo "0 results";
    }

    $query_imdb_tmdb = "SELECT imdbId, tmdbId FROM database.links WHERE movieId = $movieId";
    $result =$mysqli->query($query_imdb_tmdb);
    
    /*
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = mysqli_fetch_assoc($stmt->get_result());*/
    $tmdb = null;
    $imdb = null;

    if ($result->num_rows > 0) {
        
        while($row = $result->fetch_assoc()) {
            if ($tmdb !== null){
                break;
            }
            $tmdb = $row["tmdbId"];
            $imdb = $row["imdbId"];
        }
    } else {
        echo "0 results";
    }

    $query_rating = "SELECT ratings FROM database.movies WHERE movieId = $movieId";
    $result =$mysqli->query($query_rating);
    
    $average_rating = null;

    if ($result->num_rows > 0) {
        
        while($row = $result->fetch_assoc()) {
            if ($average_rating !== null){
                break;
            }
            $average_rating = $row["ratings"];
        }
    } else {
        echo "0 results";
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
             CURLOPT_URL => "https://imdb-api.com/en/API/Title/k_23zo2q7v/tt$imdb",
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_ENCODING => "",
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_TIMEOUT => 10,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "GET",            
    ]);

    $imdbResponse = curl_exec($ch);
    curl_close($ch);

    //echo($imdbResponse), "\n";

    $data = json_decode($imdbResponse, true);
    $imdbRating = $data['imDbRating'];

    $ch = curl_init();
    curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.themoviedb.org/3/movie/$tmdb?api_key=acd7122df7779323db781c38430de0ac&language=en-US",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
                
    ]);

    $tmdbResponse = curl_exec($ch);
    //echo($tmdbResponse), "\n";
    //$tmdbErr = curl_error($ch);
    curl_close($ch);
    
    $data = json_decode($tmdbResponse, true);

    //echo($data), "\n";
                
    $tmdbRating = $data['vote_average'];
    $poster_path = $data['poster_path'];
    $overview = $data['overview'];

    echo "<div class=\"container\">
            <div class=\"row\">
                    <center>
                    <h2>$movieName </h2>
                             <img src = https://image.tmdb.org/t/p/original$poster_path align = \"center\"width = \"240\" height = \"330\"  >
                    </center>
                    <br>
            </div>
            <div class=\"row\">
                    
                    $overview
                    
            <h2>tmDB Rating: $tmdbRating/10 </h2>
            <h2>imDB Rating: $imdbRating/10 </h2>
            <h2>Average Rating: $average_rating/5 </h2>
            
            ";

    echo "<h2>Each rating vote</h2>
        <table border='1'>
            <tr>
            <th>rating</th>
            <th>Number of people voted</th>
            <th>Percentage</th>
        </tr>
        ";
    
    $query_each_rating= "SELECT rating, Count(rating) as Total FROM database.ratings WHERE movieId = $movieId GROUP BY rating ORDER BY rating DESC ";
    $result = $mysqli->query($query_each_rating);

    $query_column_length= "SELECT Sum(Total) as Total_Sum FROM (SELECT Count(rating) as Total FROM database.ratings WHERE movieId = $movieId GROUP BY rating) as Total_Sum";
    $length_result = $mysqli->query($query_column_length);
    $row = $length_result->fetch_assoc();
    $length = $row["Total_Sum"];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $perecntage = round($row["Total"]/$length * 100, 2);
            echo "<tr>", "<td align='center'>" . $row['rating'] . "</td>", "<td align='center'>" .$row["Total"]. "</td>", "<td align='center'>" .$perecntage. "%</td>","</tr>";
        }
    } else {
        echo "<tr>", "<td align='center'> " . "no ratings" . "</td>", "<td align='center'>" ."0". "</td>", "<td align='center'>" ."0". "%</td>","</tr>";
    }
            
    echo "</table>
            <br><br>";

    echo "<h2>Each tag vote</h2>
            <table border='1'>
            <tr>
            <th>tag</th>
            <th>Number of people voted</th>
            <th>Percentage</th>
        </tr>";

    $query_tags= "SELECT tag, Count(tag) as Total FROM database.tags WHERE movieId = $movieId GROUP BY tag";
    $result =$mysqli->query($query_tags);

    $query_column_length= "SELECT Sum(Total) as Total_Sum FROM (SELECT Count(tag) as Total FROM database.tags WHERE movieId = $movieId GROUP BY tag) as Total_Sum";
    $length_result = $mysqli->query($query_column_length);
    $row = $length_result->fetch_assoc();
    $length = $row["Total_Sum"];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $perecntage = round($row["Total"]/$length * 100, 2);
            echo "<tr>", "<td align='center'> " . $row['tag'] . "</td>", "<td align='center'>" .$row["Total"]. "</td>", "<td align='center'>" .$perecntage. "%</td>","</tr>";
        }
    } else {
        echo "<tr>", "<td align='center'> " . "no tags" . "</td>", "<td align='center'>" ."0". "</td>", "<td align='center'>" ."0". "%</td>","</tr>";
    }
            
            
    echo "</table>
            <br><br>";
?>