<?php
  $mysqli = new mysqli("db", "root", "password");
  mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
  $movieId = $_GET["id"];
?>
<!DOCTYPE html>
<html>
  <header>
  <section>
    <h1 style="text-align:center;">
			<a href="web.php"> Main Webpage</a> 
      <br><br>
      <a href="web_personality.php"> Personality</a> 
		</h1>

    <br><br>
  </section>
</header>
<body>

  <section>
		<h2>Information About Film</h2>
    <?php include '2nd_usecase.php'; ?>

    <br><br>
  </section>
</body>
</html> 