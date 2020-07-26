<?php
require_once "pdo.php";

function validatePos()
{
  for ($i = 1; $i <= 9; $i++) {
    if (!isset($_POST['year' . $i])) continue;
    if (!isset($_POST['desc' . $i])) continue;

    $year = $_POST['year' . $i];
    $desc = $_POST['desc' . $i];

    if (strlen($year) == 0 || strlen($desc) == 0) {
      return "All values are required";
    }

    if (!is_numeric($year)) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu()
{
  for ($i = 1; $i <= 9; $i++) {
    if (!isset($_POST['edu_year' . $i])) continue;
    if (!isset($_POST['edu_school' . $i])) continue;

    $year = $_POST['edu_year' . $i];
    $school = $_POST['edu_school' . $i];

    if (strlen($year) == 0 || strlen($school) == 0) {
      return "All values are required";
    }

    if (!is_numeric($year)) {
      return "Position year must be numeric";
    }
  }
  return true;
}


function insertEdu($pdo, $profile_id)
{
  $rank = 1;
  for ($i = 1; $i <= 9; $i++) {
    if (!isset($_POST['edu_year' . $i])) continue;
    if (!isset($_POST['edu_school' . $i])) continue;

    $year = $_POST['edu_year' . $i];
    $school = $_POST['edu_school' . $i];
    $i_id = false;
    $stmt = $pdo->prepare('SELECT institution_id FROM institution where name=:name');
    $stmt->execute(array(":name" => $school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) $i_id = $row['institution_id'];
    if ($i_id === false) {
      $stmt = $pdo->prepare('INSERT INTO institution
      (name)
      VALUES ( :name)');
      $stmt->execute(array(":name" => $school));
      $i_id = $pdo->lastInsertId();
    }


    $stmt = $pdo->prepare('INSERT INTO Education
(profile_id, rank, year, institution_id)
VALUES ( :pid, :rank, :year, :iid)');

    $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':iid' => $i_id
    ));

    $rank++;
  }
}

function loadPos($pdo, $p_id)
{

  $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :prof order by rank");
  $stmt->execute(array(":prof" => $p_id));
  $ps = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $ps[] = $row;
  }
  return $ps;
}

function loadEdu($pdo, $profile_id)
{
 // $query="SELECT year from education";


  $stmt = $pdo->prepare("SELECT * FROM Education left join Institution on Education.institution_id=Institution.Institution_id where profile_id = :prof order by rank");
  $stmt->execute(array(":prof" => $profile_id));
  $ps = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $ps[] = $row;
  }
  return $ps;
}
