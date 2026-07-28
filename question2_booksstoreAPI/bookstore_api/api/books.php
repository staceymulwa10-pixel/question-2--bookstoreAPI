<?php

include("../db.php");

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if($method == "GET"){

   $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 5;

$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM books LIMIT $limit OFFSET $offset";

    $result = $conn->query($sql);

    $books = [];

    while($row = $result->fetch_assoc()){

        $books[] = $row;

    }

    echo json_encode($books);

}

elseif($method == "POST"){

    $title = trim($_POST['title']);
$author = trim($_POST['author']);
$price = trim($_POST['price']);
$stock = trim($_POST['stock']);

if($title=="" || $author=="" || $price=="" || $stock==""){

    http_response_code(400);

    echo json_encode([
        "message"=>"All fields are required."
    ]);

    exit;

}

    $stmt = $conn->prepare("INSERT INTO books(title,author,price,stock) VALUES(?,?,?,?)");

    $stmt->bind_param("ssdi",$title,$author,$price,$stock);

    if($stmt->execute()){

        http_response_code(201);

        echo json_encode(["message"=>"Book added successfully"]);

    }else{

        http_response_code(500);

        echo json_encode(["message"=>"Failed to add book"]);

    }

}

elseif($method == "PUT"){

    parse_str(file_get_contents("php://input"), $_PUT);

   $id = $_PUT['id'];

$title = trim($_PUT['title']);

$author = trim($_PUT['author']);

$price = trim($_PUT['price']);

$stock = trim($_PUT['stock']);

if($title=="" || $author=="" || $price=="" || $stock==""){

    http_response_code(400);

    echo json_encode([
        "message"=>"All fields are required."
    ]);

    exit;

}
    $check = $conn->prepare("SELECT id FROM books WHERE id=?");

$check->bind_param("i",$id);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0){

    http_response_code(404);

    echo json_encode([
        "message"=>"Book not found."
    ]);

    exit;

}

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, price=?, stock=? WHERE id=?");

    $stmt->bind_param("ssdii",$title,$author,$price,$stock,$id);

    if($stmt->execute()){

        echo json_encode(["message"=>"Book updated successfully"]);

    }else{

        http_response_code(500);

        echo json_encode(["message"=>"Update failed"]);

    }

}

elseif($method == "DELETE"){

    parse_str(file_get_contents("php://input"), $_DELETE);

    $id = $_DELETE['id'];
    $check = $conn->prepare("SELECT id FROM books WHERE id=?");

$check->bind_param("i",$id);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0){

    http_response_code(404);

    echo json_encode([
        "message"=>"Book not found."
    ]);

    exit;

}

    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");

    $stmt->bind_param("i",$id);

    if($stmt->execute()){

        echo json_encode(["message"=>"Book deleted successfully"]);

    }else{

        http_response_code(500);

        echo json_encode(["message"=>"Delete failed"]);

    }

}

else{

    http_response_code(405);

    echo json_encode(["message"=>"Method Not Allowed"]);

}

?>