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

    if(!isset($_GET['userId']) || empty($_GET['userId'])){
        validate(false, 'Please Login to view contacts');
    }

    $userId = htmlspecialchars($_GET['userId']);

    $database = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn = $database->getConn();

    if(!$conn){
        validate(false, 'Database Connection Failed (readCon)');
    }

    try {

        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

        if(!empty($searchTerm)){
            $sql = "SELECT * FROM contacts WHERE userId = :userId AND (firstName LIKE :search OR lastName LIKE :search
                    OR email LIKE :search OR phoneNumber LIKE :search OR address LIKE :search) ORDER BY firstName, lastName";

            $stmt = $conn->prepare($sql);
            $searchParam = "%{$searchTerm}%";
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM contacts WHERE userId = :userId ORDER BY firstName, lastName";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
        }

        $contacts = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $contacts[] = $row;
        }

        echo json_encode($contacts);

    } catch (PDOException $error){
        validate(false, 'Error: ' . $error->getMessage());

    } finally {
        $database->closeConn();
    }
?>