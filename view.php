<?php
session_start();
require_once("pdo.php");
require_once("functions.php");
if (!isset($_SESSION["user_id"])) {
	die('ACCESS DENIED');
}
if (!isset($_GET["profile_id"])) {
	setError("Missing profile_id", "index.php");
}
$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name, email, headline, summary from profile where profile_id = :id");
$stmt->bindParam(":id", $_GET["profile_id"]);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_LAZY);
if ($row === false) {
	setError("Could not load profile", "index.php");
}
$stmt = $pdo->prepare("SELECT * from position where profile_id = :id order by rank");
$stmt->bindParam(":id", $_GET["profile_id"]);
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT education.year, institution.name from education, institution, profile where profile.profile_id = :idProf and profile.user_id = :idUser and education.profile_id = profile.profile_id and institution.institution_id = education.institution_id order by education.rank");
$stmt->bindParam(":idProf", $_GET["profile_id"]);
$stmt->bindParam(":idUser", $_SESSION["user_id"]);
$stmt->execute();
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	<h1>Profile information</h1>
	<?php
	echo "<p>First name: ".htmlentities($row["first_name"])."</p>";
	echo "<p>Last name: ".htmlentities($row["last_name"])."</p>";
	echo "<p>Email: ".htmlentities($row["email"])."</p>";
	echo "<p>Headline: ".htmlentities($row["headline"])."</p>";
	echo "<p>Summary: ".htmlentities($row["summary"])."</p>";
	if (count($positions) != 0) {
		echo "<p>Position: </p><ul>";
		foreach($positions as $position) {
			echo "<li>".$position["year"].": ".htmlentities($position["description"])."</li>";
		}
		echo "</ul>";
	}
	if (count($educations) != 0) {
		echo "<p>Education: </p><ul>";
		foreach($educations as $education) {
			echo "<li>".$education["year"].": ".htmlentities($education["name"])."</li>";
		}
		echo "</ul>";
	}
	?>
	<a href="index.php">Done</a>
</div>
</body>
</html>