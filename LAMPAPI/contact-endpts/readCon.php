<?php

    require_once('../database.php');
    header('Content-Type: application/json');

    function validate($success, $errorMessage){
        $response = array(
            'success' => $success,
            'message' => $errorMessage);
        echo json_encode($response);
        exit;
    }

    $dbHost = 'localhost';
    $dbName = 'reach';
    $dbUsername = 'user';
    $dbPassword = 'pwd';

    $database = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
    $conn = $database->getConn();

    if(!$conn){
        validate(false, 'Database Connection Failed (readCon)');
    }

    try {

        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

        if(!empty($searchTerm)){
            $sql = "SELECT * FROM contacts WHERE firstName LIKE :search OR lastName LIKE :search
                    OR email LIKE :search OR phoneNumber LIKE :search OR address LIKE :search";

            $stmt = $conn->prepare($sql);
            $searchParam = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM contacts ORDER BY firstName, lastName";
            $stmt = $conn->prepare($sql);
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