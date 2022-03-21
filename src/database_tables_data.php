<?php
    //echo "Hello from group 6 \n";

    $mysqli = new mysqli("db", "root", "password");
    //$mysqli = new mysqli("mysql", "root", "password");

    //echo "hi";
    mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
    $local_infile = 'SET GLOBAL local_infile=1';

    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if ($result = $mysqli->query($local_infile)){
        //echo "local file is set \n";
    }else{
        //echo $mysqli->error;
    }

    $sql = "CREATE DATABASE IF NOT EXISTS `database`";
    
    if ($result = $mysqli->query($sql))
    {
        //echo "'database' created successfully \n";
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.genres(
        `genres` VARCHAR(30),
        `genresId` INT AUTO_INCREMENT PRIMARY KEY
    );";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table GENRES created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }

    $load_data = "LOAD DATA LOCAL INFILE 'Excel/genres.csv'
            INTO TABLE database.genres
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (genres , genresId)";

    if ($mysqli->query($load_data) == TRUE) {
       // echo "GENRES data loaded into table \n";
    } else {
       // echo "Error loading data: " . $mysqli->error;
    }

    
    $sql = "CREATE TABLE IF NOT EXISTS database.tags(
        `userId` BIGINT NOT NULL,
        `movieId` BIGINT NOT NULL,
        `tag` VARCHAR(60),
        `timestamp` BIGINT NOT NULL
    );";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table TAGS created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }
    
    $load_data = "LOAD DATA LOCAL INFILE 'Excel/tags.csv'
            INTO TABLE database.tags
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (userId , movieId, tag, timestamp)";

    if ($mysqli->query($load_data) == TRUE) {
        //echo "GENRES data loaded into table \n";
    } else {
        //echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.ratings(
        `userId` BIGINT NOT NULL,
        `movieId` BIGINT NOT NULL,
        `rating` INT NOT NULL,
        `timestamp` BIGINT NOT NULL
    );";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table RATINGS created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }
    
    $load_data = "LOAD DATA LOCAL INFILE 'Excel/ratings_movie.csv'
            INTO TABLE database.ratings
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (userId , movieId, rating, timestamp)";

    if ($mysqli->query($load_data) == TRUE) {
        //echo "RATINGS data loaded into table \n";
    } else {
       // echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.links(
        `movieId` BIGINT NOT NULL,
        `imdbId` VARCHAR (10) NOT NULL,
        `tmdbId` BIGINT NOT NULL
    );";

    if ($mysqli->query($sql) == TRUE) {
        //echo "table LINKS created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }
    
    $load_data = "LOAD DATA LOCAL INFILE 'Excel/links.csv'
            INTO TABLE database.links
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (movieId, imdbId, tmdbId)";

    if ($mysqli->query($load_data) == TRUE) {
       // echo "LINKS data loaded into table \n";
    } else {
       // echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.movieId_genreId(
        `movieId` BIGINT NOT NULL,
        `genresId` INT NOT NULL
    );";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table MOVIE_GENRES_ID created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }
    
    $load_data = "LOAD DATA LOCAL INFILE 'Excel/movieId_genresId.csv'
            INTO TABLE database.movieId_genreId
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (movieId, genresId)";

    if ($mysqli->query($load_data) == TRUE) {
      //  echo "MOVIE_GENRES_ID data loaded into table \n";
    } else {
      //  echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.movies(
        `movieId` BIGINT PRIMARY KEY,
        `title` VARCHAR(60) NOT NULL,
        `year` INT NOT NULL,
        `ratings` FLOAT NOT NULL
    );";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table MOVIES created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }
    
    $load_data = "LOAD DATA LOCAL INFILE 'Excel/copy_movie.csv'
            INTO TABLE database.movies
            FIELDS TERMINATED BY '|'
            LINES TERMINATED BY '\n'
            IGNORE 1 LINES
            (movieId, title, year, ratings)";

    if ($mysqli->query($load_data) == TRUE) {
       // echo "MOVIES data loaded into table \n";
    } else {
       // echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.personality_types(
        `userId_hashed` VARCHAR(60) PRIMARY KEY,
        `openness` DECIMAL,
        `agreeableness` DECIMAL,
        `emotional_stability` DECIMAL,
        `conscientiousness` DECIMAL,
        `extraversion` DECIMAL,
        `assigned_metric` VARCHAR(20),
        `assigned_condition` VARCHAR(20),
        `is_personalised`  INT,
        `enjoy_watching` INT
    );";

    if ($mysqli->query($sql) == TRUE) {
      //  echo "table PERSONALITY TYPES created successfully \n";
    } else {
      //  echo "Error creating table: " . $mysqli->error;
    }

    $load_data = "LOAD DATA LOCAL INFILE 'Excel/personality_types.csv'
    INTO TABLE database.personality_types
    FIELDS TERMINATED BY ','
    LINES TERMINATED BY '\n'
    IGNORE 1 LINES
    (userId_hashed, openness, agreeableness, emotional_stability, conscientiousness, extraversion, assigned_metric, assigned_condition, is_personalised, enjoy_watching)";


    if ($mysqli->query($load_data) == TRUE) {
      //  echo "PERSONALITY TYPES data loaded into table \n";
    } else {
      //  echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.personality_ratings(
        `userId_hashed` VARCHAR(60),
        `movieId` BIGINT,
        `rating` FLOAT,
        CONSTRAINT PK_personality_ratings PRIMARY KEY(userId_hashed, movieId),
        CONSTRAINT FK_userId FOREIGN KEY (userId_hashed) REFERENCES database.personality_types(userId_hashed)
        
    );
    
    ";

    if ($mysqli->query($sql) == TRUE) {
      //  echo "table PERSONALITY RATINGS created successfully \n";
    } else {
      //  echo "Error creating table: " . $mysqli->error;
    }


    $load_data = "LOAD DATA LOCAL INFILE 'Excel/personality_ratings.csv'
    INTO TABLE database.personality_ratings
    FIELDS TERMINATED BY ','
    LINES TERMINATED BY '\n'
    IGNORE 1 LINES
    (userId_hashed, movieId, rating)";


    if ($mysqli->query($load_data) == TRUE) {
       // echo "PERSONALITY RATINGS data loaded into table \n";
    } else {
        //echo "Error loading data: " . $mysqli->error;
    }

    $sql = "CREATE TABLE IF NOT EXISTS database.ratings_hashed(
        `userId_hashed` VARCHAR(60),
        `movieId` BIGINT,
        `rating` FLOAT,
        `timestamp` VARCHAR(100)
        
    );
    
    ";

    if ($mysqli->query($sql) == TRUE) {
       // echo "table PERSONALITY RATINGS created successfully \n";
    } else {
       // echo "Error creating table: " . $mysqli->error;
    }


    $load_data = "LOAD DATA LOCAL INFILE 'Excel/ratings.csv'
    INTO TABLE database.ratings_hashed
    FIELDS TERMINATED BY ','
    LINES TERMINATED BY '\n'
    IGNORE 1 LINES
    (userId_hashed, movieId, rating, timestamp)";


    if ($mysqli->query($load_data) == TRUE) {
       // echo "PERSONALITY RATINGS data loaded into table \n";
    } else {
      //  echo "Error loading data: " . $mysqli->error;
    }
 
?>