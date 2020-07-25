<?php
session_start();
require_once("pdo.php");
require_once("functions.php");
if (isset($_POST["cancel"])) {
	header("Location: index.php");
	return;
}
$salt = "XyZzy12*_";
if (isset($_POST["done"])) {
	unset($_SESSION["user_id"]);
	$check = hash('md5', $salt.$_POST["pass"]);
	$stmt = $pdo->prepare("SELECT user_id, name from users where email = :em and password = :pw");
	$stmt->bindParam(':em', $_POST["email"]);
	$stmt->bindParam(':pw', $check);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_LAZY);
	if ($row !== false) {
		$_SESSION["name"] = $row["name"];
		$_SESSION["user_id"] = $row["user_id"];
		header("Location: index.php");
		return;
	}
	else {
		$_SESSION["error"] = "Incorrect password";
		header("Location: login.php");
		return;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>3e307b6a</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 
</head>
<body>
	<div class = "container">
	<h1>Please Log In</h1>
	<?php
	findError();
	?>
	<form method = "POST" action = "login.php">
		<label for = "email">Email</label>
		<input type="text" name="email" id = "email"><br>
		<label for = "id_1723">Password</label>
		<input type="password" name="pass" id = "id_1723"><br>
		<input type="submit" name = "done" onclick="return doValidate();" value = "Log In">
		<input type="submit" name="cancel" value = "Cancel">
	</form>
	<script>
		function doValidate() {
			console.log('Validating...');
			try {
				addr = document.getElementById('email').value;
				pw = document.getElementById('id_1723').value;
				console.log("Validating addr = "+addr+" pw = "+pw);
				if (addr == null || addr == "" || pw == null || pw == "") {
					alert("Both fields must be filled out");
					return false;
				}
				if (addr.indexOf('@') == -1) {
					alert("Invalid email address");
					return false;
				}
				return true;
			} catch (e) {
				return false;
			}
			return false;
		}
	</script>
</div>
</body>
</html>