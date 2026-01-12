<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'mysql';
$db   = 'studenti';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Conexiune eșuată']);
    exit();
}

$metoda = $_SERVER['REQUEST_METHOD'];

if ($metoda === 'GET') {
    $stmt = $pdo->query("SELECT * FROM studenti ORDER BY id DESC");
    $studenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($studenti);
}

elseif ($metoda === 'POST') {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data['nume'], $data['an'], $data['media'])) {
        $sql = "INSERT INTO studenti (nume, an, media) VALUES (:nume, :an, :media)";
        $stmt = $pdo->prepare($sql);
        
        $result = $stmt->execute([
            ':nume' => $data['nume'],
            ':an' => $data['an'],
            ':media' => $data['media']
        ]);

        if ($result) {
            echo json_encode(["message" => "Student adăugat cu succes!", "success" => true]);
        } else {
            echo json_encode(["message" => "Eroare la adăugare", "success" => false]);
        }
    } else {
        echo json_encode(["message" => "Date incomplete", "success" => false]);
    }
}
?>