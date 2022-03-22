<?php
    $mysqli = new mysqli("db", "root", "password", "database");
    mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        
        $movie_name = $_POST["movie_name1"];

        if($movie_name !== "" && ctype_alnum(str_replace(' ', '', $movie_name))){
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
    }
    if ($movieId !== NULL){
        $movieIdint = (int) $movieId;
        $get_movie_ratings ="SELECT `rating` FROM `ratings` WHERE `movieId` = '$movieIdint'";
        $result1 = $mysqli->query($get_movie_ratings);  
        //echo $get_movie_ratings;

        if ($result1->num_rows > 0) {    
            $ratings = array();
            while($row = mysqli_fetch_array($result1))
            {
                $ratings[] = $row['rating'];
            }
        } else {
            echo "No ratings";
        }
        $pred_sum = 0;
        $ratings_training = array_rand($ratings, floor(0.3*count($ratings)));
        for ($i = 0; $i < count($ratings); $i++)
        {
            $pred_sum += $ratings[$ratings_training[$i]];
        }
        $predicted_rating = $pred_sum/count($ratings_training);
        $actual_rating = array_sum($ratings)/count($ratings);
        echo "Predicted rating: ";
        echo round($predicted_rating,2);
        echo "\n";
        echo "Actual rating: ";
        echo round($actual_rating,2);
    }
    else
    {
        echo '<span style="color:#FF0000;text-align:center;">Invalid or no input!</span>';
    }