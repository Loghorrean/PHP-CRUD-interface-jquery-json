<?php
function findError() // displaying flash error
{
	if (isset($_SESSION["error"])) 
	{
		echo '<p style = "color : red">'.$_SESSION["error"]."</p>\n";
		unset($_SESSION["error"]);
	}
}

function findSuccess() // displaying flash success
{
	if (isset($_SESSION["success"])) 
	{
		echo '<p style = "color: green">'.$_SESSION["success"]."</p>\n";
		unset($_SESSION["success"]);
	}
}

function doPosValidate() // checking if the array of positions validate
{ 
	for ($i=1; $i <= 9; $i++) 
	{
		if (!isset($_POST["year".$i])) 
		{
			continue;
		}
		if (!isset($_POST["desc".$i])) 
		{
			continue;
		}
		if (empty($_POST["desc".$i]) || empty($_POST["year".$i])) 
		{
			$_SESSION["error"] = "All fields are required";
			return false;
		}
		if (!is_numeric($_POST["year".$i])) 
		{
			$_SESSION["error"] = "Year must be numeric";
			return false;
		}
	}
	return true;
}

function doEduValidate() // checking if the array of educations validate
{
	for ($i = 1; $i <= 9; $i++) {
		if (!isset($_POST["edu_year".$i])) 
		{
			continue;
		}
		if (!isset($_POST["edu_school".$i])) 
		{
			continue;
		}
		if (empty($_POST["edu_year".$i]) || empty($_POST["edu_school".$i]))
		{
			$_SESSION["error"] = "All fields are required";
			return false;
		}
		if (!is_numeric($_POST["edu_year".$i]))
		{
			$_SESSION["error"] = "Year must be numeric";
			return false;
		}
	}
	return true;
}

function insertPos($pdo, $profile_id) // inserting position
{ 
		$rank = 1;
		$stmt = $pdo->prepare("INSERT into position (profile_id, rank, year, description) VALUES (:id, :rn, :yr, :dsc)");
		$stmt->bindParam('id', $profile_id);
		$stmt->bindParam('rn', $rank);
		for ($i = 1; $i <= 9; $i++) 
		{
			if (!isset($_POST["year".$i])) 
			{
				continue;
			}
			if (!isset($_POST["desc".$i])) 
			{
				continue;
			}
			$stmt->bindParam('yr', $_POST["year".$i]);
			$stmt->bindParam('dsc', $_POST["desc".$i]);
			$stmt->execute();
			$rank++;	
		}
}

function insertEdu($pdo, $profile_id)
{
	$institution_id = false;
	$rank = 1;
	$stmt = $pdo->prepare("INSERT into education (profile_id, institution_id, rank, year) VALUES (:idPro, :idInst, :rn, :yr)");
	$stmt->bindParam(':idPro', $profile_id);
	$stmt->bindParam(':rn', $rank);
	for ($i = 1; $i <= 9; $i++)
	{
		if (!isset($_POST["edu_year".$i]))
		{
			continue;
		}
		if (!isset($_POST["edu_school".$i]))
		{
			continue;
		}
		$sth = $pdo->prepare("SELECT institution_id from institution where name = :nm");
		$sth->bindParam(':nm', $_POST["edu_school".$i]);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_LAZY);
		if ($row !== false)
		{
			$institution_id = $row["institution_id"];
		}
		else
		{
			$sth = $pdo->prepare("INSERT into institution (name) VALUES (:nm)");
			$sth->bindParam(":nm", $_POST["edu_school".$i]);
			$sth->execute();
			$instituion_id = $pdo->lastInsertId();
		}
		$stmt->bindParam(":idInst", $institution_id);
		$stmt->bindParam(":yr", $_POST["edu_year".$i]);
		$stmt->execute();
		$rank++;
	}
}

function setError($message, $path) // setting session error
{
	$_SESSION["error"] = $message;
	header("Location: $path");
	return;
}

function setSuccess($message, $path) // setting session success
{
	$_SESSION["success"] = $message;
	header("Location: $path");
	return;
}
?>