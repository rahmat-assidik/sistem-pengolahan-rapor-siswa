<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "db_raporsiswa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check guru table columns
$sql = "SHOW COLUMNS FROM guru";
$result = $conn->query($sql);

echo "=== Guru Table Columns ===\n";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row["Field"] . " - " . $row["Type"] . " (Null: " . $row["Null"] . ")\n";
    }
} else {
    echo "No columns found\n";
}

echo "\n=== Migrations Status ===\n";
$sql = "SELECT migration FROM migrations WHERE migration LIKE '%guru%' OR migration LIKE '%email%' ORDER BY migration";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "✓ " . $row["migration"] . "\n";
    }
} else {
    echo "No migrations found\n";
}

$conn->close();
?>
