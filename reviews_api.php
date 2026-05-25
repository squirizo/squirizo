<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Koneksyon sa MySQL Database ng XAMPP mo
$host = "localhost"; // Mas mainam na "localhost" ang gamitin natin kesa 127.0.0.1
$user = "root";
$pass = "";
$dbname = "project"; // Ang pangalan ng DB mo sa phpMyAdmin

$conn = new mysqli($host, $user, $pass, $dbname);

// INAYOS NA ERROR HANDLER (json_encode at may details na)
if ($conn->connect_error) {
    echo json_encode([
        "error" => "Database Connection Failed",
        "details" => $conn->connect_error
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// 1. KAPAG NAG-GET (KUKUNIN ANG MGA REVIEWS)
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM reviews ORDER BY id DESC");
    $reviews = [];
    
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    echo json_encode($reviews);
}

// 2. KAPAG NAG-POST (MAGSESEGURONG MAG-SAVE NG BAGONG REVIEW)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['name']) && !empty($data['rating']) && !empty($data['text'])) {
        $name = $conn->real_escape_string($data['name']);
        $rating = $conn->real_escape_string($data['rating']);
        $text = $conn->real_escape_string($data['text']);
        $image = $conn->real_escape_string($data['image']); // base64 string format
        
        $sql = "INSERT INTO reviews (name, rating, text, image) VALUES ('$name', '$rating', '$text', '$image')";
        
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Incomplete fields"]);
    }
}

$conn->close();
?>