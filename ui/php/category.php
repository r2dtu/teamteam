<?php
session_start();

include 'error.php';

/********* OPEN PDO DATABASE CONNECTION *********/
try{
  $conn = new PDO("mysql:host=localhost;dbname=thefeed", root, WTF110lecture);
}
catch(PDOException $e){
  error_out();
}

$c_name = $_POST['c_name'];
$c_img = $_POST['c_img'];
$username = $_SESSION["username"];

if($_POST['message'] == "create_category"){

  try {

    $q_result = $conn->query("SELECT * FROM categories WHERE username='$username' AND c_name='$c_name'")->fetch(PDO::FETCH_ASSOC);

    if(isset($q_result["category_name"])){
      $q_result["can_create"] = "no";
      echo json_encode($q_result);
      exit();
    }
    $c_id = uniqid();
    $statement = $conn->prepare("INSERT INTO categories (c_id, c_name, username, img) VALUES ('$c_id', '$c_name', '$username', '$c_img')")->execute();
    $q_result["can_create"] = "yes";
    echo json_encode($q_result);
    exit();

  }
  catch(PDOException $e){
    error_out();
  }

}else if($_POST['message'] == "update_img"){

  try{

    $statement = $conn->prepare("UPDATE categories SET img='$c_img' WHERE username='$username' AND c_name='$c_name'")->execute();

  }
  catch(PDOException $e){
    error_out();
  }

}else if($_POST['message'] == "update_name"){

  try{

    $new_name = $_POST['new_name'];

    $q_result = $conn->query("SELECT * FROM categories WHERE username='$username' AND c_name='$new_name'")->fetch(PDO::FETCH_ASSOC);

    if(isset($q_result["category_name"])){
      $q_result["can_update"] = "no";
      echo json_encode($q_result);
      exit();
    }

    $statement = $conn->prepare("UPDATE categories SET category_name='$new_name' WHERE username='$username' AND category_name='$category_name'")->execute();

    $q_result["can_update"] = "yes";
    echo json_encode($q_result);
    exit();

  }
  catch(PDOException $e){
    error_out();
  }
}
echo uniqid();

?>
