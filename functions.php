<?php
include 'db.php';


function fetchGraves($conn) {
    $sql = "SELECT g.grave_id, g.section, g.block_number, g.lot_number, g.status, 
                   d.first_name, d.last_name
            FROM graves g
            LEFT JOIN deceased d ON g.grave_id = d.grave_id
            ORDER BY g.grave_id ASC";  
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}


function fetchDeceased($conn) {
    $sql = "SELECT d.deceased_id, d.first_name, d.last_name, d.birth_date, d.death_date, d.obituary, 
                   g.grave_id, g.section, g.block_number, g.lot_number, g.status
            FROM deceased d
            LEFT JOIN graves g ON d.grave_id = g.grave_id
            ORDER BY g.grave_id ASC";
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}


function getTotalGraves($conn) {
    $sql = "SELECT COUNT(*) AS total_graves FROM graves";
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_assoc()['total_graves'] : 0;
}


function getTotalDeceased($conn) {
    $sql = "SELECT COUNT(*) AS total_deceased FROM deceased";
    $result = $conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_assoc()['total_deceased'] : 0;
}

function addDeceased($conn, $postData, $fileData) {
    if (!isset($postData['add_deceased'])) {
        return "invalidRequest";
    }
    $first_name = $conn->real_escape_string($postData['first_name']);
    $last_name = $conn->real_escape_string($postData['last_name']);
    $birth_date = $conn->real_escape_string($postData['birth_date']);
    $death_date = $conn->real_escape_string($postData['death_date']);
    $obituary = $conn->real_escape_string($postData['obituary']);
    $section = $conn->real_escape_string($postData['section']);
    $block_number = $conn->real_escape_string($postData['block_number']);
    $lot_number = $conn->real_escape_string($postData['lot_number']);

   
    $grave_query = "SELECT grave_id, status FROM graves WHERE section = '$section' AND block_number = '$block_number' AND lot_number = '$lot_number'";
    $result = $conn->query($grave_query);

    if ($result->num_rows === 0) {
        return "graveNotExist";
    }

    $row = $result->fetch_assoc();
    $grave_id = $row['grave_id'];
    $grave_status = $row['status'];

    if ($grave_status === 'Taken') {
        return "addDeceasedFailed";
    }

    $death_certificate_path = NULL;
    if (!empty($fileData['death_certificate']['name'])) {
        $upload_dir = "death_certificates/";
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
            return "uploadFailed";
        }
        $file_name = basename($fileData['death_certificate']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = array('pdf', 'jpg', 'jpeg', 'png');

        if (!in_array($file_type, $allowed_types) || $fileData['death_certificate']['size'] > 5000000) {
            return "invalidFileType";
        }
        $target_file = $upload_dir . uniqid() . "_" . $file_name;
        if (move_uploaded_file($fileData['death_certificate']['tmp_name'], $target_file)) {
            $death_certificate_path = $conn->real_escape_string($target_file);
        } else {
            return "uploadFailed";
        }
    }

    $sql = "INSERT INTO deceased (first_name, last_name, birth_date, death_date, obituary, grave_id, death_certificate) 
            VALUES ('$first_name', '$last_name', '$birth_date', '$death_date', '$obituary', '$grave_id', '$death_certificate_path')";

    if ($conn->query($sql) === TRUE) {
        $update_grave = "UPDATE graves SET status = 'Taken' WHERE grave_id = '$grave_id'";
        $conn->query($update_grave);
        return "addDeceasedSuccess";
    }

    return "addDeceasedFailed";
}



function addGrave($conn, $section, $block_number, $lot_number) {
  
    $section = $conn->real_escape_string($section);
    $block_number = $conn->real_escape_string($block_number);
    $lot_number = $conn->real_escape_string($lot_number);

  
    $check_sql = "SELECT * FROM graves WHERE section = '$section' AND block_number = '$block_number' AND lot_number = '$lot_number'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        return "addGraveFailed"; 
    } 

    $sql = "INSERT INTO graves (section, block_number, lot_number, status) 
            VALUES ('$section', '$block_number', '$lot_number', 'available')";

    return ($conn->query($sql) === TRUE) ? "addGraveSuccess" : "addGraveFailed";
}

function editGrave($conn) {
    
    if (isset($_POST['update_grave'])) {

      
        $id = $_POST['grave_id']; 
        $section = $_POST['section'];
        $block_number = $_POST['block_number'];
        $lot_number = $_POST['lot_number'];
        $status = $_POST['status'];

        $id = mysqli_real_escape_string($conn, $id);
        $section = mysqli_real_escape_string($conn, $section);
        $block_number = mysqli_real_escape_string($conn, $block_number);
        $lot_number = mysqli_real_escape_string($conn, $lot_number);
        $status = mysqli_real_escape_string($conn, $status);

       
        $sql = "UPDATE graves SET 
                    section = '$section', 
                    block_number = '$block_number', 
                    lot_number = '$lot_number', 
                    status = '$status' 
                WHERE grave_id = '$id'";

    
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?status=editGraveSuccess");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}












?>
