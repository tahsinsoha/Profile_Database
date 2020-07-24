<?php
// Demand a GET parameter
session_start();
require_once "pdo.php";

if ( ! isset($_SESSION['user_id']) ) {
    die('Not Logged In');
}
// If the user requested logout go back to index.php
if (isset($_POST['cancel'])) {

    header("Location: index.php");
    return;
}

elseif (isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) &&isset($_POST['save']) && isset($_GET['profile_id']) ) {
        if (empty($_POST["first_name"]) || empty($_POST["last_name"]) || empty($_POST["email"] ) || empty($_POST["headline"])  || empty($_POST['summary'])) {
            $_SESSION["error"] = "All fields are required";
            header("Location: edit.php?profile_id=".urlencode($_GET['profile_id']));
            return;
        }	
        elseif (strpos($_POST["email"], '@') == false) {
            $_SESSION["error"] = "Email must have an at-sign (@)";
            header("Location: edit.php?profile_id=".urlencode($_GET['profile_id']));
            return;
        }

	else{
        $sql = "UPDATE profile SET first_name = :fn, last_name= :ln,
        email = :email, headline = :headline, summary=:summary
        WHERE profile_id = :zip and user_id = :p";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':email' => $_POST['year'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary'],
            ':zip' => $_GET['profile_id'],
            ':p'=> $_SESSION['user_id']

        ));
            $_SESSION["success"] = "Profile updated";
            header('Location: index.php');
            return;
			
		}
    }

    if ( ! isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header("Location: edit.php?profile_id=".urlencode($_GET['profile_id']));
        return;
      }

    
    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz and user_id=:p");
$stmt->execute(array(":xyz" => $_GET['profile_id'], ":p"=> $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "All fields are required";
    header("Location: edit.php?profile_id=".urlencode($_GET['profile_id']));
    return;
}
if ( isset($_SESSION["error"])) {
    echo ('<p style="color: red;">' . htmlentities($_SESSION["error"]) . "</p>\n");
        unset($_SESSION["error"]);
}


 $n = htmlentities($row['first_name']);
$e = htmlentities($row['last_name']);
$p = htmlentities($row['email']);
$q = htmlentities($row['headline']);
$r = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>

<!DOCTYPE html>
<html>
<head>
<title>Sabiha Tahsin Soha</title>
</head>
<body>
<div class="container">
<h1>Editing Profile for UMSI</h1>

    

<form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= $n ?>" /></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= $e?>" /></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= $p ?>"/></p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" value="<?= $q ?>" /></p>
            <p>Summary:<br />
            <input type="text" name="summary" size="80" value="<?= $r ?>" /></p>
                <input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']);?>">
                <p>
                    <input type="submit" value="Save" name="save">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
        </form>
    </div>
</body>

</html>