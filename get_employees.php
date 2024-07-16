<?php
$host = 'localhost';
$db = 'employees_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $countries = isset($_GET['country']) ? $_GET['country'] : [];
    $cities = isset($_GET['city']) ? $_GET['city'] : [];
    $column = isset($_GET['column']) ? $_GET['column'] : 'Name';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $recordsPerPage = isset($_GET['recordsPerPage']) ? (int)$_GET['recordsPerPage'] : 10;
    $offset = ($page - 1) * $recordsPerPage;

    $query = "SELECT Name, Surname, Country, City, Salary FROM employees WHERE 1";

    $bindValues = [];

    if (!empty($countries)) {
        $countryPlaceholders = implode(',', array_map(function ($country) {
            return ":country_" . str_replace(' ', '_', $country);
        }, $countries));
        $query .= " AND Country IN ($countryPlaceholders)";

        foreach ($countries as $country) {
            $bindValues[":country_" . str_replace(' ', '_', $country)] = $country;
        }
    }
    
    if (!empty($cities)) {
        $citiesPlaceholders = implode(',', array_map(function ($city) {
            return ":city_" . str_replace(' ', '_', $city);
        }, $cities));
        $query .= " AND City IN ($citiesPlaceholders)";

        foreach ($cities as $city) {
            $bindValues[":city_" . str_replace(' ', '_', $city)] = $city;
        }
    }

    $query .= " ORDER BY $column $order";

    $query .= " LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    
    foreach ($bindValues as $param => $value) {
        $stmt->bindValue($param, $value, PDO::PARAM_STR);
    }

    $stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    foreach ($employees as $employee) {
        echo "<tr>";
        echo "<td>{$employee['Name']}</td>";
        echo "<td>{$employee['Surname']}</td>";
        echo "<td>{$employee['Country']}</td>";
        echo "<td>{$employee['City']}</td>";
        echo "<td>{$employee['Salary']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
