<?php
include 'db.php';

if (isset($_POST['add_grave'])) {
    $section = $conn->real_escape_string($_POST['section']);
    $block_number = $conn->real_escape_string($_POST['block_number']);
    $lot_number = $conn->real_escape_string($_POST['lot_number']);

    // Check if the grave already exists
    $check_sql = "SELECT * FROM graves WHERE section = '$section' AND block_number = '$block_number' AND lot_number = '$lot_number'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        header("Location: index.php?status=addGraveFailed");
            exit();
    } else {
        // Insert the new grave into the database
        $sql = "INSERT INTO graves (section, block_number, lot_number, status) 
                VALUES ('$section', '$block_number', '$lot_number', 'available')";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?status=addGraveSuccess");  
            exit();
        } else {
            header("Location: index.php?status=addGraveFailed");
            exit();
        }
    }
}
?>

