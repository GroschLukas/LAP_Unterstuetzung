<!DOCTYPE HTML>
<html>
  <head>
  <link rel="stylesheet" href="style.css">
      <title>Hotel Anmeldung</title>
      <meta charset="utf-8"">
      <ul class="navbar">
        <li class="navelement"><a class="link" href="HotelAnmeldung.php" target="_self">Home</a></li>
        <li class="navelement"><a class="link" href="anmeldung.php" target="_self">Anmeldung</a></li>
        <li class="navelement"><a class="link" href="index.php" target="_self">Schülerverwaltung</a></li>
        <li class="navelement"><a class="link" href="klassenverwaltung.php" target="_self">Klassenverwaltung</a></li>
      </ul>
  </head>
<body>
<?php require_once("config.php"); ?>
<p>Anmeldung in den letzten 24 Stunden:</p>
<?php
$stmt = "SELECT id,Vorname,Nachname,Anmeldedatum,Geschlecht FROM anmeldung WHERE anmeldedatum >=date_sub(NOW(),INTERVAL 1 DAY)"; 
//$stmt = "SELECT * FROM anmeldung WHERE id = 1";
$result=$conn->query($stmt);
mysqli_fetch_all($result);
$foundrecords = mysqli_num_rows($result);
if($foundrecords <1) {
    echo "<p>Heute hat sich noch niemand angemeledet<p>";
}
else {
    echo '<table style="width:100%;" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=ID">ID</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Vorname">Vorname</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Nachname">Nachname</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Klasse">Anmeldedatum</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Klasse">Geschlecht</a></b></td>';                  //hinzugefügt
        echo '<tr>';
    foreach($result as $anmeldungen) {
        echo '<tr>';
        echo '<td>'. $anmeldungen['id'] .'</td>';
        echo '<td>'. $anmeldungen['Vorname'] .'</td>';
        echo '<td>'. $anmeldungen['Nachname'] .'</td>';
        echo '<td>'. $anmeldungen['Anmeldedatum'] .'</td>';
        echo '<td>'. $anmeldungen['Geschlecht'] .'</td>';
        echo '<tr>';
    }
}
?>

</body>
</html>

