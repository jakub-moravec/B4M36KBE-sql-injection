<?php
/*
 * ----------------------------
 * Server logic
 * ----------------------------
 */
define("MESSAGES_PER_PAGE", 3);
define("XORING_KEY_FOR_MESSAGES", "SecretXorCode");

session_start();
$db = new mysqli("localhost", "kbe", "kbe", "kbe");

if (isset($_GET["open"]) and in_array($_GET["open"], array("readme.txt", "index.php", "./index.php"))) {
    highlight_file($_GET["open"]);
    exit();
}

if (isset($_GET["logout"])) {
    unset($_SESSION["username"], $_SESSION["logged"]);
    header("Location: index.php");
}

if (isset($_POST["username"], $_POST["password"])) {
    $username = $db->query("SELECT username FROM users WHERE username = '$_POST[username]' AND password = SHA1('$_POST[password]')")->fetch_assoc()["username"];
    if ($username) {
        $_SESSION["username"] = $username;
        $_SESSION["logged"] = False;
    } else {
        $wrong_creditials = True;
    }
}

if (isset($_POST["pin"], $_SESSION["username"])) {
    $result = $db->query("SELECT 1 FROM users WHERE username = '" . $db->escape_string($_SESSION["username"]) . "' AND pin = " . $db->escape_string($_POST["pin"]));
    if ($result->num_rows) {
        $_SESSION["logged"] = True;
    } else {
        $wrong_pin = True;
    }
}

function base64_xor_cipher($data, $key, $encode = True) {
    $data = ($encode) ? $data : base64_decode($data);
    for ($i = 0; $i < strlen($data); $i++) {
        $data[$i] = ($data[$i] ^ $key[$i % strlen($key)]);
    }
    return ($encode) ? base64_encode($data) : $data;
}

function h($val) {
    return htmlspecialchars($val, ENT_QUOTES);
}
/*
 * ----------------------------
 * HTML page ...
 * ----------------------------
 */
?>
<!DOCTYPE html>
<html>
<head>
<title>KBE - SQL Injection</title>
<style type="text/css">
* {font-family: sans-serif; box-sizing: border-box;}
body {background: #76b852;}
main {position: relative; width: 360px; margin: 0 auto; padding: 30px; margin-top: 12%; text-align: center; background: #FFFFFF; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24)}
input[type="text"], input[type="password"] {width: 100%; text-align: center; padding: 12px; margin-bottom: 15px; font-size: 18px; background: #f2f2f2; border: none;}
input[type="submit"] {width: 100%; padding: 12px; font-size: 18px; cursor: pointer; background: #4CAF50; color: white; text-transform: uppercase; border: none;}
input[type="submit"]:hover { background: #43A047; }
form {text-align: center; }
h3 {color: red;}
a:link, a:visited, a:active {color: green; text-decoration: none;}
a:hover {text-decoration: underline;}
.wrong {border: 2px solid red !important;}
.message {font-weight: bold; }
#logout {position: absolute; right: 15px; top: 15px; }
}
</style>
</head>
<body>
<main>

<?php
/*
 * ----------------------------
 * Messages
 * ----------------------------
 */
if (isset($_SESSION["username"], $_SESSION["logged"]) && $_SESSION["logged"] == True):
?>
<h1>Messages</h1>
<?php
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;
    $count = $db->query("SELECT COUNT(*) AS count FROM messages WHERE username = '$_SESSION[username]'")->fetch_assoc()["count"];
    $messages = $db->query("SELECT date_time, base64_xored_message_with_plain_key AS message FROM messages WHERE username = '$_SESSION[username]' LIMIT " . MESSAGES_PER_PAGE . " OFFSET $offset");

    while ($row = $messages->fetch_assoc()) {
    echo "<p class='message'>" . h($row["date_time"]) . "</p><p>" . base64_xor_cipher($row["message"], XORING_KEY_FOR_MESSAGES, False) . "</p>";
    }

    echo "<p>";
    if ($offset > 0) {
        echo "<a href='?offset=" . (h($offset) - MESSAGES_PER_PAGE) . "'>&lt;&lt; Back</a>&nbsp;";
    }

    if ($count - $offset > MESSAGES_PER_PAGE) {
        echo "<a href='?offset=" . (h($offset) + MESSAGES_PER_PAGE) . "'>Next &gt;&gt;</a>";
    }
    echo "</p>";
?>
<a id="logout" href="index.php?logout">Logout</a>
<a href="index.php?open=readme.txt">Please read me!</a>

<?php
/*
 * ----------------------------
 * PIN number form
 * ----------------------------
 */
elseif (isset($_SESSION["username"], $_SESSION["logged"]) && $_SESSION["logged"] == False):
?>
<h1>2-Step Verification</h1>
<h2>Welcome <font color="green"><?php echo h($_SESSION["username"]); ?></font>, enter your four digit PIN number</h2>
<form id="pin" action="index.php" method="post">
<?php if (isset($wrong_pin)) { echo "<h3>Wrong pin</h3>";} ?>
<input type="text" maxlength="4" name="pin" placeholder="_ _ _ _" required <?php if (isset($wrong_pin)) {echo 'class="wrong"';} ?>>
<input type="submit" value="Verify">
</form>
<a id="logout" href="index.php?logout">Logout</a>

<?php
/*
 * ----------------------------
 * Login form
 * ----------------------------
 */
else:
?>
<h1>Login</h1>
<form name="login" action="index.php" method="post">
<?php if (isset($wrong_creditials)) {echo "<h3>Wrong credentials</h3>";} ?>
<input type="text" placeholder="Username" name="username" required <?php if (isset($wrong_creditials)) {echo 'class="wrong"';} ?>>
<input type="password" placeholder="Password" name="password" required <?php if (isset($wrong_creditials)) {echo 'class="wrong"';} ?>>
<input type="submit" value="Login">
</form>

<?php endif; ?>
</main>
</body>
</html>