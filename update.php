<?php
/**
* update.php 
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8j
 */

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

//Définitions
global $db;
global $table_prefix;
define("TABLE_ATTAQUES_ATTAQUES", $table_prefix."attaques_attaques");
define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix."attaques_recyclages");
define("TABLE_ATTAQUES_ARCHIVES", $table_prefix."attaques_archives");
define("TABLE_ATTACK_SAVE", $table_prefix."attack_save");

//On récupère la version actuel du mod	
$query = "SELECT version FROM ".TABLE_MOD." WHERE action='attack'";
$result = $db->sql_query($query);

list($version) = $db->sql_fetch_row($result);

if ($version == 0)
{
	$query = "SELECT version FROM ".TABLE_MOD." WHERE action='attaques'";
	$result = $db->sql_query($query);

	list($version) = $db->sql_fetch_row($result);
}

if ($version == "0.3")
{
	//Comme j'ai changé le parametre GET du mod, on commence par changer celui-ci
	$query  = "UPDATE ".TABLE_MOD." SET action='attaques', link='index.php' WHERE action='attack' LIMIT 1";
	$db->sql_query($query);
	
	//On commence par créer les nouvelles tables
	$query = "CREATE TABLE ".TABLE_ATTAQUES_ATTAQUES." ("
		. " attack_id INT NOT NULL AUTO_INCREMENT, "
		. " attack_user_id INT NOT NULL, "
		. " attack_coord VARCHAR(8) NOT NULL, "
		. " attack_date INT NOT NULL, "
		. " attack_metal INT NOT NULL, "
		. " attack_cristal INT NOT NULL, "
		. " attack_deut INT NOT NULL, "
		. " attack_pertes INT NOT NULL, "
		. " primary key ( attack_id )"
		. " )";
	$db->sql_query($query);

	$query = "CREATE TABLE ".TABLE_ATTAQUES_RECYCLAGES." ("
		. " recy_id INT NOT NULL AUTO_INCREMENT, "
		. " recy_user_id INT NOT NULL, "
		. " recy_coord VARCHAR(8) NOT NULL, "
		. " recy_date INT NOT NULL, "
		. " recy_metal INT NOT NULL, "
		. " recy_cristal INT NOT NULL, "
		. " primary key ( recy_id )"
		. " )";
	$db->sql_query($query);

	$query = "CREATE TABLE ".TABLE_ATTAQUES_ARCHIVES." ("
		. " archives_id INT NOT NULL AUTO_INCREMENT, "
		. " archives_user_id INT NOT NULL, "
		. " archives_nb_attaques INT NOT NULL, "
		. " archives_date INT NOT NULL, "
		. " archives_metal INT NOT NULL, "
		. " archives_cristal INT NOT NULL, "
		. " archives_deut INT NOT NULL, "
		. " archives_pertes INT NOT NULL, "
		. " archives_recy_metal INT NOT NULL, "
		. " archives_recy_cristal INT NOT NULL, "
		. " primary key ( archives_id )"
		. " )";
	$db->sql_query($query);

	//Puis on va récuperer les données qui sont actuellement enregistrées, et on va les replacer dans les nouvelles tables.

	//On commence par les resultats anterieurs
	$querry = "SELECT user_id, mois, annee, nb_attaques, gains_metal, gains_cristal, gains_deut, pertes, recy_metal, recy_cristal FROM ".TABLE_GAINS_SAVE."";
	$result = $db->sql_query($querry);
	
	while( list($user_id, $mois, $annee, $nb_attaques, $gains_metal, $gains_cristal, $gains_deut, $pertes, $recy_metal, $recy_cristal) = $db->sql_fetch_row($result) )
	{
		//On definit le timestamp
		$timestamp = mktime(0, 0, 0, $mois, 01, $annee);
		
		//On enregistre les données récupérer dans la nouvelle table
		$query = "INSERT INTO ".TABLE_ATTAQUES_ARCHIVES." ( `archives_id` , `archives_user_id` , `archives_nb_attaques` , `archives_date` , `archives_metal` , `archives_cristal` , `archives_deut` , `archives_pertes`, `archives_recy_metal`, `archives_recy_cristal` )
			VALUES (
				NULL , '$user_id', '$nb_attaques', '$timestamp', '$gains_metal', '$gains_cristal', '$gains_deut' , '$pertes', '$recy_metal', '$recy_cristal'
		)";
		$db->sql_query($query);
	}

	//Puis on s'occupe des attaques du mois et des recyclages
	$query = "SELECT user_id, coord_rc, date_rc, mois_rc, annee_rc, metal_rc, cristal_rc, deut_rc, pertes_rc, recy_metal_rc, recy_cristal_rc FROM ".TABLE_ATTACK_SAVE."";
	$result = $db->sql_query($query);

	while( list($user_id, $coord_rc, $date_rc, $mois_rc, $annee_rc, $metal_rc, $cristal_rc, $deut_rc, $pertes_rc, $recy_metal_rc, $recy_cristal_rc) = $db->sql_fetch_row($result) )
	{
		//On definit le timestamp
		$timestamp = mktime(0, 0, 0, $mois_rc, $date_rc, $annee_rc);
		
		//On enregistre les données récupérer dans la nouvelle table
		$query = "INSERT INTO ".TABLE_ATTAQUES_ATTAQUES." ( `attack_id` , `attack_user_id` , `attack_coord` , `attack_date` , `attack_metal` , `attack_cristal` , `attack_deut` , `attack_pertes` )
			VALUES (
				NULL , '$user_id', '$coord_rc', '$timestamp', '$metal_rc', '$cristal_rc', '$deut_rc', '$pertes_rc'
			)";
		$db->sql_query($query);
		
		if( ($recy_metal_rc != 0) || ($recy_cristal_rc != 0))
		{
		//On insere ces données dans la base de données
			$query = "INSERT INTO ".TABLE_ATTAQUES_RECYCLAGES." ( `recy_id` , `recy_user_id` , `recy_coord` , `recy_date` , `recy_metal` , `recy_cristal` )
				VALUES (
					NULL , '$user_id', '$coord_rc', '$timestamp', '$recy_metal_rc', '$recy_cristal_rc'
				)";
			$db->sql_query($query);
		}
	}

	//Pour chaque attaques, on a un recyclages
	
	//Puis on supprime les ancienes tables
	$query = "DROP TABLE IF EXISTS ".TABLE_ATTACK_SAVE.";";
	$db->sql_query($query);
	
	$query = "DROP TABLE IF EXISTS ".TABLE_GAINS_SAVE.";";
	$db->sql_query($query);

	//Pour finir on met la version du mod à jours
	$query  = "UPDATE ".TABLE_MOD." SET version='0.4' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.4";
}
if ($version == "0.4")
{
	//Comme j'ai changé le parametre root du mod on change celui-ci, et puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET root='Attaques', version='0.4b' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.4b";
}

