<?php
// Demand a GET parameter
session_start();
require_once "pdo.php";
echo('<h1>Profile Information</h1>'."\n");
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz and user_id=:p");
$stmt->execute(array(":xyz" => $_GET['profile_id'], ":p"=> $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$n = htmlentities($row['first_name']);
$e = htmlentities($row['last_name']);
$p = htmlentities($row['email']);
$q = htmlentities($row['headline']);
$r = htmlentities($row['summary']);

echo('<h2>First Name:</h2>');
echo ($n."\n");
echo("\n");
echo('<h2>Last Name:</h2>');
echo ($e."\n");
echo("\n");
echo('<h2>Email:</h2>');
echo ($p."\n");
echo("\n");
echo('<h2>Headline:</h2>');
echo ($q."\n");
echo("\n");
echo('<h2>Summary:</h2>');
echo ($r."\n");
echo("\n");


$pt = $pdo->prepare('SELECT * FROM Position WHERE profile_id=:pid');
$pt->execute(array(':pid' => $_REQUEST['profile_id']));


echo('<h2>Position:</h2>'); 
while($x = $pt->fetch(PDO::FETCH_ASSOC)){
    echo ($x['year']);
    echo(":");
    echo ($x['description']);
   // echo('\n');
}

$stmt = $pdo->prepare("SELECT * FROM Education left join Institution on Education.institution_id=Institution.Institution_id where profile_id = :prof order by rank");
$stmt->execute(array(":prof" => $_GET['profile_id']));


echo('<h2>Education:</h2>'); 
while($x = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo ($x['year']);
    echo(":");
    echo ($x['name']);
  //  echo('\n');
}



?>