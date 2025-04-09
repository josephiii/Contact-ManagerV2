<?php

    require_once('../database.php');
    require_once('../dbConfig.php');
    header('Content-Type: application/json');

    function validate($success, $errorMessage){
        $response = array(
            'success' => $success,
            'message' => $errorMessage
        );
        echo json_encode($response);
        exit;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if(!isset($data['userId']) || empty($data['userId'])){
        validate(false, 'Please Login to view contacts');
    }

    if(!isset($data['id']) || empty($data['id'])){
        validate(false, 'Database Error: Contact Id is required');
    }

    if($data == null){
        validate(false, 'Json data error (updateCon)');
    }

    if($data['firstName'] == null || $data['lastName'] == null){
        validate(false, 'First and Last names are required');
    }

    $database = New Database($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn = $database->getConn();

    if(!$conn){
        validate(false, 'Database Connection Failed (updateCon)');
    }

    $firstName = trim($data['firstName']);
    $lastName = trim($data['lastName']);
    $phoneNumber = isset($data['phoneNumber']) ? trim($data['phoneNumber']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $address = isset($data['address']) ? trim($data['address']) : '';
    $contactId = $data['id'];
    $userId = $data['userId'];

    try{

        $sql = "UPDATE contacts SET firstName = :firstName, lastName = :lastName, phoneNumber = :phoneNumber, email = :email, address = :address 
                WHERE id = :contactId AND userId = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':contactId', $contactId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        if($stmt->rowCount() > 0){

            $response = array(
                'success' => true,
                'message' => 'Contact updated successfully',
                'id' => $contactId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phoneNumber' => $phoneNumber,
                'email' => $email,
                'address' => $address,
                'userId' => $userId
            );
            echo json_encode($response);
            exit;

        } else {
            validate(false, 'No changes were made');
        }
        
    } catch(PDOException $error){
        validate(false, 'Error: ' .  $error->getMessage());

    } finally{
        $database->closeConn();
    }
?>