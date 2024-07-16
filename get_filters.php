<?php
$host = 'localhost';
$db = 'employees_db';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqlCountries = "SELECT DISTINCT Country FROM employees";
$sqlCities = "SELECT DISTINCT City FROM employees";

$stmtCountries = $pdo->prepare($sqlCountries);
$stmtCities = $pdo->prepare($sqlCities);

$stmtCountries->execute();
$stmtCities->execute();

$countries = $stmtCountries->fetchAll(PDO::FETCH_COLUMN);
$cities = $stmtCities->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['countries' => $countries, 'cities' => $cities]);
?>
