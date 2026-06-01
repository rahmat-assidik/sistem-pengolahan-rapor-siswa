<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "db_raporsiswa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update email dengan format guru_{nip}@sekolah.local
$sql = "UPDATE guru SET email = CONCAT('guru_', nip, '@sekolah.local') WHERE email = '' OR email IS NULL";
$result = $conn->query($sql);

if ($result) {
    echo "Email berhasil diperbarui untuk " . $conn->affected_rows . " guru\n";
} else {
    echo "Error: " . $conn->error;
}

// Show updated data
echo "\n=== Data Guru Updated ===\n";
$sql = "SELECT nip, nama_guru, email FROM guru LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row["nip"] . " | " . $row["nama_guru"] . " | " . $row["email"] . "\n";
    }
}

$conn->close();
?>
