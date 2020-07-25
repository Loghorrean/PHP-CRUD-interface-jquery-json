<?php
require_once("pdo.php");
header('Content-Type: application/json; charset=utf-8');
$stmt = $pdo->prepare("SELECT name from institution where name LIKE :prefix");
$keyword = $_REQUEST["term"]."%";
$stmt->bindParam(":prefix", $keyword);
$stmt->execute();
$names = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
	$names[] = $row["name"];
}
echo(json_encode($names, JSON_PRETTY_PRINT));
?>