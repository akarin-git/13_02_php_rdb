<?php
session_start();
include('functions.php');
// var_dump($_POST);
// exit();

$msg = "";
// var_dump($_SESSION['id']);
// exit();

if (
  !isset($_POST['name']) || $_POST['name'] == "" ||
  !isset($_POST['text']) || $_POST['text'] == "" ||
  !isset($_POST['category']) || $_POST['category'] == "" ||
  !isset($_FILES['image']) || $_FILES['image'] == ""

) {
  // exit('入力してください   ');

}


$pdo = connect_to_db();
if(isset($_POST['upload'])){
  $image = $_FILES['image']['name'];
  $title = $_POST['title'];
  $text = $_POST['text'];
  $category = $_POST['category'];
  
  $members = $pdo->prepare('SELECT * FROM user WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
  $member_id = $member['id'];
  $member_name = $member['name'];
  // var_dump($member_id);
  // exit();
$target = "images/" . basename($_FILES['image']['name']);


$sql = 'INSERT INTO post_table(id,user_id,user_name,title,text,image,category,created_at,updated_at) VALUES (NULL,:user_id,:user_name,:title,:text,:image,:category,sysdate(),sysdate())';

if(move_uploaded_file($_FILES['image']['tmp_name'],$target)){
  $msg = "Image uploaded successfully";
} else {
  $msg = "There was a problem ";
}

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':user_id', $member_id, PDO::PARAM_INT);
$stmt->bindValue(':user_name', $member_name, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':text', $text, PDO::PARAM_STR);
$stmt->bindValue(':image', $image, PDO::PARAM_STR);
$stmt->bindValue(':category', $category, PDO::PARAM_STR);
$status = $stmt->execute();
// var_dump($status);
// exit();
if($status == false) {
  $error = $stmt->errorInfo();
  exit('sqlError:' . $error[2]);
} else {
  
  header('location:content.php');
}
exit();
}