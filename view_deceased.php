<?php
require 'db.php'; // Ensure this file exists and correctly sets up $conn

header("Content-Type: application/json"); // Ensure JSON response

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$id = intval($_GET['id']); // Convert to an integer for security

// Prepare the SQL statement
$stmt = $conn->prepare("
    SELECT d.deceased_id, d.first_name, d.last_name, d.birth_date, d.death_date, d.obituary, 
           CONCAT(g.section, ' - Block ', g.block_number, ', Lot ', g.lot_number) AS grave_location, 
           d.death_certificate 
    FROM deceased d
    LEFT JOIN graves g ON d.grave_id = g.grave_id
    WHERE d.deceased_id = ?");


if (!$stmt) {
    echo json_encode(["error" => "Database error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row, JSON_PRETTY_PRINT); // Return the data as JSON
} else {
    echo json_encode(["error" => "No record found"]);
}

// Close resources
$stmt->close();
$conn->close();
?>
