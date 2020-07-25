<?php
session_start();
require_once("pdo.php");
require_once("functions.php");
if (!isset($_SESSION["user_id"])) {
	die('ACCESS DENIED');
}
if (isset($_POST["cancel"])) {
	header("Location: index.php");
	return;
}
if (!isset($_GET["profile_id"])) {
	setError("Missing profile_id", "index.php");
}
$stmt = $pdo->prepare("SELECT * from profile where profile_id = :id");
$stmt->bindParam('id', $_GET["profile_id"]);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_LAZY);
if ($row === false) {
	setError("Could not load profile", "index.php");
}
$profile_id = $row["profile_id"];
if (isset($_POST["delete"])) {
	$stmt = $pdo->prepare("DELETE from profile where profile_id = :id");
	$stmt->bindParam('id', $_POST["profile_id"]);
	$stmt->execute();
	setSuccess("Profile deleted", "index.php");
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
	<h1>Deleting profile</h1>
	<form method = "POST">
		<p>First name: <?=htmlentities($row["first_name"])?></p>
		<p>Last name: <?=htmlentities($row["last_name"])?></p>
		<input type = "hidden" name = "profile_id" value = "<?=$profile_id?>">
		<input type = "submit" name = "delete" value = "Delete">
		<input type="submit" name="cancel" value = "Cancel">
	</form>
</div>
</body>
</html>