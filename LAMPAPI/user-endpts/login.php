<?PHP
    
    require_once '../database.php';
    require_once('../dbConfig.php');
    header('Content-Type: application/json');

    function validate($success, $errorMessage, $userId = null, $username = null){
        $response = array(
            'success' => $success,
            'message' => $errorMessage);

        if($userId !== null){
            $response['userId'] = $userId;        
        }

        if($username !== null){
            $response['username'] = $username;
        }

        echo json_encode($response);
        exit;
    } 

    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        validate(false, 'Invalid Request Method (login)');
    }

    // grabs userInfo from POST
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // validate and sanitize data
    if(!$data){
        validate(false, 'Invalid Data');
    }

    if(empty($data['username']) || empty($data['password'])){
        validate(false, 'All fields are required');
    }

    $username = htmlspecialchars($data['username']);
    $password = $data['password']; // add password hashing with DB

    
    try{

        $database = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
        $conn = $database->getConn();

        $checkUsername = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $conn->prepare($checkUsername);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // grabs user info if user/email is found

        if($user){
            if($password == $user['password']){ // IMPLEMENT HASHING FOR DB!!!!!!!
                $database->closeConn();
                validate(true, 'Login Successful', $user['userId'], $user['username']);

            } else {
                $database->closeConn();
                validate(false, 'Invalid Username or Password');
            }

        } else {
            $database->closeConn();
            validate(false, 'Invalid Username or Password');
        }

    } catch(PDOException $e){

        if(isset($database)){
            $database->closeConn();
        }
        validate(false, 'An error has occurred. Please try again later.');
    }
?>