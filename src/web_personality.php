<?php
    $mysqli = new mysqli("db", "root", "password", "database");
    mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
?>
<!DOCTYPE html>
<html>
<header>
  <section>
    <h1 style="text-align:center;">
			<a href="web.php"> Main Webpage</a> 
		</h1>

    <br><br>
  </section>
</header>
<body>

  <section>
		<h2>Information About Personality</h2>
    <form method="post" action="web_personality.php">
      
    <input type="text" placeholder="Movie Title" id="movie_name" name="movie_name">  
    <br><br>
    <input type="submit" value="Submit" name="button1">
    <br><br>

    <input type="text" placeholder="Set of Movie Titles" id="tags" name="tags">  
    <br><br>
    
    <input type="submit" value="Submit" name="button2">
    <br><br>
    
    <?php
        if(array_key_exists('button1', $_POST)) {
          include '5th_usecase.php';
        }
        else if(array_key_exists('button2', $_POST)) {
          include '6th_usecase.php';
        }
    ?>
    
    <br><br>

  </section>
</body>
</html> 