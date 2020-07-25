<?php
// Demand a GET parameter
session_start();
require_once "pdo.php";
require_once "utility.php";
if (!isset($_SESSION['user_id'])) {
    die('Not Logged In');
}
// If the user requested logout go back to index.php
if (isset($_POST['cancel'])) {

    header("Location: index.php");
    return;
}


if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz and user_id=:p ");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id'], ":p" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "blablablah";
    header("Location: edit.php?profile_id=" . urlencode($_REQUEST['profile_id']));
    return;
}

if (
    isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])
) {
    $msg = validatePos();
    if (empty($_POST["first_name"]) || empty($_POST["last_name"]) || empty($_POST["email"]) || empty($_POST["headline"])  || empty($_POST['summary'])) {
        $_SESSION["error"] = "All fields are required";
        header("Location: edit.php?profile_id=" . urlencode($_REQUEST['profile_id']));
        return;
    } elseif (strpos($_POST["email"], '@') == false) {
        $_SESSION["error"] = "Email must have an at-sign (@)";
        header("Location: edit.php?profile_id=" . urlencode($_REQUEST['profile_id']));
        return;
    } elseif (is_string($msg)) {
        $_SESSION["error"] = $msg;
        header("Location: edit.php?profile_id=" . urlencode($_REQUEST['profile_id']));
        return;
    } else {
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
            ':zip' => $_REQUEST['profile_id'],
            ':p' => $_SESSION['user_id']

        ));

        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;

            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            $stmt = $pdo->prepare('INSERT INTO Position
    (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :desc)');

            $stmt->execute(array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            ));

            $rank++;
        }

        $_SESSION["success"] = "Profile updated";
        header('Location: index.php');
        return;
    }
}

if (isset($_SESSION["error"])) {
    echo ('<p style="color: red;">' . htmlentities($_SESSION["error"]) . "</p>\n");
    unset($_SESSION["error"]);
}


$n = htmlentities($row['first_name']);
$e = htmlentities($row['last_name']);
$p = htmlentities($row['email']);
$q = htmlentities($row['headline']);
$r = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

$positions = loadPos($pdo, $_REQUEST['profile_id']);

?>



<!DOCTYPE html>
<html>

<head>
    <title>Sabiha Tahsin Soha</title>
    <!-- head.php -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <h1>Editing Profile for UMSI</h1>
        <form method="post" action="edit.php">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= $n ?>" /></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= $e ?>" /></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= $p ?>" /></p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" value="<?= $q ?>" /></p>
            <p>Summary:<br />
                <input type="text" name="summary" size="80" value="<?= $r ?>" /></p>
            <input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']) ?>">


            <?php
            $pos = 0;
            echo ('<p>Position: <input type= "submit" id="addPos" value="+">' . "\n");
            echo ('<div id="position_fields">' . "\n");
            foreach ($positions as $position) {

                $pos++;
                echo ('<div id="position' . $pos . '">' . "\n");
                echo ('<p>Year: <input type= "text" name="year' . $pos . '"');
                echo ('value="' . $position['year'] . '"/>' . "\n");
                echo ('<input type="button" value="-"');
                echo ('onclick="$(\'#position' . $pos . '\').remove(); return false;">' . "\n");
                //  echo ("<\p>\n");
                echo ('<textarea name="desc' . $pos . '"rows="8" cols="80">' . "\n");
                echo (htmlentities($position['description']) . "\n");
                echo ("\n</textarea>\n</div>\n");
            }
            echo ("</div></p>\n");

            ?>

            <p>
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.11.4.js"></script>
        <script>
            countPos = 1;

            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function() {
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event) {
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
                });
            });
        </script>
    </div>
</body>

</html>