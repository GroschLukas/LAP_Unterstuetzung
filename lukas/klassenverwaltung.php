<!DOCTYPE HTML>
<html>
  <head>
      <title>DB Uebung</title>
      <meta charset="utf-8"">
  </head>
<body>



<?php
// Programm-Steps prüfen
$step='IDLE';
if (isset($_POST['append_cancel'])) {
    $step = 'IDLE';
}
if (isset($_POST['append'])) {
    $step = 'APPENDMODE';
}
if (isset($_POST['append_save'])) {
    $step = 'APPENDSAVE';
}

if (isset($_POST['edit'])) {
    $step = 'EDITMODE';
}
if (isset($_POST['edit_save'])) {
    $step = 'EDITSAVE';
}

if (isset($_POST['delete'])) {
    $step = 'DELETEMODE';
}
if (isset($_POST['edit_delete'])) {
    $step = 'DELETEEXECUTE';
}
if (isset($_POST['klasse_id'])) {
    $id = $_POST['klasse_id'];    
} else {
    $id = 0;
}
?>


<h1>Klassenverwaltung Groch Lukas</h1>
<?php 
    require_once('config.php'); 
    $query='select klasse_id, name from klassen';    
    // Sortierung über GET-Variable
    if (isset($_GET['sort'])) {
      if ($_GET['sort']=='okla') {
         $query = $query.' ORDER by klasse_id';
      } else if ($_GET['sort']=='ona') {
         $query=$query.' ORDER by name';
      }
    }       
    $result = $conn->query($query);
    mysqli_fetch_all($result);
    $foundrecords = mysqli_num_rows($result);
    if ($foundrecords==0) 
    {
        echo '<h3>Keine Datensätze gefunden!</h3>';
    } else { 
        echo '<table style="width:100%;" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="klassenverwaltung.php?sort=okla">ID</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="klassenverwaltung.php?sort=ona">Klasse</a></b></td>';                  //hinzugefügt
        echo '<td style="background:#eeeeee;" align="center"><b>Funktionen</b></td>';
        echo '</tr>';
        
        foreach ($result as $klasse) {
            echo '<tr>';
            echo '<td align="center">'.$klasse['klasse_id'].'</td>';
            echo '<td>'.$klasse['name'].'</td>';
                                                                         //hinzugefügt
            echo '<td>';
           
            ?>
            <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="buttons">
                <input name="edit" type="submit" value="Bearbeiten" <?php if ($step !='IDLE') echo 'disabled'; ?> >
                <input name="delete" type="submit" value="Löschen"  <?php if ($step !='IDLE') echo 'disabled'; ?> >
                <input name=hurensohn>
                <input type="hidden" name="klasse_id" value="<?php echo $klasse['klasse_id']; ?>">
            </form>
            <?php
            echo'</td>';
            echo '<tr>';
        }
        echo '</table>';    }
        ?>
<hr>

<?php

if ($step=='IDLE')  // Erweitrn anzeigen
{ ?>
  <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="edit_klasse">
  <input name="append" type="submit" value="Neuen Klasse anlegen">
  <a href="index.php"><input name="verwaltung" type="button" value="zurück zur Schülerverwaltung"></a>
  <a href="HotelAnmeldung.php"><input name="hotelanmeldungg" type="button" value="zur Hotel Anmeldung"></a>
  </form>
<?php
}


if ($step=='DELETEEXECUTE') {
    $id = $_POST['klasse_id'];
    $stmt = $conn->prepare("DELETE FROM klassen WHERE klasse_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    ?>

    <script type="text/javascript">
    window.location = "<?php $_SERVER['PHP_SELF']; ?>";
    </script>        

    <?php
    // Fertig: Seite neu laden

}

if ($step=='APPENDMODE' || $step=='APPENDSAVE' || $step=='EDITMODE' || $step=='EDITSAVE' || $step=='DELETEMODE') 
{ 
  $klassen = '';
  $id = 0;
  $showform = 1;
  $error_k = '';

   // prüfen auf Eingabefehler im Append Mode
  if ($step=='APPENDSAVE' || $step=='EDITSAVE') { 
     $klassen  = trim($_POST['name']);
     $id = $_POST['klasse_id'];   
     if ($klasse != '') { // alles OK -> Datensatz speichern     
       
        if ($step=='APPENDSAVE') {
           $stmt = $conn->prepare("INSERT INTO klassen (name) VALUES (?)");
           $stmt->bind_param("s",$klassen);
           $stmt->execute();

        } 
        if ($step=='EDITSAVE') {
           $stmt = $conn->prepare("UPDATE klassen SET name = ? WHERE klasse_id = ?");
           $stmt->bind_param("si", $klassen, $id);
           $stmt->execute();
        } 
        ?>
        <script type="text/javascript">
        window.location = "<?php $_SERVER['PHP_SELF']; ?>";
        </script>        
        <?php
        // Fertig: Seite neu aufrufen... muss mit JS aufgerufen werden, da mit PHP nicht fuinktioniert (Header)
     } else {      
        if ($klassen == '') 
        {
            $error_k='Bitte geben Sie einen Klassen Namen ein!';
        }

     }
  } 
  if ($step=='EDITMODE' || $step=='DELETEMODE') {
    $id = trim($_POST['klasse_id']); 
    $query='SELECT klasse_id,name FROM klassen WHERE klasse_id = '.$id;
    $result = $conn->query($query);
    mysqli_fetch_all($result);
    $foundrecords = mysqli_num_rows($result);
    if ($foundrecords <> 1) {
        // sollte nicht passieren
        echo '<h3><span style="color:#ff0000;">Datensatz mit der ID '.$id.' wurde nicht gefunden, möglicherweise von anderen Benutzer gelöscht!</span></h3>';
        $showform = 0;
     } else {
        foreach ($result as $klassen) {
           $klassen   = $klassen['name'];
           break;
        }
     }
  } 
   
  
  if ( $showform == 1) {
  ?>
    <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="edit_schueler">
    <input type="hidden" name="klasse_id" value="<?php echo $id; ?>">
    <table>
    <tr>
    <td>Klasse:</td>
    <td><input name="name" type="text" value="<?php echo $klassen; ?>" maxlength="100" <?php if ($step=='DELETEMODE') echo 'readonly'; ?> /></td>
    <td><span style="color:#ff0000;"><?php echo $error_k; ?></span></td>
    </tr>
    </tr>
    
    
    
    </table>
    <?php
       if ($step=='APPENDMODE' || $step=='APPENDSAVE') { ?>
          <input name="append_save" type="submit" value="Neuen Datensatz speichern">
       <?php 
       }
       if ($step=='EDITMODE' || $step=='EDITSAVE') { ?> 
          <input name="edit_save" type="submit" value="Änderungen speichern">
       <?php
       }
       if ($step=='DELETEMODE') { ?> 
          <input name="edit_delete" type="submit" value="Datensatz löschen">
       <?php
       }          
    ?>
    <input name="append_cancel" type="submit" value="Abbruch">
    </form>
    <?php
  }
}
?>
</body>
</html>