<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Movies</title>
	<link href="main.css" rel="stylesheet">
	
	<style>
        .container .row .col-sm-3 {
			
            border-bottom: 2px solid #000;
            margin-left: 0;
            margin-right: 0;
            padding-bottom: 10px;
            padding-top: 10px;
        }

		.sidenav a {
			padding: 6px 8px 6px 16px;
			text-decoration: none;
			font-size: 25px;
			color: #818181;
			display: block;
			}
    </style>
	
</head>

<body>


	<header id="header"><!--header-->

	</header>

	<section>
		<h1 style="text-align:center;">
			<a href="web_personality.php"> Personality</a> 
		</h1>
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<div class="left-sidebar">
						
                        <h2>Select Tables</h2>
                        <form method="post" action="web.php">

                        <label for="genres">Genre Type:</label>
                            <select name="genres" id="genres">
								<form action="list_genres.php" method="post">
								
                                <?php include 'list_genres.php'; ?>
                            </select>
                        <br><br>
                        
                        <label for="rating">Rating:</label>
							<select name="rating" id="rating">
							    <?php include 'list_ratings.php'; ?>
							</select>
                        <br><br>

                        <label for="movie">Search for movie by Name:</label>
							<input type="text" placeholder="Movie Title" id="movie_name" name="movie_name">  
                        <br><br>

                        <!--<h4>Year</h4>-->
                        <label for="year">Start Year:</label>
							<select name="start_year" id="start_year">
							    <?php include 'list_years.php'; ?>
							</select>
                        <br><br>


                        <label for="year">End Year:</label>
                            <select name="end_year" id="end_year">
							    <?php include 'list_years.php'; ?>
							</select>
                        
                        <br><br>

						<label for="stats_name">Films Statistics</label>
							<select name="stats" id="stats">
                                <option value="None"> None</option>
                                <option value="popular">Popular</option>
                                <option value="polarizing">Polarizing <l/option>
							</select>
                        
                        <br><br>

                        <h2>Sort Tables</h2>
						<!--
						<select name="sort" id="sort">
							<option value="None"> None</option>
							<optgroup label="Year">
								<option>Year: Ascending</option>
								<option>Year: Descending</option>
							</optgroup>
							<optgroup label="Rating">
								<option>Rating: Ascending</option>
								<option>Rating: Descending</option>
							</optgroup>
							<optgroup label="Movie Name">
								<option>Movie Name: Ascending</option>
								<option>Movie Name: Descending</option>
							</optgroup>
							<optgroup label="Genres">
								<option>Genres: Ascending</option>
								<option>Genres: Descending</option>
							</optgroup>
						</select>
		-->
                        <label for="sort">Year:</label>
							<select name="year_sort" id="year_sort">
                                <option value="None"> None</option>
                                <option value="ascending">Ascending Order</option>
                                <option value="descending">Descending Order</option>
							</select>
                        <br><br>

                        <label for="rating">Rating:</label>
							<select name="rating_sort" id="rating_sort">
                                <option value="None"> None</option>
                                <option value="ascending">Ascending Order</option>
                                <option value="descending">Descending Order</option>
							</select>
                        <br><br>

						<label for="rating">Movie Title:</label>
							<select name="movie_sort" id="movie_sort">
                                <option value="None"> None</option>
                                <option value="ascending">Ascending Order</option>
                                <option value="descending">Descending Order</option>
							</select>
                        <br><br>

						<label for="rating">Genres:</label>
							<select name="genre_sort" id="genre_sort">
                                <option value="None"> None  </option>
                                <option value="ascending">Ascending Order</option>
                                <option value="descending">Descending Order</option>
							</select>
                        <br><br>
						
						<input type="submit" value="Submit Data">
                        <!--
						<label for="statistics">Statistics</label>
							<select name="statistics" id="statistics">
							<option disabled selected value> - select an option - </option>
								<option value="popular">Most Popular</option>
								<option value="polarizing">Most Polarizing</option>
							</select>
                        <br><br>

						<label for="movie">Search for movie:</label>
							<input type="text" placeholder="Movie Title" id="movieTitle" name="movieTitle">  
                        <br><br>

						
                        -->
                        </form>
                        
						
						
					</div>
				
				</div>
				
				<div class="col-sm-9 padding-right">
				
					<div class="features_items">
						<form method="post" action="movie_info.php">
                        <h2 class="title text-center">Movies</h2>
                        <?php include '1st_3rd_usecase.php'; ?>
					</div>
				</div>
			</div>
		</div>
	
	</section>
</body>
</html>