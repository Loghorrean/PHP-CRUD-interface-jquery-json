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
if (isset($_POST["done"])) {
	if (empty($_POST["first_name"]) || empty($_POST["last_name"]) || empty($_POST["email"]) || empty($_POST["headline"]) || empty($_POST["summary"])) {
		$_SESSION["error"] = "All fields are required";
		header("Location: add.php");
		return;
	}
	else if (stripos($_POST["email"], '@') === false) {
		$_SESSION["error"] = "Email address must contain @";
		header("Location: add.php");
		return;
	}
	else if (!doPosValidate() || !doEduValidate()) {
		header("Location: add.php");
		return;
	} 
	else {
		$stmt = $pdo->prepare("INSERT into profile (user_id, first_name, last_name, email, headline, summary) VALUES (:id, :fn, :ln, :em, :hl, :sum)");
		$stmt->bindParam('id', $_SESSION["user_id"]);
		$stmt->bindParam('fn', $_POST["first_name"]);
		$stmt->bindParam('ln', $_POST["last_name"]);
		$stmt->bindParam('em', $_POST["email"]);
		$stmt->bindParam('hl', $_POST["headline"]);
		$stmt->bindParam('sum', $_POST["summary"]);
		$stmt->execute();
		$profile_id = $pdo->lastInsertId();
		insertPos($pdo, $profile_id);
		insertEdu($pdo, $profile_id);
		setSuccess("Profile added", "index.php");
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
	<h1>Adding profile for <?=$_SESSION["name"]?></h1>
	<?php
	findError();
	?>
	<form method = "POST">
		<p>First name: <input type="text" name="first_name" size = "60"></p>
		<p>Last name: <input type="text" name="last_name" size = "60"></p>
		<p>Email: <input type="text" name="email" size = "30"></p>
		<p>Headline:<br><input type="text" name="headline" size = "80"></p>
		<p>Summary:<br><input type="text" name="summary" rows = "8" cols = "80"></p>
		<p>Education: <input type="submit" id = "addEdu" value = "+"></p>
		<p>Position: <input type = "submit" id = "addPos" value = "+"></p>
		<div id = "edu_fields"></div>
		<p></p>
		<div id = "position_fields"></div>
		<p>
			<input type="submit" name="done" value = "Add">
			<input type="submit" name="cancel" value = "Cancel">
		</p>
	</form>
	<script> // скрипт добавления новых полей
		countPos = 0;
		countEdu = 0;
		$(document).ready(function() {
			console.log('Document ready called');
			$('#addPos').click(function(event) {
				event.preventDefault();
				if (countPos >= 9) {
					alert('Maximum of nine positions entries exceeded');
					return;
				}
				countPos++;
				console.log('Adding position '+countPos);
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