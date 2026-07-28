<?php

include("../db.php");

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
if($method=="GET"){

    $sql="SELECT * FROM customers";

    $result=$conn->query($sql);

    $customers=[];

    while($row=$result->fetch_assoc()){

        $customers[]=$row;

    }

    echo json_encode($customers);

}
elseif($method=="POST"){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if($name=="" || $email==""){

        http_response_code(400);

        echo json_encode([
            "message"=>"All fields are required."
        ]);

        exit;

    }

    $stmt = $conn->prepare("INSERT INTO customers(name,email) VALUES(?,?)");

    $stmt->bind_param("ss",$name,$email);

    if($stmt->execute()){

        http_response_code(201);

        echo json_encode([
            "message"=>"Customer added successfully."
        ]);

    }else{

        http_response_code(500);

        echo json_encode([
            "message"=>"Failed to add customer."
        ]);

    }

}
elseif($method=="PUT"){

    parse_str(file_get_contents("php://input"),$_PUT);

    $id=$_PUT['id'];

    $name=trim($_PUT['name']);

    $email=trim($_PUT['email']);

    if($name=="" || $email==""){

        http_response_code(400);

        echo json_encode([
            "message"=>"All fields are required."
        ]);

        exit;

    }

    $check=$conn->prepare("SELECT id FROM customers WHERE id=?");

    $check->bind_param("i",$id);

    $check->execute();

    $result=$check->get_result();

    if($result->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Customer not found."
        ]);

        exit;

    }

    $stmt=$conn->prepare("UPDATE customers SET name=?, email=? WHERE id=?");

    $stmt->bind_param("ssi",$name,$email,$id);

    if($stmt->execute()){

        http_response_code(200);

        echo json_encode([
            "message"=>"Customer updated successfully."
        ]);

    }else{

        http_response_code(500);

        echo json_encode([
            "message"=>"Update failed."
        ]);

    }

}
elseif($method=="DELETE"){

    parse_str(file_get_contents("php://input"),$_DELETE);

    $id=$_DELETE['id'];

    $check=$conn->prepare("SELECT id FROM customers WHERE id=?");

    $check->bind_param("i",$id);

    $check->execute();

    $result=$check->get_result();

    if($result->num_rows==0){

        http_response_code(404);

        echo json_encode([
            "message"=>"Customer not found."
        ]);

        exit;

    }

    $stmt=$conn->prepare("DELETE FROM customers WHERE id=?");

    $stmt->bind_param("i",$id);

    if($stmt->execute()){

        http_response_code(200);

        echo json_encode([
            "message"=>"Customer deleted successfully."
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