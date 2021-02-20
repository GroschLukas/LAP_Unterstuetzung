<!DOCTYPE HTML>
<html>
  <head>
  <link rel="stylesheet" href="style.css">
      <title>Hotel Anmeldung</title>
      <meta charset="utf-8">
      <ul class="navbar">
        <li class="navelement"><a class="link" href="HotelAnmeldung.php" target="_self">Home</a></li>
        <li class="navelement"><a class="link" href="anmeldung.php" target="_self">Anmeldung</a></li>
        <li class="navelement"><a class="link" href="index.php" target="_self">Schülerverwaltung</a></li>
        <li class="navelement"><a class="link" href="klassenverwaltung.php" target="_self">Klassenverwaltung</a></li>
      </ul>
  </head>
<body>
<?php
if(isset($_POST["vorname"]) && isset($_POST["nachname"]) && isset($_POST["email"]) && isset($_POST["geburtstag"]) && isset($_POST["geschlecht"]))
{
    require_once('config.php'); 
    $vorname = $_POST["vorname"];
    $nachname = $_POST["nachname"];
    $email = $_POST["email"];
    $geschlecht = $_POST["geschlecht"];
    $datum = $_POST["geburtstag"];
    $geburtstag = date("Y-m-d H:i:s",strtotime($datum));

    $stmt = $conn->prepare("INSERT INTO anmeldung (Vorname,Nachname,Email,geschlecht,geburtsdatum,anmeldedatum) VALUES(?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssd", $vorname,$nachname,$email,$geschlecht,$geburtstag);
    $stmt->execute();
    ?>
    <script type="text/javascript">
    window.location = "HotelAnmeldung.php";
    </script>   
    <?php  
    
}
?>
<form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="anmeldung">
<label>Vorname</label><input name="vorname" type="text" maxlength="100" required><br>
<label>Nachname</label><input name="nachname" type="text" maxlength="100" required><br>
<label>Email Adresse</label><input name="email" type="email"required><br>
<label>Geschlecht</label> <br>
<input type="radio" id="weiblich" name="geschlecht" value="Weiblich"checked>
<label for="male">Male</label><br>
<input type="radio" id="männlich" name="geschlecht" value="Männlich">
<label for="female">Female</label><br>
<input type="radio" id="divers" name="geschlecht" value="Divers">
<label for="other">Other</label> <br>
<label>Geburtstag</label><input name="geburtstag" type="date" maxlength="100" required><br>
<input type="submit" value="Registrieren">
</form>
</body>
</html>

