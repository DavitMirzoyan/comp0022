<?php
    $mysqli = new mysqli("db", "root", "password", "database");
    mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        
        $movie_name = $_POST["movie_name"];

        if($movie_name !== ""){
            $get_movieId = "SELECT movieId FROM database.movies WHERE title LIKE BINARY '$movie_name '";
            
            $movieId = -1;
            
            $result = $mysqli->query($get_movieId);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $movieId = (int) $row["movieId"];
            
                }
            } else {
                echo "0 results";
            }
        }
            $join_two_tables = "CREATE TABLE personality_ratings_types SELECT personality_ratings.userId_hashed, personality_ratings.rating, personality_types.openness,
            personality_types.agreeableness,personality_types.emotional_stability,personality_types.conscientiousness,personality_types.extraversion
            FROM `personality_ratings`
            LEFT JOIN `personality_types` ON personality_ratings.userId_hashed=personality_types.userId_hashed
            WHERE `movieId` = '1178'";

            $result = $mysqli->query($join_two_tables);

            echo "<table>";
            while($row = $result->fetch_assoc()) {
                $userid[] = $row['userId_hashed'];
                $ratings[] = $row['rating'];
                $openness[] = $row['openness'];
                $agreeableness[] = $row['agreeableness'];
                $emotional_stability[] = $row['emotional_stability'];
                $conscientiousness[] = $row['conscientiousness'];
                $extraversion[] = $row['extraversion'];

            echo "<tr><td>".$userId_hashed."</td><td>".$rating."</td><td>".$openness."</td><td>".$agreeableness."</td><td>".$emotional_stability."</td><td>".$conscientiousness."</td><td>".$extraversion."</td></tr>";
            }
            echo "</table>";
        }