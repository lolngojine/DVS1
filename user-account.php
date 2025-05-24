

<?php
require_once 'dbConnect.php';

session_start();
$errors=[];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])){
    $email=filter_input(INPUT_POST, 'email',FILTER_SANITIZE_EMAIL);
    $name=$_POST['name'];    
    $password=$_POST['password'];    
    $confirm_password=$_POST['confirm_password'];   
    $created_at=date('Y-m-d H:i:s') ;



if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email']='Invalid email format';
}


//API
$api_key="e698cd4e0aaf45d6b2be7659514ff7a6";
$ch= curl_init();
curl_setopt_array($ch,[
    CURLOPT_URL=>"https://emailvalidation.abstractapi.com/v1/?api_key=$api_key&email=$email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION =>true
]);
$response = curl_exec($ch);
curl_close($ch);
$data=json_decode($response,true);

if($data['deliverability']==="UNDELIVERABLE"){
    exit("API not recognize this email");
}
if($data["is_disposable_email"]["value"]===true){
    exit("Thanks for submitting right email");
}


//end api


if(empty($name)){
    $errors['name']='Name cannot be empty';
}
if(strlen($password)< 4){
    $errors['password']='password must be aleast 4 characters';
}
if($password !==$confirm_password){
    $errors['confirm_password']='Password do not match';
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
if($stmt->fetch()){
    $errors['user_exist']='Email is alredy registered';
}

if(!empty($errors)){
    $_SESSION['errors']=$errors;
    header('location: register.php');
    exit();
}

$hashedPassword=password_hash($password, PASSWORD_BCRYPT);
$stmt =$pdo->prepare('INSERT INTO users (email,password,name,created_at) VALUES(:email,:password,:name, :created_at)');

//$stmt->execute(['email' => $email, 'password' => $hashedPassword, 'name'=>$name,'created_at'=>$created_at]);

//header('Location: index.php');
//exit();



//ile ya kwanza iko commented inawork but below code display error of success registration
try {
    $stmt->execute([
        'email' => $email,
        'password' => $hashedPassword,
        'name' => $name,
        'created_at' => $created_at
    ]);
    echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='register.php';</script>";
}
exit();


}




if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['signin'])){
    $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
    $password= $_POST['password'];

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']='Invalid email format';
    }



    
    if(empty($password)){
        $errors['password']='Password is security key';
    }
    if(!empty($errors)){
        $_SESSION['errors']=$errors;
        header('location:index.php');
        exit();
    }
    $stmt=$pdo->prepare("SELECT * FROM users WHERE email= :email");  // hapa we are creating login ndio ifanye verification from the database
    $stmt->execute(['email'=>$email]);
    $user=$stmt->fetch();
//hapa kama details matches anapewa access
    if($user && password_verify($password,$user['password'])){
        $_SESSION['user']=[
            'id'=>$user['id'],
            'email'=>$user['email'],
            'name'=>$user['name'],
            'created_at'=>$user['created_at']

        ];
        
        header('Location:home.php');
        exit();
    }
    else{   //kama user detals is not in database dispaly below error
        $errors['login']='Wrong password or email';
        $_SESSION['errors']=$errors;
        header('Location:index.php');
        exit();
    }
}
?>