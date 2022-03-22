<?php
    include "Cache.php";

    $mysqli = new mysqli("db", "root", "password", "database");
    mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $genres = $_POST["genres"];
        $rating = $_POST["rating"];
        $movie_name = $_POST["movie_name"];
        $start_year = $_POST["start_year"];
        $end_year = $_POST["end_year"];
        $popular_polarizing = $_POST["stats"];

        $year_sort = $_POST["year_sort"];
        $rating_sort = $_POST["rating_sort"];
        $movie_sort = $_POST["movie_sort"];
        $genre_sort = $_POST["genre_sort"];


        $select_clause = "SELECT m.movieId, m.title, m.year, m.ratings, group_concat(DISTINCT g.genres) as genres";
        $from_clause = "FROM database.genres as g 
                        JOIN database.movieId_genreId as mg ON mg.genresId = g.genresId 
                        JOIN database.movies as m ON m.movieId = mg.movieId";
        $where_clause = "WHERE";
        $group_clause = "GROUP BY m.movieId";
        $sort_clause = "ORDER BY";

        if($genres!="None"){
            
            $genresId_query = "SELECT genresId FROM genres as g WHERE g.genres LIKE ?";
            
            $stmt = $mysqli->prepare($genresId_query);
            $stmt->bind_param("s",$genres);
            $stmt->execute();
            $result = mysqli_fetch_assoc($stmt->get_result());
            $genresId = $result['genresId'];

            if ($where_clause!=="WHERE"){
                $where_clause .= " and";
            }

            $where_clause .= " mg.genresId = $genresId";
            //$where_clause .= " mg.genresId LIKE '%$genresId%'"
        }

        if ($rating!="None"){
            if ($where_clause!=="WHERE"){
                $where_clause .= " and";
            }
            
            $from_clause .= " LEFT JOIN database.ratings as r ON m.movieId = r.movieId";
            $where_clause .= " m.ratings >= $rating - 0.5 and m.ratings <= $rating + 0.5";
        }

        if ($movie_name!==""){
            if ($where_clause!=="WHERE"){
                $where_clause .= " and";
            }

            $where_clause .= " m.title LIKE '%$movie_name%'";
        }

        if ($start_year !== "None" or $end_year !== "None"){
            if ($start_year === "None"){
                $start_year = $end_year;
            }

            elseif ($end_year === "None"){
                $end_year = $start_year;
            }

            if ($end_year < $start_year){
                throw new Exception("'End Year' must be greater than 'Start Year'");
            }

            if ($where_clause!=="WHERE"){
                $where_clause .= " and";
            }

            $where_clause .= " m.year >= $start_year AND m.year <= $end_year";
        }
        
        $pop_pol = "";

        if ($popular_polarizing !== "None"){
            if($popular_polarizing === "popular"){
                $pop_pol = "Views";
                $sum_all_voters = "SELECT movieId, Count(rating) as Total_Sum FROM database.ratings GROUP BY movieId";           
               
            }else{
                $pop_pol = "Variance";
                $sum_all_voters = "SELECT movieId, Variance(rating) as Total_Sum FROM database.ratings GROUP BY movieId";
            }

            $select_clause .= ", " . "c.Total_Sum";
            $from_clause .= " LEFT JOIN " . "(" . $sum_all_voters . ") as c ON m.movieId = c.movieId";  
            $sort_clause .= " Total_Sum DESC";
        }

        $s_year = "";
        $year_order = "";

        if($year_sort==="descending"){
            $s_year = "year";
            $year_order = "DESC";
        }

        elseif($year_sort==="ascending"){
            $s_year = "year";
            $year_order = "ASC";
        }

        if($year_sort!=="None"){
            if($sort_clause!=="ORDER BY"){
                $sort_clause .= ",";
            }
            $sort_clause .= " " . $s_year . " " . $year_order;
        }

        $s_rating = "";
        $rating_order = "";

        if($rating_sort==="descending"){
            $s_rating = "ratings";
            $rating_order = "DESC";
        }

        elseif($rating_sort==="ascending"){
            $s_rating = "ratings";
            $rating_order = "ASC";
        }

        if($rating_sort!=="None"){
            if($sort_clause!=="ORDER BY"){
                $sort_clause .= ",";
            }
            $sort_clause .= " " . $s_rating . " " . $rating_order;
        }

        $s_movie = "";
        $movie_order = "";

        if($movie_sort==="descending"){
            $s_movie = "title";
            $movie_order = "DESC";
        }

        elseif($movie_sort==="ascending"){
            $s_movie = "title";
            $movie_order = "ASC";
        }

        if($movie_sort!=="None"){
            if($sort_clause!=="ORDER BY"){
                $sort_clause .= ",";
            }
            $sort_clause .= " " . $s_movie . " " . $movie_order;
        }

        $s_genre = "";
        $genre_order = "";

        if($genre_sort==="descending"){
            $s_genre = "`genres`";
            $genre_order = "DESC";
        }

        elseif($genre_sort==="ascending"){
            $s_genre = "`genres`";
            $genre_order = "ASC";
        }

        if($genre_sort!=="None"){
            if($sort_clause!=="ORDER BY"){
                $sort_clause .= ",";
            }
            $sort_clause .= " " . $s_genre . " " . $genre_order;
        }

        if($where_clause==="WHERE"){
            $where_clause = "";
        }

        if($sort_clause==="ORDER BY"){
            $sort_clause = "";
        }

        $final_query = $select_clause . " " . $from_clause . " " . $where_clause . " " . $group_clause . " ". $sort_clause;

        //echo $final_query;

        if($pop_pol !== ""){
            $result = $mysqli->query($final_query);
            $returned_result = check_cache($final_query, $result);

            echo "<h2>List of films</h2>
                    <table border='1'>
                    <tr>
                    <th>movieId</th>
                    <th>Title</th>
                    <th>Year</th>
                    <th>Rating</th>
                    <th>Genres</th>
                    <th>$pop_pol</th>
                </tr>";
            
            for ($i = 0; $i < count($returned_result); $i+=6)  {
                $id = $returned_result[$i];
                $name = $returned_result[$i+1];
                $year = $returned_result[$i+2];
                $rating = $returned_result[$i+3];
                $genres = $returned_result[$i+4];

                if (is_null($returned_result[$i+5])){
                    $vote = 0;
                }else{
                    $vote = round($returned_result[$i+5], 2);
                }
        
                echo "<tr>", 
                "<td> $id </td>", 
                "<td> <a href=\"web_movie_info.php?id=$id\"> $name </a> </td>",  
                "<td> $year </td>", 
                "<td> $rating </td>", 
                "<td> $genres </td>",
                "<td> $vote </td>",  
                "</tr>";
            }
    
            /*
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $id = $row['movieId'];
                    $name = $row["title"];
                    $year = $row["year"];
                    $rating = $row["ratings"];
                    $genres = $row["genres"];
                    
                    if (is_null($row['Total_Sum'])){
                        $vote = 0;
                    }else{
                        $vote = round($row['Total_Sum'], 2);
                    }
    
                    echo "<tr>", 
                    "<td> $id </td>", 
                    "<td> <a href=\"web_movie_info.php?id=$id\"> $name </a> </td>",  
                    "<td> $year </td>", 
                    "<td> $rating </td>", 
                    "<td> $genres </td>",
                    "<td> $vote </td>",  
                    "</tr>";
                }
            } #else {
            #    echo "0";
            #}
            */
                    
            echo "</table>
                    <br><br>";
        }
        else{
            //echo "<h2> No Select type is chosen </h2>";
            all_movies($final_query);
        }
    }
    
    else{
        $sql =          "SELECT m.movieId as movieId, 
                               m.title as title, 
                               m.year as year, 
                               m.ratings as ratings, 
                               group_concat(DISTINCT g.genres) as genres 
                        FROM database.genres as g 
                        JOIN database.movieId_genreId as mg ON mg.genresId = g.genresId 
                        JOIN database.movies as m ON m.movieId = mg.movieId
                        GROUP BY m.movieId";
        //$sql = "SELECT * FROM database.movies";
        all_movies($sql);
    }

    function all_movies($sql){
        global $mysqli;

        $result = $mysqli->query($sql);        
        $returned_result = check_cache($sql, $result);


        echo "<table border='1'>
        <tr>
        <th>movieId</th>
        <th>title</th>
        <th>year</th>
        <th>ratings</th>
        <th>genres</th>
        </tr>";

        for ($i = 0; $i < count($returned_result); $i+=5)  {
            $id = $returned_result[$i];
            $name = $returned_result[$i+1];
            $year = $returned_result[$i+2];
            $rating = $returned_result[$i+3];
            $genres = $returned_result[$i+4];

            echo "<tr>", 
            "<td> $id </td>", 
            "<td> <a href=\"web_movie_info.php?id=$id\"> $name </a> </td>", 
            "<td> $year </td>",
            "<td> $rating </td>",
            "<td> $genres </td>",
             "</tr>";
        }
        
        /*
        while($row = $returned_result->fetch_assoc())
        {
            $id = $row['movieId'];
            $name = $row["title"];
            $year = $row['year'];
            $rating = $row['ratings'];
            $genres = $row['genres'];
            echo "<tr>", 
                "<td> $id </td>", 
                "<td> <a href=\"web_movie_info.php?id=$id\"> $name </a> </td>", 
                "<td> $year </td>",
                "<td> $rating </td>",
                "<td> $genres </td>",
                 "</tr>";
        }*/


        echo "</table>";
    }


    
?>