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
            
            $result =$mysqli->query($get_movieId);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $movieId = $row["movieId"];
                }
            } else {
                echo "0 results";
            }

            $personality_types = array("openness", "agreeableness", "emotional_stability", "conscientiousness", "extraversion");
            $rating_types = array(4, 4.5, 5);
            $trained_data = array('4' => "", '4.5'=> "", '5' => "");

            $two_tables = "database.ratings_hashed 
                            LEFT JOIN database.personality_types 
                            ON ratings_hashed.userId_hashed = personality_types.userId_hashed";

            $total_rows_query = "SELECT COUNT(*) as total_rows
                                 FROM $two_tables 
                                 WHERE ratings_hashed.movieId = $movieId";

                
            //$total_rows = mysqli_num_rows($mysqli->query($total_rows_query));
            //echo $total_rows;
            $total_rows = mysqli_fetch_row($mysqli->query($total_rows_query))[0];
            echo $total_rows;
            $trainging_rows = FLOOR($total_rows * 70 / 100);
            //$result = $mysqli->query($total_rows_query);



            $join_two_tables = "SELECT personality_types.openness,
                                       personality_types.agreeableness,
                                       personality_types.emotional_stability,
                                       personality_types.conscientiousness,
                                       personality_types.extraversion,
                                       ratings_hashed.userId_hashed,
                                       ratings_hashed.rating
                                FROM $two_tables
                                where ratings_hashed.movieId = $movieId 
                                LIMIT $trainging_rows offset 0
                               ";
            
            foreach($rating_types as $rating_val){
                $map_type = array("openness" => "", 
                                  "agreeableness" => "", 
                                  "emotional_stability" => "",
                                  "conscientiousness" => "",
                                  "extraversion" => "");
               
                foreach($personality_types as $type){
                    $got_values = array();

                    $query = " SELECT p.rating, COUNT(p.$type) as total_appeared, p.$type FROM (" 
                                . $join_two_tables
                                . ") as p WHERE p.rating  = $rating_val
                                    GROUP BY $type
                                    ORDER BY total_appeared DESC
                                    LIMIT 3
                                   ";

                    $result = $mysqli->query($query);
                    
                    echo "<h2>Each Type Amount </h2>
                    <table border='1'>
                        <tr>
                        <th>rating</th>
                        <th>$type</th>
                        <th>total_appeared</th>
                    </tr>
                    ";

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            
                            $rating = round($row["rating"],2);
                            $given_type_value = round($row[$type],2);
                            $total = round($row["total_appeared"],2);

                            array_push($got_values, $given_type_value);
                            
                            echo 
                            "<tr>", 
                                "<td align='center'>" .$rating. "</td>",
                                "<td align='center'>" .$given_type_value. "</td>", 
                                "<td align='center'>" .$total. "</td>",
                            "</tr>";
                        }
                    }
        
                    echo "</table>
                    <br><br>";

                    $map_type[$type] = $got_values;
                    
                }

                $rating_val = (string) $rating_val;
                $trained_data[$rating_val] = $map_type;
            }
            
            $rest_rows = $total_rows - $trainging_rows;
            $query_test_resting_rows = "SELECT personality_types.openness,
                                       personality_types.agreeableness,
                                       personality_types.emotional_stability,
                                       personality_types.conscientiousness,
                                       personality_types.extraversion,
                                       ratings_hashed.userId_hashed,
                                       ratings_hashed.rating
                                FROM $two_tables
                                where ratings_hashed.movieId = $movieId 
                                LIMIT $rest_rows offset $trainging_rows
                                ";
            
            echo "<h2>Each Type Amount </h2>
            <table border='1'>
                <tr>
                <th>user_id</th>
                <th>actual_rating</th>
                <th>predicted_rating</th>
            </tr>
            ";

            $result = $mysqli->query($query_test_resting_rows);

            if ($result->num_rows > 0) {
                $low = 0;
                $matched = 0;
                $count_rows = 0;
                while($row = $result->fetch_assoc()) {
                    $count_rows++;
                    $rating = round($row["rating"],2);
                    $openness = round($row["openness"],2);
                    $agreeableness = round($row["agreeableness"],2);
                    $emotional_stability = round($row["emotional_stability"],2);
                    $conscientiousness = round($row["conscientiousness"],2);
                    $extraversion = round($row["extraversion"],2);
         
                    $personality_types_ratings = array($openness, $agreeableness, $emotional_stability, $conscientiousness, $extraversion);
                    if($rating < 4){
                        $low +=1;
                    }

                    $found = 0;

                    foreach($trained_data as $key => $each_data){
                        $count = 0;
                        //print_r($each_data);
                        //echo "\n";

                        foreach($personality_types_ratings as $type_key => $get_rating){
                            $keys = array_keys($each_data);
                            
                            //print_r ($keys);
                            $values = $each_data[$keys[$type_key]];

                            if(in_array($get_rating, $values)){
                                $count++;
                            }
                        }

                        if($count>=4){
                            //echo $row["userId_hashed"];
                            //echo "\n";
                            //echo $rating;
                            //echo "\n";
                            $found = 1;
                            echo
                            "<tr>", 
                                "<td align='center'>" .$row["userId_hashed"]. "</td>",
                                "<td align='center'>" .$rating. "</td>", 
                                "<td align='center'>" .key($trained_data). "</td>",
                            "</tr>";
                            break;
                            /*
                            if($rating==key($trained_data)){
                                $found = 1;
                                $matched++;

                                //echo "matched \n";
                                //echo $row["userId_hashed"];
                                //echo "\n";
                                //echo 

                                
                                echo
                                "<tr>", 
                                    "<td align='center'>" .$row["userId_hashed"]. "</td>",
                                    "<td align='center'>" .$rating. "</td>", 
                                    "<td align='center'>" .key($trained_data). "</td>",
                                "</tr>";
                                break;
                            }*/
                            //else{
                            //    echo "not correct \n";
                            //}
                            //echo key($trained_data);
                            
                        }

                        //echo "not found";
                    }
                    
                    if ($found===0){
                        echo
                        "<tr>", 
                        "<td align='center'>" .$row["userId_hashed"]. "</td>",
                        "<td align='center'>" .$rating. "</td>", 
                        "<td align='center'>" ."not_high_rating". "</td>",
                        "</tr>";
                    }

                }
                //echo $matched;
                //echo "\n";
                //echo $low;
                //echo "\n";
                //echo $count_rows;
            }

            echo "</table>
            <br><br>";

            
                $i = 0;
                $r = 1;
                while($i<1){
                    $i += 1;
                    $query_getPersonalityData = " 
                        SELECT 
                                ratings_hashed.userId_hashed,
                                (ratings_hashed.rating) as rating,
                                (personality_types.openness) as openness,
                                (personality_types.agreeableness) as agreeableness,
                                (personality_types.emotional_stability) as emotional_stability,  
                                (personality_types.conscientiousness) as conscientiousness, 
                                (personality_types.extraversion) as extraversion
                        FROM (database.personality_types 
                            LEFT JOIN database.ratings_hashed ON ratings_hashed.userId_hashed = personality_types.userId_hashed)
                        WHERE ratings_hashed.movieId = $movieId 
                        LIMIT 169 offset 409
                
                        ";
                    $r += 0.5;
                    $result = $mysqli->query($query_getPersonalityData);
                    
                echo "<h2>Each rating vote</h2>
                <table border='1'>
                    <tr>
                    <th>count</th>
                    <th>id</th>
                    <th>rating</th>
                    <th>openess</th>
                    <th>agreeableness</th>
                    <th>emotionalStability</th>
                    <th>conscientiousness</th>
                    <th>extraversion</th>
                </tr>
                ";
    
                $count = 0;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $count += 1;
                        $user_id = $row["userId_hashed"];
                        $rating = round($row["rating"],2);
                        $openess = round($row["openness"],2);
                        $agreeableness = round($row["agreeableness"],2);
                        $emotionalStability = round($row["emotional_stability"],2);
                        $conscientiousness = round($row["conscientiousness"],2);
                        $extraversion = round($row["extraversion"],2);
 
                        echo 
                        "<tr>", 
                            "<td align='center'>" .$count. "</td>",
                            "<td align='center'>" .$user_id. "</td>",
                            "<td align='center'>" .$rating. "</td>",
                            "<td align='center'>" .$openess. "</td>", 
                            "<td align='center'>" .$agreeableness. "</td>", 
                            "<td align='center'>" .$emotionalStability. "</td>", 
                            "<td align='center'>" .$conscientiousness. "</td>", 
                            "<td align='center'>" .$extraversion. "</td>" , 
                        "</tr>";
                        
    
                    }
                }
                echo "</table>
                <br><br>";

                //echo $openess + $agreeableness + $emotionalStability + $conscientiousness + $extraversion;
            }
        }
    }
?>