<!DOCTYPE html>

<?php include("database.php");?>
<?php include("header.php");?>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="">
  <title>UrbanFork</title>
  <link href="css/pin.css" rel = "stylesheet">
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/search.css" rel = "stylesheet">
</head>
<body>
	

	<div class = "container">
		<div id = "inputContainer">
			<form  method="post" action="search.php?go"  id="searchform"> 
				<p class="text-center search-title">Search Restaurants</p>  
				<div class="searchBar">
					<input id = "center-bar" class = "center-bar" type="text" placeholder="Search..." name="query">
					<button class="btn-primary" type="submit" name="submit">
						<span class="glyphicon glyphicon-search"></span>
					</button>
				</div>
				
				<div class = "row">
					<div class = "col-md-6 options">
						<p class = "sectionHead" >Additional Information</p>
						<input type="checkbox" name="checkCuisine" value="cuisine">   Cuisine<br>
						<input type="checkbox" name="checkPhone" value="phone-number">  Phone number<br>
						<input type="checkbox" name="checkDes" value="description">  Description<br>
					</div>
					<div class = "col-md-6 options">
					<p class = "sectionHead" >Choose Cuisine</p>
						<input type="radio" name="cuisine" value="chinese">  Chinese<br>
						<input type="radio" name="cuisine" value="french">  French<br>
						<input type="radio" name="cuisine" value="italian">  Italian<br>
						<input type="radio" name="cuisine" value="korean">  Korean<br>
						<input type="radio" name="cuisine" value="german">  German<br>
						<input type="radio" name="cuisine" value="japanese">  Japanese<br>
					</div>
				</div>
	
			</form>
		</div>
	</div>
	
	<div class = "container">
		<div class="row">
			<div class = "col-md-6">
				<form method="post" action=""  id="expensiveBtn">
					<button class = "btn-nested-aggr" type = "submit" name = "expensiveDish">
						Most expensive restaurant
					</button>
				</form>
			</div>
			
			<div class = "col-md-6">
				<form method="post" action=""  id="cheapBtn">
					<button class = "btn-nested-aggr" type = "submit" name = "cheapDish">
						Cheapest restaurant
					</button>
				</form>
			</div>
		</div>
	</div>


	<?php
		function displayOutput($result){
				while($row = mysqli_fetch_array($result)){
					echo "<div class = 'Output'>";
					
					echo "<br>";
					
					$row = (array_unique($row, SORT_STRING));
					
					$rname = $row[0];
					$location = $row[1];
					
					$fileName = str_replace(' ', '', $location.$rname);
					
					$hrefRname = str_replace(' ', '%20', $rname);
					$hrefLoc = str_replace(' ', '%20', $location);
					
					$hrefPath = "http://localhost/Urbanfork/restaurant.php?rname=".$hrefRname."&location=".$hrefLoc;
					
					$imagePath = "./img/searchImage/".$fileName.".jpg";
					echo "<br>";
					?>
					
					
					<div class = "pin">
						<div class = "image">
							<a href = <?php echo $hrefPath ?>>
								<img src= <?php echo $imagePath ?> alt="Test" style="width:304px;height:228px;">
							</a>
						</div>
						
						<div class = "row">
							<div class = "col text_output">
								<p>
									<?php foreach($row as $value){
										echo $value;
										echo "<br>";
										echo "<br>";
										} ?>
								</p>
							</div>
						</div>
					
					</div>
				
					
					<?php
					echo "</div>";
				}
		}
		
		function displayNestedAggr($result){
			while($row = mysqli_fetch_array($result)){
				$rname = $row[0];
				$loc = $row[1];
				$val = round($row[2],2);					
				$hrefRname = str_replace(' ', '%20', $rname);
				$hrefLoc = str_replace(' ', '%20', $loc);
				$hrefPath = "http://localhost/Urbanfork/restaurant.php?rname=".$hrefRname."&location=".$hrefLoc;
					
				?>
					<div class="container">
						<div class="row">
							<div class = "nested-aggr-output">
								<a href = <?php echo $hrefPath ?>>
									<?php echo $rname ?>
								</a>
								<br>
								<?php echo $loc ?>
								<br>
								<?php echo "Average price of restaurant: ".$val ?>
							</div>
						</div>
					</div>
				
				<?php
			}
		}
		
		if(isset($_POST['submit'])){
			if(isset($_GET['go'])){
				$name=$_POST['query'];
				
				$select_string = " rname, location";
						
				if(isset($_POST['checkCuisine'])){
					$select_string = $select_string.", cuisine";
				}
				if(isset($_POST['checkPhone'])){
					$select_string = $select_string.", phone";
				}
				if(isset($_POST['checkDes'])){
					$select_string = $select_string.", description";
				}
				
				
				if(isset($_POST['cuisine'])){
					$cuisine_name = $_POST['cuisine'];
					$sql = "(SELECT".$select_string." FROM restaurant WHERE cuisine LIKE '%".$cuisine_name."%')";
					$result = mysqli_query($con, $sql) or die(mysqli_error($con));
					displayOutput($result);
				}
				else if(!isset($_POST['cuisine'])){
					$sql="SELECT" . $select_string . " FROM restaurant WHERE location LIKE '%" . $name . "%' OR rname LIKE '%" . $name  ."%'"; 
					$result = mysqli_query($con, $sql) or die(mysqli_error($con));
					displayOutput($result);
				}				
				
			}
		}
		
		
		if(isset($_POST['expensiveDish'])){
			$sql = "SELECT temp.rname, temp.location, temp.avgprice
					FROM (SELECT r.rname, r.location, AVG(d.price) AS avgprice
						  FROM restaurant r, contains c, dishes d
						  WHERE r.location = c.location AND r.rname = c.rname AND c.dishid = d.dishid
						  GROUP BY r.rname, r.location) as temp
					 WHERE temp.avgprice = (SELECT MAX(t1.avgprice) FROM
												(SELECT r.rname, r.location, AVG(d.price) AS avgprice
												  FROM restaurant r, contains c, dishes d
												  WHERE r.location = c.location AND r.rname = c.rname AND c.dishid = d.dishid
												  GROUP BY r.rname, r.location) as t1)";
										
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));
			displayNestedAggr($result);
		}
		
		if(isset($_POST['cheapDish'])){
			$sql = "SELECT temp.rname, temp.location, temp.avgprice
					FROM (SELECT r.rname, r.location, AVG(d.price) AS avgprice
						  FROM restaurant r, contains c, dishes d
						  WHERE r.location = c.location AND r.rname = c.rname AND c.dishid = d.dishid
						  GROUP BY r.rname, r.location) as temp
					 WHERE temp.avgprice = (SELECT MIN(t1.avgprice) FROM
												(SELECT r.rname, r.location, AVG(d.price) AS avgprice
												  FROM restaurant r, contains c, dishes d
												  WHERE r.location = c.location AND r.rname = c.rname AND c.dishid = d.dishid
												  GROUP BY r.rname, r.location) as t1)";
										
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));
			displayNestedAggr($result);
		}
	?>
		
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.js"></script>  
  <script>
    jQuery("#search").addClass("active");
  </script>
</body>
</html>
