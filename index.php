<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sabiha Tahsin Soha</title>
</head>
<h1>Welcome to Automobiles Database</h1>

<body>
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo ('<a href="login.php">Please log in</a>');
    }
    else {
        echo '<a href="add.php">Add New Entry</a>';
        echo '<a href="logout.php">Logout</a>';

    }
    if (!isset($_SESSION['pp'])) {
      //  echo ('<a href="login.php">Please log in</a>');
       // echo ('<p>Attempt to <a href="add.php">add data</a>without logging in</p>');

       $stmt = $pdo->query("SELECT first_name,last_name,email,headline,summary,profile_id FROM profile");
       $row = $stmt->fetch(PDO::FETCH_ASSOC);

         if($row===0){
           $_SESSION['error'] = 'No rows found';
           header( 'Location: index.php' ) ;
           return;
         }
          else {
       echo ('<table border="1">' . "\n");
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>";
        echo (htmlentities($row['first_name']).htmlentities($row['last_name']));
        echo ("</td><td>");
        echo (htmlentities($row['headline']));
        echo ("</td><td>");
        //    echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
        //    echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
        //    echo ("</td></tr>\n");
       }
   }
    } else {

        if (isset($_SESSION['success'])) {
            echo ('<p style="color: green;">' . htmlentities($_SESSION["success"]) . "</p>\n");
            unset($_SESSION["success"]);
        }
        $sql = "SELECT first_name,last_name,email,headline,summary,profile_id FROM profile Where user_id= :zip";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':zip' => $_SESSION['user_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

          if($row===0){
            $_SESSION['error'] = 'No rows found';
            header( 'Location: index.php' ) ;
            return;
          }
           else {
        echo ('<table border="1">' . "\n");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>";
            echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">'.$row['first_name'].$row['last_name'].'</a>';
            echo ("</td><td>");
            echo (htmlentities($row['headline']));
            echo ("</td><td>");
            echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
            echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
            echo ("</td></tr>\n");
        }
    }
    }



    ?>
    </table>
</body>

</html>