if ($version == "0.4b")
{
	//On efface les bilans vides qui pourrait etre present suite à un bug dans la version 0.4b
	$query  = "DELETE FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE `archives_nb_attaques` = 0";
	$db->sql_query($query);
	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5";
	
}

if ($version == "0.5")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5b' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5b";
}

if ($version == "0.5b")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5c' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5c";
}

if ($version == "0.5c")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5d' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5d";
}

if ($version == "0.5d")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5e' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5e";
}

if ($version == "0.5e")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5f' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5f";
}
if ($version == "0.5f")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5g' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5g";
}
if ($version == "0.5g")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.5h' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.5h";
}
if ($version == "0.5h")
{	
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.7a' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.7a";
}
if ($version == "0.6" or $version == "0.6a" or $version == "0.6b" or $version == "0.6c" or $version == "0.6d" )
{	
	//On change le nom de la colonne attack_pertesA en attack_pertes
	$query = "ALTER TABLE ".TABLE_ATTAQUES_ATTAQUES." CHANGE attack_pertesA attack_pertes INT NOT NULL";
	$db->sql_query($query);

	//On change le nom de colonne archives_pertesA en archive_pertes
	$query = "ALTER TABLE ".TABLE_ATTAQUES_ARCHIVES." CHANGE archives_pertesA archives_pertes INT NOT NULL";
	$db->sql_query($query);

	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.7a' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.7a";
}
if ($version == "0.7a")
{	
  // on insère les valeurs de configuration par défaut
  $sqldata='a:4:{s:5:"layer";i:1;s:9:"defenseur";i:1;s:6:"transp";i:75;s:5:"histo";i:1;}';
  $query = "INSERT INTO ".TABLE_MOD_CFG." VALUES ('Attaques','config','".$sqldata."')";
  $db->sql_query($query);
  // on insère les valeurs bbcodes par défaut
  $sqldata='a:8:{s:5:"title";s:7:"#FFA500";s:3:"m_g";s:7:"#00FF40";s:3:"c_g";s:7:"#00FF40";s:3:"d_g";s:7:"#00FF40";s:3:"m_r";s:7:"#00FF40";s:3:"c_r";s:7:"#00FF40";s:5:"perte";s:7:"#FF0000";s:5:"renta";s:7:"#00FF40";}';
  $query = "INSERT INTO ".TABLE_MOD_CFG." VALUES ('Attaques','bbcodes','".$sqldata."')";
  $db->sql_query($query);
	//Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8a' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8a";	
}
if ($version == "0.8a")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8c' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8c";
  }	
if ($version == "0.8c")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8d' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8d";
  }
if ($version == "0.8d")
  {
  //On vérifie que la table xtense_callbacks existe (Xtense2)
  if( mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$table_prefix."xtense_callbacks"."'")))
    {
    // Si oui, on récupère le n° d'id du mod
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
    //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8e' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8e";
  }
  if ($version == "0.8e")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8f' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8f";
  }
  if ($version == "0.8f")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8g' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8g";
  }
  if ($version == "0.8g")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8h' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8h";
  }
  if ($version == "0.8h")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8i' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8i";
  }
  if ($version == "0.8i")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8j' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8j";
  }
  if ($version == "0.8j")
  {
  //Puis on change la version
	$query  = "UPDATE ".TABLE_MOD." SET version='0.8.11' WHERE action='attaques' LIMIT 1";
	$db->sql_query($query);
	
	$version = "0.8.11";
  }
  if ($version = "0.8.11")
  {
  // Puis on change le numéro de version
  $mod_folder = "attaques";
  $mod_name = "attaques";
  update_mod($mod_folder,$mod_name);
  }
  
?>