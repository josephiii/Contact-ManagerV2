<?php
   
    require_once '../database.php';
    header('Content-Type: application/json');

    function validate($success, $errorMessage){
        $response = array(
            'success' => $success,
            'message' => $errorMessage );
        echo json_encode($response); 
        exit;         
    }

    $json_data = file_get_contents('php://input'); // grabs userInfo json
    $data = json_decode($json_data, true); // json -> array (so we can use it)

    // ensures data is allowed
    if(!$data){
        validate(false, 'Invalid Data');
    }

    if(empty($data['firstName']) || empty($data['username']) || empty($data['email']) || empty($data['password'])){
        validate(false, 'All fields are required');
    }

    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
        validate(false, 'Invalid Email');
    }

    // DB Connection 
    $dbHost = 'localhost';
    $dbName = 'reach';
    $dbUsername = 'user';
    $dbPassword = 'pwd';

    try{

        // creates DB obj and makes connection
        $database = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
        $conn = $database->getConn();

        // queries for existing username/ email
        $checkUsername = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $conn->prepare($checkUsername);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        if($stmt->fetchColumn() > 0){
            $database->closeConn();
            validate(false, 'Username already exists');
        }

        $checkEmail = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $conn->prepare($checkEmail);
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();

        if($stmt->fetchColumn() > 0){
            $database->closeConn();
            validate(false, 'Email already registered');
        }

        // insert new user
        $insertUser = "INSERT INTO users (firstName, username, email, password) 
                       VALUES (:firstName, :username, :email, :password)";
        
        $stmt = $conn->prepare($insertUser);
        $stmt->bindParam(':firstName', $data['firstName']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        
        if($stmt->execute()){
            $database->closeConn();
            validate(true, 'Registration Successful');
        } else {
            $database->closeConn();
            validate(false, 'Registration Failed');
        }

    } catch(PDOException $e){

        // ensures we close connections to DB
        if(isset($database)){
            $database->closeConn();
        }
        validate(false, 'Database Error: ' . $e->getMessage());

    }
?>