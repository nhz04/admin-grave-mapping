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
?>
