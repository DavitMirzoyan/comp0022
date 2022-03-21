<?php
    include "Cache.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $all_films = $_POST["tags"];
        $films = explode(",",$all_films);
        $copy_array = array();
        $each_film_tags = array();
 
        foreach ($films as $film_name){
            $get_movieId = "SELECT movieId FROM database.movies WHERE title LIKE BINARY '$film_name '";
            $id = (int)mysqli_fetch_row($mysqli->query($get_movieId))[0];
            
            $find_tag = "SELECT group_concat(DISTINCT tag) as tag FROM database.tags WHERE movieId = $id";
            $tags = mysqli_fetch_row($mysqli->query($find_tag))[0];
            
            array_push($copy_array, "'".$id."'");
            $each_film_tags[$tags] = "";
            //array_push($each_film_tags, "'".$tags."'");
        }

        $films = implode(", ", $copy_array);

        $joined_tables = "(SELECT tag, 
                                ratings_hashed.movieId,
                                ratings_hashed.userId_hashed, 
                                ratings_hashed.rating,
                                personality_types.openness,
                                personality_types.agreeableness,
                                personality_types.emotional_stability,
                                personality_types.conscientiousness,
                                personality_types.extraversion
                        FROM database.ratings_hashed 
                        LEFT JOIN database.personality_types 
                        ON ratings_hashed.userId_hashed = personality_types.userId_hashed
                        LEFT JOIN (SELECT movieId, group_concat(DISTINCT tag) as tag FROM database.tags GROUP BY movieId) as tags
                        ON tags.movieId = ratings_hashed.movieId
                        WHERE ratings_hashed.movieId IN ($films) 
                        LIMIT 300) as st";

        $all_values = "SELECT st.tag, st.movieId,
                               st.userId_hashed, st.rating, 
                               st.openness,
                               st.agreeableness,
                               st.emotional_stability,
                               st.conscientiousness,
                               st.extraversion
                        FROM "; 
        $final_query = $all_values .$joined_tables." WHERE st.rating >= 4";
        $all_ratings = $all_values . $joined_tables;

        // 5,6,7: high
        // 3,4: medium
        // 1,2: low


        foreach($each_film_tags as $tag_key => $given_tag){
            $map_type = array("openness" => "", 
                          "agreeableness" => "", 
                          "emotional_stability" => "",
                          "conscientiousness" => "",
                          "extraversion" => "");

            foreach ($map_type as $type => $value){
                $final_result = array();

                $count = "SELECT f.tag as tag, COUNT(f.$type) as total_appeared, f.$type 
                        FROM (" . $final_query . ") as f 
                        WHERE f.tag = '$tag_key'
                        GROUP BY f.$type 
                        ORDER BY total_appeared DESC
                        LIMIT 3";

                //echo $count;
                
                $result = $mysqli->query($count);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $total = (int) $row["total_appeared"];
                        $type_rating = (int) $row[$type];
                        //echo $type_rating;
                        if($type_rating===5 or $type_rating===6 or $type_rating===7){
                            array_push($final_result, 2);
                        }

                        elseif($type_rating===3 or $type_rating===4){
                            array_push($final_result, 1);
                        }

                        else{
                            array_push($final_result, 0);
                        }
                    }

                    $values = array_count_values($final_result);
                    arsort($values);
                    reset($values);
                    $firstKey = key($values);
                    $map_type[$type] = $firstKey;
                } else {
                    echo "0 results";
                }
            
            }

            $each_film_tags[$tag_key] = $map_type;
        }

        print_r($each_film_tags);

        //print_r($map_type);

        $result = $mysqli->query($all_ratings);
        //$cache_result = check_cache($final_query, $result);

        echo "<h2>List of films</h2>
        <table border='1'>
        <tr>
        <th>movieId</th>
        <th>tag</th>
        <th>userid</th>
        <th>rating</th>
        <th>user liked film ?</th>
        <th>open</th>
        <th>agree</th>
        <th>emotion</th>
        <th>conc</th>
        <th>extra</th>
        </tr>";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $movieId = $row["movieId"];
                //echo gettype($movieId);
                $tag = $row["tag"];
                $id = $row["userId_hashed"];
                $rating = $row["rating"];

                $open = $row["openness"];
                $agree = $row["agreeableness"];
                $emotion = $row["emotional_stability"];
                $conc = $row["conscientiousness"];
                $extra = $row["extraversion"];

                $user_type_values = array($open, $agree, $emotion, $conc, $extra);
                $liked = predict($user_type_values, $tag, $each_film_tags);

                echo "<tr>", 
                "<td> $movieId </td>", 
                "<td> $tag </td>", 
                "<td> $id </td>",   
                "<td> $rating </td>",  
                "<td> $liked </td>", 
                "<td> $open </td>", 
                "<td> $agree </td>", 
                "<td> $emotion </td>",   
                "<td> $conc </td>",  
                "<td> $extra </td>", 
                "</tr>";
            }
        } else {
            echo "0 results";
        }

        echo "</table>
        <br><br>";
    }

    function predict($user_type_values, $tag, $tag_trained_values){
        $personality_types = array("openness", "agreeableness", "emotional_stability", "conscientiousness", "extraversion");
        $final_result = 0;

        $trained_data = $tag_trained_values[$tag];

        for($i = 0; $i < count($user_type_values); $i+=1){
            $type_rating = (int) $user_type_values[$i];
            $pers_type = $personality_types[$i];
            $trained_val = $trained_data[$pers_type];

            if($type_rating===5 or $type_rating===6 or $type_rating===7){
                $result = 2;
            }

            elseif($type_rating===3 or $type_rating===4){
                $result = 1;
            }

            else{
                $result = 0;
            }

            if($result === $trained_val){
                $final_result++;
            }
        }

        if($final_result>=4){
            return "yes";
        }

        return "no";
    }
?>