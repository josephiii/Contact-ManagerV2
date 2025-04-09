<?php

    require_once('../database.php');
    require_once('../dbConfig.php');
    header('Content-Type: application/json');

    function validate($success, $errorMessage){
        $response = array(
            'success' => $success,
            'message' => $errorMessage);
        echo json_encode($response);
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        validate(false, 'Invalid Request Method (createCon)');
    }

    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if($data == null){
        validate(false, 'Json data error (createCon)');
    }

    if(empty($data['firstName']) || empty($data['lastName'])){
        validate(false, 'First and Last name required');
    }

    if(empty($data['userId'])){
        validate(false, 'Please Login to create contacts');
    }

    $firstName = trim($data['firstName']);
    $lastName = trim($data['lastName']);
    $phoneNumber = isset($data['phoneNumber']) ? trim($data['phoneNumber']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $address = isset($data['address']) ? trim($data['address']) : '';
    $userId = htmlspecialchars(trim($data['userId']));
    
    $db = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn = $db->getConn();

    if(!$conn){
        validate(false, 'Database Connection Failed (createCon)');
    }
    
    try{
        $stmt = $conn->prepare("INSERT INTO contacts (firstName, lastName, phoneNumber, email, address, userId) VALUES (:firstName, :lastName, :phoneNumber, :email, :address, :userId)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':userId', $userId);
        
        $stmt->execute();
        $id = $conn->lastInsertId();

    } catch(PDOException $error){
        validate(false, 'Database Error:' . $error->getMessage());
        
    } finally{

        $contact = array(
            'id' => $id,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'address' => $address,
            'userId' => $userId
        );
    
        echo json_encode($contact);
        $db->closeConn();
        exit;

    }
?>