<?php
include 'db.php';

if (!isset($_POST['add_deceased'])) {
    exit();
}

$first_name = $conn->real_escape_string($_POST['first_name']);
$last_name = $conn->real_escape_string($_POST['last_name']);
$birth_date = $conn->real_escape_string($_POST['birth_date']);
$death_date = $conn->real_escape_string($_POST['death_date']);
$obituary = $conn->real_escape_string($_POST['obituary']);
$section = $conn->real_escape_string($_POST['section']);
$block_number = $conn->real_escape_string($_POST['block_number']);
$lot_number = $conn->real_escape_string($_POST['lot_number']);

// Check if the grave exists and get its status
$grave_query = "SELECT grave_id, status FROM graves WHERE section = '$section' AND block_number = '$block_number' AND lot_number = '$lot_number'";
$result = $conn->query($grave_query);

// If grave doesn't exist
if ($result->num_rows === 0) {
    header("Location: index.php?status=graveNotExist");
    exit();
}

$row = $result->fetch_assoc();
$grave_id = $row['grave_id'];
$grave_status = $row['status'];


if ($grave_status === 'Taken') {
    header("Location: index.php?status=addDeceasedFailed");
    exit();
}


$sql = "INSERT INTO deceased (first_name, last_name, birth_date, death_date, obituary, grave_id) 
        VALUES ('$first_name', '$last_name', '$birth_date', '$death_date', '$obituary', '$grave_id')";

if ($conn->query($sql) === TRUE) {

    $update_grave = "UPDATE graves SET status = 'Taken' WHERE grave_id = '$grave_id'";
    $conn->query($update_grave);

    header("Location: index.php?status=addDeceasedSuccess");
    exit();
}


header("Location: index.php?status=addDeceasedFailed");
exit();
?>
