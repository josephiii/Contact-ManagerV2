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

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if(!isset($data['userId']) || empty($data['userId'])){
        validate(false, 'Please Login to view contacts');
    }

    if(!isset($data['id']) || empty($data['id'])){
        validate(false, 'Database Error: Contact Id is required');
    }

    $contactId = $data['id'];
    $userId = $data['userId'];

    $database = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn = $database->getConn();

    if(!$conn){
        validate(false, 'Database Connection Failed (delCon)');
    }

    try{

        $sql = "DELETE FROM contacts WHERE id = :contactId AND userId = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':contactId', $contactId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            validate(true, 'Contact Deleted Successfully');
        } else {
            validate(false, 'Contact could not be deleted');
        }

    } catch(PDOException $error){
        validate(false, 'Error: ' . $error->getMessage());

    } finally{
        $database->closeConn();
    } 
?>