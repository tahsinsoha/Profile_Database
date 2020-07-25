<?php
require_once "pdo.php";

function validatePos() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        return "All fields are required";
      }
  
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
    }
    return true;
  }

  function loadPos($pdo, $p_id){

    $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :prof order by rank");
$stmt->execute(array(":prof" => $p_id));
$ps = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $ps[] = $row;
}
return $ps;
  }
