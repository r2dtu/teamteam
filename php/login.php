<?php
session_start();
include 'error.php';

try{
  $conn = new PDO("mysql:host=localhost;dbname=thefeed", root, WTF110lecture);
}
catch(PDOException $e){
  error_out();
}

$username = $_POST['username'];
$password = $_POST['password'];
$message = $_POST["message"];

// BCrypt the password
$enc_pwd = password_hash($password);

$question = $_POST["security_question"];
$answer = $_POST["security_answer"];

if($message == "login"){
  try{
    $q_result = $conn->query("SELECT * FROM accounts WHERE username='$username'")->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $e){
    error_out();
  }
  if(!$username || !$password || $q_result["password"] != $enc_pwd){
    $q_result["can_login"] = "no";
    echo json_encode($q_result);
    exit();
  }

  $q_result["can_login"] = "yes";
  echo json_encode($q_result);
  $_SESSION["username"] = $username;

}else if($message == "create_account"){ //CASE: user is trying to create account
  $q_result = $conn->query("SELECT * FROM accounts WHERE username='$username'")->fetch(PDO::FETCH_ASSOC);

  if(isset($q_result["username"])){
    $q_result["can_create"] = "no";
    echo json_encode($q_result);
    exit();
  }

  $q_result["can_create"] = "yes";
  $statement = $conn->prepare("INSERT INTO accounts (username, password) VALUES ('$username', '$enc_pwd')")->execute();
  $statement = $conn->prepare("UPDATE accounts SET security_answer='$answer' WHERE username='$username'")->execute();
  $statement = $conn->prepare("UPDATE accounts SET s_question='$question' WHERE username='$username'")->execute();
  $_SESSION["username"] = $username;
  echo json_encode($q_result);

}else if($message == "forgot_password"){

  $q_result = $conn->query("SELECT * FROM accounts WHERE username='$username'")->fetch(PDO::FETCH_ASSOC);
  if($q_result["s_question"] == $question && $q_result["security_answer"] == $answer){
    $result["password"] = $q_result["password"];
    $result["display_password"] = "yes";
  }else{
    $result["display_password"] = "no";
  }
  echo json_encode($result);
}

?>
