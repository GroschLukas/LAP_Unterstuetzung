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
if (isset($_POST['schuelerid'])) {
    $id = $_POST['schuelerid'];    
} else {
    $id = 0;
}
?>


<h1>Schülerverwaltung Grosch Lukas</h1>
<?php 
    require_once('config.php'); 
    $query='select id,vorname,nachname, klassen.name from schueler left outer join klassen ON schueler.klasse_id = klassen.klasse_id';    
    // Sortierung über GET-Variable
    if (isset($_GET['sort'])) {
      if ($_GET['sort']=='Vorname') {
         $query = $query.' ORDER by vorname, Nachname';
      } else if ($_GET['sort']=='Nachname') {
         $query=$query.' ORDER by Nachname, Vorname';
      } else if ($_GET['sort']=='ID') {
         $query=$query.' ORDER by ID';
      } else if($_GET['sort']=='Klasse'){         //Hinzugefügt
      $query=$query.' ORDER by isnull(Name),Vorname,Nachname';
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
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=ID">ID</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Vorname">Vorname</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Nachname">Nachname</a></b></td>';
        echo '<td style="background:#eeeeee;" align="center"><b><a href="index.php?sort=Klasse">Klasse</a></b></td>';                  //hinzugefügt
        echo '<td style="background:#eeeeee;" align="center"><b>Funktionen</b></td>';
        echo '<tr>';
        
        foreach ($result as $schueler) {
            echo '<tr>';
            echo '<td align="center">'.$schueler['id'].'</td>';
            echo '<td>'.$schueler['vorname'].'</td>';
            echo '<td>'.$schueler['nachname'].'</td>';
            echo '<td>'.$schueler['name'].'</td>';                                                                          //hinzugefügt
            echo '<td>';
           
            ?>
            <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="buttons">
                <input name="edit" type="submit" value="Bearbeiten" <?php if ($step !='IDLE') echo 'disabled'; ?> >
                <input name="delete" type="submit" value="Löschen"  <?php if ($step !='IDLE') echo 'disabled'; ?> >
                <input type="hidden" name="schuelerid" value="<?php echo $schueler['id']; ?>">
            </form>
            <?php
            echo'</td>';
            echo '<tr>';
        }
        echo '</table>';    
    }
?>
<hr>

<?php

if ($step=='IDLE')  // Erweitrn anzeigen
{ ?>
  <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="edit_schueler">
  <input name="append" type="submit" value="Neuen Datensatz erstellen">
  <a href="klassenverwaltung.php"><input name="klassenverwaltung" type="button" value="zur Klassenverwaltung"></a>
  <a href="HotelAnmeldung.php"><input name="hotelanmeldungg" type="button" value="zur Hotel Anmeldung"></a>
  </form>
<?php
}


if ($step=='DELETEEXECUTE') {
    $id = $_POST['schuelerid'];
    $stmt = $conn->prepare("DELETE FROM schueler WHERE id = ?");
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
  $error_vn='';
  $error_nn='';
  $vorname =  '';
  $nachname = '';
  $klasse_id = 0;
  $showform = 1; 

   // prüfen auf Eingabefehler im Append Mode
  if ($step=='APPENDSAVE' || $step=='EDITSAVE') { 
     $vorname  = trim($_POST['vorname']);
     $nachname = trim($_POST['nachname']);
     $id       = $_POST['schuelerid'];
     $klasse_id= $_POST['klasse'];         
     if ($vorname != '' && $nachname != '') { // alles OK -> Datensatz speichern     
       
        if ($step=='APPENDSAVE') {
           $stmt = $conn->prepare("INSERT INTO schueler (vorname, nachname, klasse_id) VALUES (?, ?, ?)");
           $stmt->bind_param("ssi", $vorname, $nachname, $klasse_id);
           $stmt->execute();

        } 
        if ($step=='EDITSAVE') {
           $stmt = $conn->prepare("UPDATE schueler SET vorname = ?, nachname = ?, klasse_id = ? WHERE id = ?");
           $stmt->bind_param("ssii", $vorname, $nachname, $klasse_id, $id);
           $stmt->execute();
        } 
        ?>
        <script type="text/javascript">
        window.location = "<?php $_SERVER['PHP_SELF']; ?>";
        </script>        
        <?php
        // Fertig: Seite neu aufrufen... muss mit JS aufgerufen werden, da mit PHP nicht fuinktioniert (Header)
     } else {      
        if ($vorname == '') 
        {
            $error_vn='Geben Sie bitte Ihren Vornamen ein!';
        }
        if ($nachname == '') 
        {
            $error_nn='Geben Sie bitte Ihren Nachname ein!';
        }
     }
  } 
  if ($step=='EDITMODE' || $step=='DELETEMODE') {
    $query='SELECT vorname,nachname,klasse_id FROM SCHUELER WHERE id ='.$id;
    $result = $conn->query($query);
    mysqli_fetch_all($result);
    $foundrecords = mysqli_num_rows($result);
    if ($foundrecords <> 1) {
        // sollte niocht passieren
        echo '<h3><span style="color:#ff0000;">Datensatz mit der ID '.$id.' wurde nicht gefunden, möglicherweise von anderen Benutzer gelöscht!</span></h3>';
        $showform = 0;
     } else {
        foreach ($result as $schueler) {
           $vorname   = $schueler['vorname'];
           $nachname  = $schueler['nachname'];
           $klasse_id = $schueler['klasse_id'];       
           break;
        }
     }
  } 
   
  
  if ( $showform == 1) {
  ?>
    <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post" name="edit_schueler">
    <input type="hidden" name="schuelerid" value="<?php echo $id; ?>">
    <table>
    <tr>
    <td>Vorname:</td>
    <td><input name="vorname" type="text" value="<?php echo $vorname; ?>" maxlength="100" <?php if ($step=='DELETEMODE') echo 'readonly'; ?> /></td>
    <td><span style="color:#ff0000;"><?php echo $error_vn; ?></span></td>
    </tr>
    <tr>
    <td>Nachname:</td>
    <td><input name="nachname" type="text" value="<?php echo $nachname; ?>" maxlength="100" <?php if ($step=='DELETEMODE') echo 'readonly'; ?> /></td>
    <td><span style="color:#ff0000;"><?php echo $error_nn; ?></span></td>
    </tr>
    <td>Klasse:</td>
    <td>
       <select  name="klasse" <?php if ($step=='DELETEMODE') echo 'disabled'; ?>>
       <option value="0">nicht definiert</option>
       <?php
       
       
        $query='select klasse_id,name from klassen ORDER by name';    
        $result = $conn->query($query);
        mysqli_fetch_all($result);
        foreach ($result as $klassen) {
          if  ($klassen['klasse_id']==  $klasse_id) {
             $selectedstring = 'selected';
          } else {
            $selectedstring = '';
          }
          echo $klassen['Name'];
          echo '<option '.$selectedstring.' value="'.$klassen['klasse_id']. '">' .$klassen['name'] . '</option>';
        }          
        ?>        
        </select>
    </td>
    <td></td>
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