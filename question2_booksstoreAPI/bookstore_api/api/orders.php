<?php

include("../db.php");

header("Content-Type: application/json");

$method=$_SERVER['REQUEST_METHOD'];
if($method=="GET"){

$sql="SELECT
orders.id,
customers.name AS customer,
books.title AS book,
orders.quantity,
orders.order_date

FROM orders

INNER JOIN customers
ON orders.customer_id=customers.id

INNER JOIN books
ON orders.book_id=books.id";

$result=$conn->query($sql);

$orders=[];

while($row=$result->fetch_assoc()){

$orders[]=$row;

}

echo json_encode($orders);

}
elseif($method=="POST"){

$customer_id=$_POST['customer_id'];

$book_id=$_POST['book_id'];

$quantity=$_POST['quantity'];

if($customer_id=="" || $book_id=="" || $quantity==""){

http_response_code(400);

echo json_encode([
"message"=>"All fields are required."
]);

exit;

}

$stmt=$conn->prepare("INSERT INTO orders(customer_id,book_id,quantity) VALUES(?,?,?)");

$stmt->bind_param("iii",$customer_id,$book_id,$quantity);

if($stmt->execute()){

http_response_code(201);

echo json_encode([
"message"=>"Order created successfully."
]);

}else{

http_response_code(500);

echo json_encode([
"message"=>"Failed to create order."
]);

}

}
elseif($method=="PUT"){

    parse_str(file_get_contents("php://input"), $_PUT);

    $id = $_PUT['id'];
    $customer_id = $_PUT['customer_id'];
    $book_id = $_PUT['book_id'];
    $quantity = $_PUT['quantity'];

    if($customer_id=="" || $book_id=="" || $quantity==""){

        http_response_code(400);

        echo json_encode([
            "message"=>"All fields are required."
        ]);

        exit;

    }

    // Check if the order exists
    $check = $conn->prepare("SELECT id FROM orders WHERE id=?");
    $check->bind_param("i",$id);
    $check->execute();

    if($check->get_result()->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Order not found."
        ]);

        exit;

    }

    // Check customer exists
    $customer = $conn->prepare("SELECT id FROM customers WHERE id=?");
    $customer->bind_param("i",$customer_id);
    $customer->execute();

    if($customer->get_result()->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Customer not found."
        ]);

        exit;

    }

    // Check book exists
    $book = $conn->prepare("SELECT id FROM books WHERE id=?");
    $book->bind_param("i",$book_id);
    $book->execute();

    if($book->get_result()->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Book not found."
        ]);

        exit;

    }

    $stmt = $conn->prepare("UPDATE orders SET customer_id=?, book_id=?, quantity=? WHERE id=?");

    $stmt->bind_param("iiii",$customer_id,$book_id,$quantity,$id);

    if($stmt->execute()){

        http_response_code(200);

        echo json_encode([
            "message"=>"Order updated successfully."
        ]);

    }else{

        http_response_code(500);

        echo json_encode([
            "message"=>"Update failed."
        ]);

    }

}
elseif($method=="DELETE"){

    parse_str(file_get_contents("php://input"), $_DELETE);

    $id = $_DELETE['id'];

    $check = $conn->prepare("SELECT id FROM orders WHERE id=?");

    $check->bind_param("i",$id);

    $check->execute();

    if($check->get_result()->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Order not found."
        ]);

        exit;

    }

    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");

    $stmt->bind_param("i",$id);

    if($stmt->execute()){

        http_response_code(200);

        echo json_encode([
            "message"=>"Order deleted successfully."
        ]);

    }else{

        http_response_code(500);

        echo json_encode([
            "message"=>"Delete failed."
        ]);

    }

}
else{

    http_response_code(405);

    echo json_encode([
        "message"=>"Method Not Allowed."
    ]);

}
?>