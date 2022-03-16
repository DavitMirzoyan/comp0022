<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $all_tags = $_POST["tags"];

        $join_tables = "SELECT tag, ratings_hashed.movieId
                        FROM database.ratings_hashed 
                        LEFT JOIN database.personality_types 
                        ON ratings_hashed.userId_hashed = personality_types.userId_hashed
                        LEFT JOIN database.tags ON ratings_hashed.movieId = tags.movieId
                        WHERE ratings_hashed.movieId = 2";

    }

    

    echo "hello";
?>