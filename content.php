<?php
session_start();
include('functions.php');
// var_dump($_SESSION['id']);
// exit();
$pdo = connect_to_db();

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $pdo->prepare('SELECT * FROM user WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('location:user/login.php');
  exit();
}

$sql = 'SELECT * FROM post_table ORDER BY id DESC LIMIT 6';

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();
// var_dump($status);
// exit();

if ($status == false) {
  $error = $stmt->errorInfo();
  exit('sqlError:' . $error[2]);
} else {
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="content.css">
</head>

<body>

  <p><a href="main.php">戻る</a></p>

  <?php echo $member['name'] ?>
  <section class="container">
    <div class="output_box">
      <?php foreach ($result as $record) : ?>
        <div class="output">
          <p><?php echo $member['name'] ?></p>
          <img src="images/<?php echo "{$record['image']}"; ?>" alt="">
          <h1><?php echo $record['title'] ?></h1>
          <div class="output_text">
            <p><?php echo $record['text'] ?></p>
          </div>
          <div class="output_date">
            <span>
              <p><?php echo $record['created_at'] ?></p>

              <a href="delete.php?id=<?php echo $record['id'] ?>">delete</a>
              <a href="edit.php?id=<?php echo $record['id'] ?>">edit</a>

            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>


    <div class="input_form">
      <form action="content_create.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="size" value="1000000">

        <div class="input">
          <ul>
            <li>
              title: <input type="text" name="title">
            </li>
            <li>
              text: <textarea type="text" rows="5" cols="35" name="text" placeholder="説明を書いてください"></textarea>
            </li>
            <li>
              <input type="file" name="image">
            </li>
            <li>
              <input type="submit" name="upload" value="送信">
            </li>
          </ul>
        </div>
      </form>
    </div>

  </section>

</body>

</html>