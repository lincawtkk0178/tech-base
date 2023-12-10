<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5</title>
</head>
<body>
    <?php
        // DB接続設定
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        // テーブル作成
        $sqlcre = "CREATE TABLE IF NOT EXISTS tbsave"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name CHAR(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "password TEXT"
        .");";
        $stmt = $pdo->query($sqlcre);

        // 送信受け取り、追記
        if(!empty($_POST["str"]) && !empty($_POST["name"]) && empty($_POST["editnum"]))
        {
            $name = $_POST["name"];
            $comment = $_POST["str"];
            $date = date("Y年m月d日 H時i分s秒");
            $password = $_POST["password"];

            $sqlin = "INSERT INTO tbsave (name, comment, date, password) VALUES (:name, :comment, :date, :password)";
            $stmt = $pdo->prepare($sqlin);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
        }
        // 削除
        else if(!empty($_POST["delete"]) && !empty($_POST["delpass"]))
        {
            $id = $_POST["delete"];
            $delpass = $_POST["delpass"];

            $sqlsel = 'SELECT * FROM tbsave WHERE id=:id ';
            $stmt = $pdo->prepare($sqlsel);                 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->execute();                             
            $results = $stmt->fetchAll(); 
            foreach ($results as $row)
            {
                $pass = $row['password'];
            }
            if($delpass == $pass)
            {
                $sqldel = 'delete from tbsave where id=:id';
                $stmt = $pdo->prepare($sqldel);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        // 編集番号指定、入力フォームに内容を表示
        else if(!empty($_POST["edit"]) && !empty($_POST["editpass"]))
        {
            $id = $_POST["edit"];
            $editer = $_POST["editpass"];

            $sqlsel = 'SELECT * FROM tbsave WHERE id=:id ';
            $stmt = $pdo->prepare($sqlsel);                 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->execute();                             
            $results = $stmt->fetchAll(); 
            foreach ($results as $row)
            {
                $pass = $row['password'];
            }
            if($editer == $pass)
            {
                foreach ($results as $row)
                {
                    $editnum = $id;
                    $editname = $row['name'];
                    $editcomment = $row['comment'];
                    $edpass = $row['password'];
                }
            }
        }
        // 編集内容
        else if(!empty($_POST["str"]) && !empty($_POST["name"]) && !empty($_POST["editnum"]) && !empty($_POST["password"]))
        {
            $name = $_POST["name"];
            $comment = $_POST["str"];
            $date = date("Y年m月d日 H時i分s秒");
            $pass = $_POST["password"];
            $id = $_POST["editnum"];

            $sqlup = 'UPDATE tbsave SET name=:name,comment=:comment, date=:date, password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sqlup);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
            $stmt->execute();
        }
    ?>
    
    <form action="mission5-1.php" method="post">
        <input type="text" name="str" placeholder="コメント" value="<?php if(!empty($editcomment)){echo $editcomment;}?>">
        <br>        
        <input type="text" name="name" placeholder="名前" value="<?php if(!empty($editname)){echo $editname;}?>">
        <br>
        <input type="text" name="password" placeholder="パスワード" value="<?php if(!empty($edpass)){echo $edpass;}?>">
        <input type="submit" name="submit">
        <br>

        <input type="number" name="delete" placeholder="削除したい番号">
        <br>
        <input type="text" name="delpass" placeholder="削除する人のパスワード">
        <input type="submit" name="del" value="削除">
        <br>
        
        <input type="number" name="edit" placeholder="編集したい番号">
        <br>
        <input type="text" name="editpass" placeholder="編集者のパスワード">
        <input type="submit" name="ed" value="編集">
        <br>
        <input type="hidden" name="editnum" placeholder="編集中の番号" value="<?php if(!empty($editnum))echo $editnum;?>">
    </form>
    <br>

    <?php
        // ブラウザに表示
        $sqlsel = 'SELECT * FROM tbsave';
        $stmt = $pdo->query($sqlsel);
        $results = $stmt->fetchAll();
        foreach ($results as $row)
        {
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
</body>
</html>