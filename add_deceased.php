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


$death_certificate_path = NULL;
if (!empty($_FILES['death_certificate']['name'])) {
    $upload_dir = "death_certificates/";
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            header("Location: index.php?status=uploadFailed");
            exit();
        }
    }

    $file_name = basename($_FILES['death_certificate']['name']);
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_types = array('pdf', 'jpg', 'jpeg', 'png');

    // Validate file type
    if (!in_array($file_type, $allowed_types)) {
        header("Location: index.php?status=invalidFileType");
        exit();
    }

    // Validate file size (example: 5MB max)
    if ($_FILES['death_certificate']['size'] > 5000000) {
        header("Location: index.php?status=uploadFailed");
        exit();
    }

    // Generate a unique file name
    $target_file = $upload_dir . uniqid() . "_" . $file_name;

    if (move_uploaded_file($_FILES['death_certificate']['tmp_name'], $target_file)) {
        $death_certificate_path = $conn->real_escape_string($target_file);
    } else {
        header("Location: index.php?status=uploadFailed");
        exit();
    }
}




$sql = "INSERT INTO deceased (first_name, last_name, birth_date, death_date, obituary, grave_id, death_certificate) 
        VALUES ('$first_name', '$last_name', '$birth_date', '$death_date', '$obituary', '$grave_id', '$death_certificate_path')";

if ($conn->query($sql) === TRUE) {
    $update_grave = "UPDATE graves SET status = 'Taken' WHERE grave_id = '$grave_id'";
    $conn->query($update_grave);

    header("Location: index.php?status=addDeceasedSuccess");
    exit();
}

header("Location: index.php?status=addDeceasedFailed");
exit();
?>