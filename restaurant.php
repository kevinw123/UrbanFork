<!DOCTYPE html>
<?php include("header.php");?>
<?php include("database.php");?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="">
		<title>UrbanFork</title>
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/restaurant.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		
		<?php
			$rname = $_GET['rname'];
			// echo $rname;
			$location = $_GET['location'];
			// echo $location;
			$hrefRname = str_replace(' ', '%20', $rname);
			$hrefLoc = str_replace(' ', '%20', $location);
					
			$statsPath = "stats.php?rname=".$hrefRname."&location=".$hrefLoc;
			
			
			$sql="SELECT * FROM restaurant WHERE rname = '" . $rname . "' AND location = '" . $location . "'";
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));
			while($row = mysqli_fetch_array($result)){
				$location = $row['location'];
				$rname = $row['rname'];
				$cuisine = $row['cuisine'];
				$description = $row['description'];
				$phone = $row['phone'];
			}
		?>
		<div class="container-fluid" id="box">
			<div class="container-fluid" id="messagebox">
				<div class="text-center" id="restaurant-name"><h1><?php echo $rname?></h1></div>
				<div class="text-center" id="description"><?php echo $description?></div>
				<br>
				<div class="text-center">Cuisine: <?php echo $cuisine?> | Location: <?php echo $location?> | Phone #: <?php echo $phone?></div>
				<ul class="list-inline" align="center">
					<li>
						<a href="#" title="Favourite" class="btn-social btn-outline"><i class="fa fa-star"></i></a>
					</li>
					<li>
						<a href="#" target="blank" title="Edit" class="btn-social btn-outline"><i class="fa fa-pencil"></i></a>
					</li>
					<li>
						<a href= <?php echo $statsPath ?> title="Statistics" class="btn-social btn-outline"><i class="fa fa-bar-chart"></i></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="text-center" id="menu"><h2>Menu</h2></div>
		<?php
			$sql="SELECT * FROM menu WHERE rname = '" . $rname . "' AND location = '" . $location . "'ORDER BY type DESC";
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));
			while($row = mysqli_fetch_array($result)){
				$type = $row['type'];
				echo "<div class = 'menu-type text-center'>";
					echo $type;
				echo "</div>";
				echo "<br>";
				$menu_query="SELECT * FROM contains JOIN dishes on dishes.dishid = contains.dishid WHERE rname = '" . $rname . "' AND location = '" . $location . "' AND type = '" . $type ."'";
				$menu_result = mysqli_query($con, $menu_query) or die(mysqli_error($con));
				echo "<div class = 'row'>";
					while($menu_row = mysqli_fetch_array($menu_result)){
						echo "<div class = 'col-sm-6 text-center menu-item'>";
							echo "<div class = 'text-center item-title'>";
								echo $menu_row['dname'] . '... ';
								echo $menu_row['price'];
							echo "</div>";
							echo "<div class = 'text-center item-description'>";
								echo $menu_row['description'];
							echo "</div>";
						echo "</div>";
					}
				echo "</div>";
				echo "<br>";
			}
		?>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>
	</body>
</html>