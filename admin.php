<?php
/**
 * admin.php Page de configuration - Accessible uniquement par les admins 
 * @package Attaques
 * @author  ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8e
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
//On vérifie que le mod est activé
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

global $db, $table_prefix, $prefixe;
// lecture des bbcodes dans la db
$query = "SELECT value FROM `".TABLE_MOD_CFG."` WHERE `mod`='Attaques' and `config`='bbcodes'";
$result = $db->sql_query($query);
$bbcolor = $db->sql_fetch_row($result);
$bbcolor=unserialize($bbcolor[0]);

// Paramètres de configurations transmis par le form
if (isset($pub_submit))
{
  if (isset($pub_transp)) 
    {
    settype($pub_transp,"integer");
    if (is_int($pub_transp)) 
      {
      $config['transp']=$pub_transp;
      }
    if (isset($pub_layer))
      {
      $config['layer']=1;
      }else{
      $config['layer']=0;
      }
    if (isset($pub_defenseur))
      {
      $config['defenseur']=1;
      }else{
      $config['defenseur']=0;
      }
    if (isset($pub_histo))
      {
      $config['histo']=1;
      }else{
      $config['histo']=0;
      }
  $sqldata=serialize($config);
  //$query = "INSERT INTO ".TABLE_MOD_CFG." VALUES ('Attaques','config','".$sqldata."')";
  $query = "UPDATE ".TABLE_MOD_CFG." SET value = '".$sqldata."' WHERE `mod` = 'Attaques' and `config`='config'"; 
  //echo $query;
  $db->sql_query($query);
  }
  echo "<font size='2' color='00FF40'>configuration sauvegard&eacute;e</font><br />";
}
// Fin paramètres de configuration

// Paramètres couleurs BBcodes
if (isset($pub_submitbb))
  {
  $bbcolor['title']=substr($pub_title,0,7);
  $bbcolor['m_g']=substr($pub_m_g,0,7);
  $bbcolor['c_g']=substr($pub_c_g,0,7);
  $bbcolor['d_g']=substr($pub_d_g,0,7);
  $bbcolor['m_r']=substr($pub_m_r,0,7);
  $bbcolor['c_r']=substr($pub_c_r,0,7);
  $bbcolor['perte']=substr($pub_perte,0,7);
  $bbcolor['renta']=substr($pub_renta,0,7);
  //echo "#".dechex(hexdec(substr($pub_title,0,7)))."\n\r"; test de validation de code hexa .. pb si débute par 00 
  $sqldata=serialize($bbcolor);
  //$query = "INSERT INTO ".TABLE_MOD_CFG." VALUES ('Attaques','bbcodes','".$sqldata."')";
  $query = "UPDATE ".TABLE_MOD_CFG." SET value = '".$sqldata."' WHERE `mod` = 'Attaques' and `config`='bbcodes'";
  $db->sql_query($query);
  echo "<font size='2' color='00FF40'>Couleurs BBcode enregistr&eacute;es</font><br />";
  }
// Fin paramètres couleurs BBcodes
// Purge des anciennes archives
if (isset($pub_submitpurg))
  {
  // On récupère les dates des archives présentes dans la base de données par ordre croissant 
  $query = "SELECT DISTINCT `archives_date` FROM ".TABLE_ATTAQUES_ARCHIVES." order by `archives_date`";
  $result = $db->sql_query($query);
  while (list($date)=$db->sql_fetch_row($result))
    {$ann[]=$date;}
  for ($i=0; $i<count($ann) ; $i++)
    {
    if ($ann[$i] <= $pub_purge)
      {
      $query="DELETE FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE `archives_date`=".$ann[$i];
      if (!$db->sql_query($query)) die("erreur SQL");
      }else{
      break;
      }
    }
  // On optimize la table 
  $query = "OPTIMIZE TABLE ".TABLE_ATTAQUES_ARCHIVES;
  if (!$db->sql_query($query)) die("erreur SQL");
  echo "<font size='2' color='00FF40'>purge effectu&eacute;e</font><br />";
  }
// Fin de la purge
// Nettoyage des valeurs non attribués dans la DB
if (isset($pub_submitid))
  {
  //on récupère les données recues par $_POST sans oublié d'enlevé les slash ^^
  $userid = unserialize(stripslashes($pub_val_id));
  foreach ($userid as $value)
    {
    // En fonction du type de la table, on génère la query
    if ($value[0] == "archive")
      {
      $query="DELETE FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE `archives_user_id`=".$value[1];
      }
      elseif ($value[0] == "attack")
      {
      $query="DELETE FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE `attack_user_id`=".$value[1];
      }
      elseif ($value[0] == "recy")
      {
      $query="DELETE FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE `recy_user_id`=".$value[1];
      }
    // et on execute
    if (!$db->sql_query($query)) die("erreur SQL");
    }
  // On optimize les tables parce que je suis propre :-) 
  $query = "OPTIMIZE TABLE ".TABLE_ATTAQUES_ARCHIVES;
  if (!$db->sql_query($query)) die("erreur SQL");
  $query = "OPTIMIZE TABLE ".TABLE_ATTAQUES_ATTAQUES;
  if (!$db->sql_query($query)) die("erreur SQL");
  $query = "OPTIMIZE TABLE ".TABLE_ATTAQUES_RECYCLAGES;
  if (!$db->sql_query($query)) die("erreur SQL");
  echo "<font size='2' color='00FF40'>nettoyage effectu&eacute;e</font><br />";
  }
// Fin du nettoyage
// Connexion Xtense2
if (isset($pub_submitxt2))
  {
  // on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $attack_id = $db->sql_fetch_row($result);
  $attack_id = $attack_id[0];
  // on fait du nettoyage au cas ou 
  $query = "DELETE FROM `".$table_prefix."xtense_callbacks"."` WHERE `mod_id`=".$attack_id;
  $db->sql_query($query);
  // Insert les données pour récuperer les RC 
  $query = "INSERT INTO ".$table_prefix."xtense_callbacks"." ( `mod_id` , `function` , `type` )
				VALUES ( '".$attack_id."', 'attack_rc', 'rc')";
	$db->sql_query($query);
	// Insert les données pour récuperer les RR
	$query = "INSERT INTO ".$table_prefix."xtense_callbacks"." ( `mod_id` , `function` , `type` )
				VALUES ( '".$attack_id."', 'attack_rr', 'rc_cdr')";
	$db->sql_query($query);
  }

// Appel des Javscripts pour le Colorpicker - Pris à Oxyfox et REStyler
echo "<link rel='stylesheet' type='text/css' href='".FOLDER_ATTCK."/ColorPicker/ColorPicker.css' />
<script type='text/javascript' src='".FOLDER_ATTCK."/ColorPicker/CP_Class.js'></script>
<script type='text/javascript'>
window.onload = function()
{
	fctLoad();
}
window.onscroll = function()
{
	fctShow();
}
window.onresize = function()
{
	fctShow();
}
</script>";
// cadre autour des paramètres
echo"<fieldset><legend><b><font color='#0080FF'>Administration&nbsp;";
echo help("Administration");
echo "</font></b></legend>";
// Formulaire des paramètres du module
echo "<form name='form1' style='margin:0px;padding:0px;' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'><center>";
echo "<table width='60%' border='0'>
<tr>
<td class='c' colspan='2'>Param&egrave;tres du module</td>
</tr>
<tr>
<th>Activer le layer&nbsp;".help("layer")."&nbsp;: </th>
<th><input type='checkbox' name='layer' value='true' ";
if ($config['layer'] == 1)
  {
  echo 'checked=checked';
  }
echo "></th>
</tr>
<tr>
<th> Valeur d'opacit&eacute;&nbsp;".help("transparence")."&nbsp;: </th>
<th><input type='textbox' name='transp' value='".$config['transp']."' size='4'>&nbsp;%</th>
</tr>
<tr>
<td class='c' colspan='2'>&nbsp;</td>
</tr>
<tr>
<th>Prise en compte des Attaques subies</th>
<th><input type='checkbox' name='defenseur' value='true' ";
if ($config['defenseur'] == 1)
  {
  echo 'checked=checked';
  }
echo "></th>
</tr>
<tr>
<th>Affichage des histogrammes</th>
<th><input type='checkbox' name='histo' value='true' ";
if ($config['histo'] == 1)
  {
  echo 'checked=checked';
  }
echo "></th>
</tr>
<tr>
<td colspan='2' class='c' align='center'><input name='submit' type='submit' value='Envoyer'></td>
</tr>
";
echo "</table></form>";

// Formulaire des BBcodes
echo "<br />";
echo "<form name='form2' style='margin:0px;padding:0px;' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'><center>";
echo "<table width='60%' border='0'>
<tr>
  <td class='c' colspan='6'>BBCodes&nbsp;".help("bbcolor")."&nbsp;</td>
</tr>";
echo "<tr>
  <th width=35%>Titre</th>
  <th width='50px'align='center'><input type='textbox' name='title' value='".$bbcolor['title']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.title)' style='cursor:pointer' /></th>
  <th width=35%>M&eacute;tal gagn&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='m_g' value='".$bbcolor['m_g']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.m_g)' style='cursor:pointer' /></th>
</tr>";
echo "<tr>
  <th width=35%>Cristal gagn&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='c_g' value='".$bbcolor['c_g']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.c_g)' style='cursor:pointer' /></th>
  <th width=35%>Deut&eacute;rium gagn&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='d_g' value='".$bbcolor['d_g']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.d_g)' style='cursor:pointer' /></th>
</tr>";
echo "<tr>
  <th width=35%>M&eacute;tal recycl&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='m_r' value='".$bbcolor['m_r']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.m_r)' style='cursor:pointer' /></th>
  <th width=35%>Cristal recycl&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='c_r' value='".$bbcolor['c_r']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.c_r)' style='cursor:pointer' /></th>
</tr>";
echo "<tr>
  <th width=35%>Perte</th>
  <th width='50px'align='center'><input type='textbox' name='perte' value='".$bbcolor['perte']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.perte)' style='cursor:pointer' /></th>
  <th width=35%>Rentabilit&eacute;</th>
  <th width='50px'align='center'><input type='textbox' name='renta' value='".$bbcolor['renta']."' size='7'></th>
  <th width='20px'><img src=".FOLDER_ATTCK."/ColorPicker/color.gif alt='choix de la couleur' onClick='fctShow(document.form2.renta)' style='cursor:pointer' /></th>
</tr>";
echo "<tr>
  <td colspan='6' class='c' align='center'><input name='submitbb' type='submit' value='Envoyer'></td>
</tr>
";
echo "</table></form>";
echo "<br />";

// Formulaire "Base de Donnees"
echo "<form name='form3' style='margin:0px;padding:0px;' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'><center>";
echo "<table width='60%' border='0'>
<tr>
  <td class='c' colspan='5'>Base de données&nbsp;".help("mysql")."&nbsp;</td>
</tr>";
echo "<tr>";
// On récupère les dates des archives présentes dans la base de données par ordre croissant 
$query = "SELECT DISTINCT `archives_date` FROM ".TABLE_ATTAQUES_ARCHIVES." order by `archives_date`";
$result = $db->sql_query($query);
//reinitialise l'array
$ann=array();
while (list($date)=$db->sql_fetch_row($result))
  {$ann[]=$date;}
echo "<th colspan='5'>Vous avez des archives depuis ".date("M Y",$ann[0]).". A partir de quand souhaitez vous purger ?</th></tr><tr>";
// On affiche la liste des dates présentes avec un checkbox
$count=0;
for($i=0; $i<count($ann); $i++)
  {
  echo "<th><input type='radio' name='purge' value='".$ann[$i]."' > ".date("M Y",$ann[$i])."</th>";
  $count += 1;
  // on limite à 5 cases par ligne pour la mise en forme
  if (($count/5)== (intval($count/5)))
    {
    echo "</tr><tr>";
    }
  }
// Pour la beauté du geste, si le nb de case ne tombe pas juste, on complète par des cases vides
if (($count/5) != (intval($count/5)))
    {
    for ($i=1; $i<=(((intval($count/5)+1)*5)-$count); $i++)
      {
      echo "<th>&nbsp;</th>";
      }
    }
echo "</tr>";
echo "<tr><td colspan='5' class='c' align='center'><input name='submitpurg' type='submit' value='Envoyer'></td></tr>";
echo "</table></form>";
// Controle de la base de données
$query = "SELECT DISTINCT `archives_user_id` FROM ".TABLE_ATTAQUES_ARCHIVES." ORDER BY `archives_user_id`";
$result = $db->sql_query($query);
$arch=array();
while (list($data)=$db->sql_fetch_row($result))
  {$arch[]=$data;}
$query = "SELECT DISTINCT `attack_user_id` FROM ".TABLE_ATTAQUES_ATTAQUES." ORDER BY `attack_user_id`";
$result = $db->sql_query($query);
$attck=array();
while (list($data)=$db->sql_fetch_row($result))
  {$attck[]=$data;}
$query = "SELECT DISTINCT `recy_user_id` FROM ".TABLE_ATTAQUES_RECYCLAGES." ORDER BY `recy_user_id`";
$result = $db->sql_query($query);
$recy=array();
while (list($data)=$db->sql_fetch_row($result))
  {$recy[]=$data;}
$inval_id=0;
//récupère la liste des users actifs
$query="SELECT `user_id`,`user_active` from ".TABLE_USER." ORDER BY `user_id`";
$result=$db->sql_query($query);
$count = 0;
$id=array();
// Seul le userid m'interesse
while (list($userid,$actif)=$db->sql_fetch_row($result))
  {
  if ($actif != 0)
    {
    $membre[$count] = $userid;
    $count+=1;
    }
  }
//On vérifie si les ID trouvé dans la DB sont dans la liste des userid actifs
for ($i=0; $i<count($arch); $i++)
  {
  if (!in_array($arch[$i],$membre))
    {
    // si non, on sauvegarde l'ID trouvé avec le type de la table correspondant et on incrémente le compteur
    $id[]=array("archive",$arch[$i]);
    $inval_id++;
    }
  }
for ($i=0; $i<count($attck); $i++)
  {
  if (!in_array($attck[$i],$membre))
    {
    $id[]=array("attack",$attck[$i]);
    $inval_id++;
    }
  }
for ($i=0; $i<count($recy); $i++)
  {
  if (!in_array($recy[$i],$membre))
    {
    $id[]=array("recy",$recy[$i]);
    $inval_id++;
    }
  }
//Si j'ai trouvé des données orphelines et seulement dans ce cas, j'affiche un simple form
if ($inval_id > 0)
  {
  //On serialize l'array pour le transmettre par $_POST dans le form
  $trans_id=serialize($id);
  echo "<form name='form4' style='margin:0px;padding:0px;' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'><center>";
  echo "<table width='60%' border='0'><tr>";
  echo "<th>Des donn&eacute;es n'appartenant &agrave; aucun joueur actif ont &eacute;t&eacute; trouv&eacute;
 dans la base de donn&eacute;es</th>";
  echo "</tr><tr>";
  echo "<th> Souhaitez vous les supprimer ?";
  echo "<input name='val_id' type='hidden' value='".$trans_id."'>";
  echo "</th></tr>";
  echo "<tr><td class='c' align='center'><input name='submitid' type='submit' value='Supprimer'></td></tr>";
  echo "</table></form>";
  }

//Connexion Xtense2
echo "<form name='form5' style='margin:0px;padding:0px;' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'><center>";
echo "<table width='60%' border='0'>
<tr>
  <td class='c' colspan='5'>Xtense 2&nbsp;".help("xtense")."&nbsp;</td>
</tr>";
echo "<tr>";
//On vérifie que la table xtense_callbacks existe
if( ! $db->sql_numrows( $db->sql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
  {
  echo "<th>la barre Xtense2 semble ne pas &ecirc;tre install&eacute;e</th>";
  }else{
  // Si oui, on récupère le n° d'id du mod
  $query = "SELECT `id` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
  $result = $db->sql_query($query);
  $attack_id = $db->sql_fetch_row($result);
  $attack_id = $attack_id[0];
  // Maintenant on vérifie que le mod est déclaré dans la table
  $query = "SELECT `id` FROM ".$table_prefix."xtense_callbacks"." WHERE `mod_id`=".$attack_id;
  $result = $db->sql_query($query);
  // On doit avoir 2 entrées dans la table : une pour les RC, une pour les RR
  if ($db->sql_numrows($result) != 2)
    {
    echo "<th>Le module 'Gestion des Attaques' n'est pas enregistr&eacute; aupr&egrave;s de Xtense2</th>";
    echo "<tr>";
    echo "<th>Souhaitez vous &eacute;tablir la connexion ?</th>";
    echo "</tr>";
    echo "<tr><td class='c' align='center'><input name='submitxt2' type='submit' value='Connecter Xtense2'></td></tr>";
    }else{
    echo "<th>Le module 'Gestion des Attaques' est correctement enregistr&eacute; aupr&egrave;s de Xtense2</th>";
    }
  }
echo "</tr></table></form>";
echo "</center></fieldset>";
?>