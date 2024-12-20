<?php
session_start();

$db_name = '';
$db_host = '';
$db_id = '';
$db_pw = '';
$username = 'name';
$password = 'password';

try {
    $server_info = 'mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host;
    $pdo = new PDO($server_info,$db_id,$db_pw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['name'] ?? '';
    $inputPassword = $_POST['pw'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM user_an_table WHERE name = :name");
    $stmt->bindParam(':name', $inputUsername);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($inputPassword, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        $_SESSION['message'] = "ログイン成功！ようこそ、" . htmlspecialchars($user['name']) . "さん。";
        header("Location: home.php");
        exit();
    } else {
        $_SESSION['login_error'] = "ユーザー名またはパスワードが間違っています。";
        header("Location: home.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div>
        <div>
            <h1>Login</h1>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label>ユーザーネーム</label><br>
            <input type="text" name="name" value="" required><br>
            <label>パスワード</label><br>
            <input type="password" name="pw" value="" required><br>
            <button type="submit" name="login">ログイン</button>
        </form>
        <a href="signup.php">サインアップ</a>
        <?php
        if (!empty($_SESSION['login_error'])) {
            echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['login_error']) . "</p>";
            unset($_SESSION['login_error']);
        }
        ?>
    </div>
</body>
</html>