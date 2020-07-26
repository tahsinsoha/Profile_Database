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
} elseif (
    isset($_POST['first_name']) && isset($_POST['last_name'])
    && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])
) {
    $msg = validatePos();
    $msg2=validateEdu();
    if (empty($_POST["first_name"]) || empty($_POST["last_name"]) || empty($_POST["email"]) || empty($_POST["headline"])  || empty($_POST['summary'])) {
        $_SESSION["error"] = "All values are required";
        header('Location: add.php');
        return;
    } elseif (strpos($_POST["email"], '@') == false) {
        $_SESSION["error"] = "Email must have an at-sign (@)";
        header('Location: add.php');
        return;
    } elseif (is_string($msg)) {
        $_SESSION["error"] = $msg;
        header('Location: add.php');
        return;
    } 
    elseif (is_string($msg2)) {
        $_SESSION["error"] = $msg2;
        header("Location: add.php");
        return;
    }
    else {
        $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        ));

        $profile_id = $pdo->lastInsertId();

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
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            ));

            $rank++;
        }

        // $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
        // $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
        insertEdu($pdo, $profile_id);


        $_SESSION["success"] = "Profile added";
        header('Location: index.php');
        return;
    }
}



?>



<!DOCTYPE html>
<html>

<head>
    <title>Sabiha Tahsin Soha</title>
    <!-- head.php -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <h1>Adding Profile for UMSI</h1>
        <?php
        if (isset($_SESSION["error"])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION["error"]) . "</p>\n");
            unset($_SESSION["error"]);
        }

        ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" /></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" /></p>
            <p>Email:
                <input type="text" name="email" size="30" /></p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" /></p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80"></textarea>
                <p>
                    Education: <input type="submit" id="addEdu" value="+">
                    <div id="edu_fields">
                    </div>
                </p>
                <p>
                    Position: <input type="submit" id="addPos" value="+">
                    <div id="position_fields">
                    </div>
                </p>
                <p>
                    <input type="submit" value="Add">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
        </form>
        <script>
            countPos = 0;
            countEdu = 0;

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
            <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"><br>\
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
                });

                $('#addEdu').click(function(event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding education " + countEdu);

                    $('#edu_fields').append(
                        '<div id="edu' + countEdu + '"> \
            <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
            </p></div>'
                    );

                    $('.school').autocomplete({
                        source: "school.php"
                    });

                });

            });
        </script>
    </div>
</body>

</html>