<?php
session_start();
require_once("pdo.php");
require_once("functions.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>3e307b6a</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
	<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
	<div class = "container">
	<h2>Welcome to the Resume Registry</h2>
	<?php
	if (!isset($_SESSION["user_id"])) { 
		echo '<p><a href="login.php">Please log in</a></p>';
	} else {
		echo '<p><a href = "logout.php">Logout</a></p>';
		findSuccess();
		findError();
	}	?>
			<table border = "1">
			<thead>
				<tr><th>Name</th><th>Headline</th>
				<?php
				if (isset($_SESSION["user_id"])) {
					echo '<th>Action</th>';
				}
				?>
			</tr></thead>
			<tbody>
			<?php
			$sth = $pdo->query("SELECT profile_id, first_name, last_name, headline from Profile");
			while ($line = $sth->fetch(PDO::FETCH_LAZY)) {
				echo '<tr><td><a href = "view.php?profile_id='.$line["profile_id"].'">'.htmlentities($line["first_name"]).' '.htmlentities($line["last_name"]).'</td><td>'.$line["headline"].'</td>';
				if (isset($_SESSION["user_id"])) {
				echo '<td><a href = "edit.php?profile_id='.$line["profile_id"].'">Edit</a> / ';
				echo '<a href = "delete.php?profile_id='.$line["profile_id"].'">Delete</a></td></tr>';
			}
			}
			echo '</tbody></table>';
			if (isset($_SESSION["user_id"])) {
				echo '<p><a href = "add.php">Add New Entry</a></p>';
			}
		?>
	</div>
</body>
</html>