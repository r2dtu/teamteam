<?php
session_start();

/********* OPEN PDO DATABASE CONNECTION *********/
$conn = new PDO("mysql:host=localhost;dbname=thefeed", root, WTF110lecture);

$username = $_SESSION["username"];
$message = $_POST["message"];
$c_id = $_POST["c_id"];
$c_newname = $_POST["c_newname"];
$subs = $_POST["subs"];
$c_img = $_POST["c_img"];
$table = $_POST["table"];

//USER IS TRYING TO UPDATE C_NAME OR CREATE NEW CATEGORY
if($c_newname){
  $q_result = $conn->query("SELECT c_id FROM categories WHERE username='$username' AND c_name='$c_newname'")->fetcColumn();
  if($q_result){
    $result["can_update_or_create"] = "no";
    echo json_encode($result);
    exit();
  }
}

if($message == "create"){

    $c_id = uniqid();
    $statement = $conn->prepare("INSERT INTO categories (c_id, c_name, username, img) VALUES ('$c_id', '$c_newname', '$username', '$c_img')")->execute();

    if ($subs) {
      $result = $conn->prepare("DELETE FROM $table WHERE c_id='$c_id'")->execute();
      foreach ($subs as $sub_name => $sub_id) {
        $result = $conn->prepare("INSERT INTO $table (c_id, sub_name, sub_id) VALUES ('$c_id', '$sub_name', '$sub_id')")->execute();
      }
    }

    $result["can_create"] = "yes";
    echo json_encode($result);

}else if($message == "update"){

    if($c_newname){

      $statement = $conn->prepare("UPDATE categories SET c_name='$c_newname' WHERE c_id='$c_id'")->execute();
    }

    if($c_img){

      $statement = $conn->prepare("UPDATE categories SET img='$c_img' WHERE c_id='$c_id'")->execute();
    }

    if ($subs) {
      $result = $conn->prepare("DELETE FROM $table WHERE c_id='$c_id'")->execute();
      foreach ($subs as $sub_name => $sub_id) {
        $result = $conn->prepare("INSERT INTO $table (c_id, sub_name, sub_id) VALUES ('$c_id', '$sub_name', '$sub_id')")->execute();
      }
    }

    $result["can_update_or_create"] = "yes";
    echo json_encode($result);

} else if($message == "deleteCategory"){

    $q_result = $conn->prepare("DELETE FROM categories WHERE c_id='$c_id'")->execute();
    $q_result = $conn->prepare("DELETE FROM y-subs WHERE c_id='$c_id'")->execute();
    $q_result = $conn->prepare("DELETE FROM p_subs WHERE c_id='$c_id'")->execute();
    $q_result = $conn->prepare("DELETE FROM r_subs WHERE c_id='$c_id'")->execute();

}else if($message == "subsInCat"){

  $sub_names = $_POST['sub_names'];
  $table = $_POST['table'];

  $result = array();

  foreach($sub_names as $sub_name){

    $q_result = $conn->query("SELECT sub_id FROM '$table' WHERE sub_name='$sub_name'")->fetcColumn();
    if(isset($q_result)){
      array_push($result, $sub_name);
    }
  }
  echo json_encode($result);
}

?>
