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
	$_SESSION["error"] = "Missing profile id";
	header("Location: index.php");
	return;
	// setError("Missing profile_id", "index.php");
}
$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name, email, headline, summary from profile where profile_id = :id");
$stmt->bindParam(":id", $_GET["profile_id"]);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_LAZY);
if ($row === false) {
	$_SESSION["error"] = "Could not load profile";
	header("Location: index.php");
	return;
}
$fn = htmlentities($row["first_name"]);
$ln = htmlentities($row["last_name"]);
$em = htmlentities($row["email"]);
$hl = htmlentities($row["headline"]);
$sm = htmlentities($row["summary"]);
$profile_id = $row["profile_id"];
$sthPos = $pdo->prepare("SELECT * from position where profile_id = :id");
$sthPos->bindParam('id', $_REQUEST["profile_id"]);
$sthPos->execute();
$sthEdu = $pdo->prepare("SELECT education.rank, education.year, institution.name from education join institution on institution.institution_id = education.institution_id where profile_id = :id");
$sthEdu->bindParam("id", $_REQUEST["profile_id"]);
$sthEdu->execute();
if (isset($_POST["done"])) {
	if (empty($_POST["first_name"]) || empty($_POST["last_name"]) || empty($_POST["email"]) || empty($_POST["headline"]) || empty($_POST["summary"])) {
		$_SESSION["error"] = "All fields are required";
		header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
		// setError("All fields are required", "edit.php?profile_id=".$_REQUEST["profile_id"]);
	}
	else if (stripos($_POST["email"], '@') === false) {
		$_SESSION["error"] = "Email address must contain @";
		header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
		// setError("Email address must contain @", "index.php");
	}
	else if (!doPosValidate() && !doEduValidate()) {
		header("Location: edit.php?profile_id=".$_REQUEST["profile_id"]);
		return;
	}
	else {
		$stmt = $pdo->prepare("UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sum where profile_id = :id");
		$stmt->bindParam('fn', $_POST["first_name"]);
		$stmt->bindParam('ln', $_POST["last_name"]);
		$stmt->bindParam('em', $_POST["email"]);
		$stmt->bindParam('hl', $_POST["headline"]);
		$stmt->bindParam('sum', $_POST["summary"]);
		$stmt->bindParam('id', $_POST["profile_id"]);
		$stmt->execute();
		$profile_id = $_POST["profile_id"];
		$stmt = $pdo->prepare("DELETE from position where profile_id = :id");
		$stmt->bindParam('id', $profile_id);
		$stmt->execute();
		insertPos($pdo, $profile_id);
		$stmt = $pdo->prepare("DELETE from education where profile_id = :id");
		$stmt->bindParam('id', $profile_id);
		$stmt->execute();
		insertEdu($pdo, $profile_id);
		setSuccess("Record edited", "index.php");
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
	<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
	<div class = "container">
	<h1>Editing Profile for <?=$_SESSION["name"]?></h1>
	<?php
	findError();
	?>
	<form method = "POST">
		<p>First name: <input type="text" name="first_name" size = "60" value = "<?=$fn?>"></p>
		<p>Last name: <input type="text" name="last_name" size = "60" value = "<?=$ln?>"></p>
		<p>Email: <input type="text" name="email" size = "30" value = "<?=$em?>"></p>
		<p>Headline:<br><input type="text" name="headline" size = "80" value = "<?=$hl?>"></p>
		<p>Summary:<br><textarea name="summary" rows = "8" cols = "80"><?=$sm?></textarea></p>
		<p>Education: <input type = "submit" id = "addEdu" value = "+"></p>
		<div id = "edu_fields">
			<?php
			$countEdu = 0;
			while ($line = $sthEdu->fetch(PDO::FETCH_LAZY))
			{
				echo '<div id = "edu'.$line["rank"].'">';
				echo '<p>Year: <input type = "text" name = "edu_year'.$line["rank"].'" value = "'.$line["year"].'">';
				echo '<input type = "button" value = "-" onclick = "$(\'#edu'.$line["rank"].'\').remove(); return false;"></p>';
				echo '<p>School: <input type = "text" name = "edu_school'.$line["rank"].'" class = "school ui-autocomplete-input" value = "'.htmlentities($line["name"]).'" autocomplete = "off" size = "80"></p>';
				echo '</div>';
				$countEdu++;
			}
			?>
		</div>
		<p>Position: <input type="submit" id = "addPos" value = "+"></p>
		<div id = "position_fields">
			<?php
			$countPos = 0;
			while ($line = $sthPos->fetch(PDO::FETCH_LAZY))
			{
				echo '<div id = "position'.$line["rank"].'">';
				echo '<p>Year: <input type = "text" name = "year'.$line["rank"].'" value = "'.$line["year"].'">';
				echo '<input type = "button" value = "-" onclick = "$(\'#position'.$line["rank"].'\').remove(); return false;"></p>';
				echo '<textarea name = "desc'.$line["rank"].'" rows = "8" cols = "80">'.htmlentities($line["description"]).'</textarea>';
				echo '</div>';
				$countPos++;
			}
			?>
		</div>
		<p>
			<input type = "hidden" name = "profile_id" value = <?=$profile_id?>>
			<input type="submit" name="done" value = "Save">
			<input type="submit" name="cancel" value = "Cancel">
		</p>
	</form>
	<script> // скрипт добавления новых полей
		countPos = <?=$countPos?>;
		countEdu = <?=$countEdu?>;
		$(document).ready(function() {
			console.log('Document ready called');
			$('#addPos').click(function(event) {
				event.preventDefault();
				if (countPos >= 9) {
					alert('Maximum of nine positions entries exceeded');
					return;
				}
				countPos++;
				console.log('Adding position'+countPos);
				$('#position_fields').append(
					'<div id = "position'+countPos+'"> \
					<p>Year: <input type="text" name = "year'+countPos+'" value = "" /> \
					<input type = "button" value = "-" \
					onclick = "$(\'#position'+countPos+'\').remove(); return false;"></p> \
					<p>Description: </p><textarea name = "desc'+countPos+'" rows = "8" cols = "80"></textarea> \
					</div>');
			});

			$('#addEdu').click(function(event) {
				event.preventDefault();
				if (countEdu >= 9) {
					alert('Maximum of nine education entries exceeded');
					return;
				}
				countEdu++;
				console.log('Adding education '+countEdu);
				var source = $('#edu-template').html();
				$('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));
				$('.school').autocomplete( {
					source: "school.php"
				});
			});
		});
	</script>
	<script type="text" id = "edu-template">
		<div id = "edu@COUNT@">
			<p>Year: <input type = "text" name = "edu_year@COUNT@" value = "">
			<input type = "button" onclick = "$('#edu@COUNT@').remove(); return false;" value = "-"></p>
			<p>School: <input type = "text" size = "80" name = "edu_school@COUNT@" class = "school" value = "">
			</p>
		</div>
	</script>
</div>
</body>
</html>