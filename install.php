<?php
/**
 * install.php
 *
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8j
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//Définitions
global $db;
global $table_prefix;
define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");
define("TABLE_ATTAQUES_ARCHIVES", $table_prefix . "attaques_archives");

$mod_folder = "attaques";
install_mod($mod_folder);

//Si la table attaques_attaques existe, on la supprime
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_ATTAQUES . "";
$db->sql_query($query);

//Si la table attaques_recyclage existe, on la supprime
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_RECYCLAGES . "";
$db->sql_query($query);

//Si la table gains_save existe, on la supprime
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_ARCHIVES . "";
$db->sql_query($query);

//Ensuite, on crée la table attaques_attaques
$query = "CREATE TABLE " . TABLE_ATTAQUES_ATTAQUES . " (" . " attack_id INT NOT NULL AUTO_INCREMENT, " . " attack_user_id INT NOT NULL, " . " attack_coord VARCHAR(8) NOT NULL, " . " attack_date INT NOT NULL, " . " attack_metal INT NOT NULL, " . " attack_cristal INT NOT NULL, " . " attack_deut INT NOT NULL, " . " attack_pertes INT NOT NULL, " . " primary key ( attack_id )" . " )";
$db->sql_query($query);

//Puis la table attaques_recyclages
$query = "CREATE TABLE " . TABLE_ATTAQUES_RECYCLAGES . " (" . " recy_id INT NOT NULL AUTO_INCREMENT, " . " recy_user_id INT NOT NULL, " . " recy_coord VARCHAR(8) NOT NULL, " . " recy_date INT NOT NULL, " . " recy_metal INT NOT NULL, " . " recy_cristal INT NOT NULL, " . " primary key ( recy_id )" . " )";
$db->sql_query($query);

//Enfin la table attaques_archives
$query = "CREATE TABLE " . TABLE_ATTAQUES_ARCHIVES . " (" . " archives_id INT NOT NULL AUTO_INCREMENT, " . " archives_user_id INT NOT NULL, " . " archives_nb_attaques INT NOT NULL, " . " archives_date INT NOT NULL, " . " archives_metal INT NOT NULL, " . " archives_cristal INT NOT NULL, " . " archives_deut INT NOT NULL, " . " archives_pertes INT NOT NULL, " . " archives_recy_metal INT NOT NULL, " . " archives_recy_cristal INT NOT NULL, " . " primary key ( archives_id )" . " )";
$db->sql_query($query);


//On vérifie que la table ogspy_mod_config
if (!$db->sql_numrows($db->sql_query("SHOW TABLES LIKE '" . $table_prefix . "mod_config" . "'"))) {
    // sinon on la crée 
    $query = "CREATE TABLE `ogspy_mod_config` (
  `mod` varchar(50) NOT NULL DEFAULT '',
  `config` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`mod`,`config`)
)";
    $db->sql_query($query);
}

// on insère les valeurs de configuration par défaut
$sqldata = 'a:4:{s:5:"layer";i:1;s:9:"defenseur";i:1;s:6:"transp";i:75;s:5:"histo";i:1;}';
$query = "INSERT INTO " . TABLE_MOD_CFG . " VALUES ('Attaques','config','" . $sqldata . "')";
$db->sql_query($query);
// on insère les valeurs bbcodes par défaut
$sqldata = 'a:8:{s:5:"title";s:7:"#FFA500";s:3:"m_g";s:7:"#00FF40";s:3:"c_g";s:7:"#00FF40";s:3:"d_g";s:7:"#00FF40";s:3:"m_r";s:7:"#00FF40";s:3:"c_r";s:7:"#00FF40";s:5:"perte";s:7:"#FF0000";s:5:"renta";s:7:"#00FF40";}';
$query = "INSERT INTO " . TABLE_MOD_CFG . " VALUES ('Attaques','bbcodes','" . $sqldata . "')";
$db->sql_query($query);

//On vérifie que la table xtense_callbacks existe (Xtense2)
if ($db->sql_numrows($db->sql_query("SHOW TABLES LIKE '" . $table_prefix . "xtense_callbacks" . "'"))) {
    // Si oui, on récupère le n° d'id du mod
    $query = "SELECT `id` FROM `" . TABLE_MOD . "` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
    $result = $db->sql_query($query);
    $attack_id = $db->sql_fetch_row($result);
    $attack_id = $attack_id[0];
    // on fait du nettoyage au cas ou 
    $query = "DELETE FROM `" . $table_prefix . "xtense_callbacks" . "` WHERE `mod_id`=" . $attack_id;
    $db->sql_query($query);
    // Insert les données pour récuperer les RC 
    $query = "INSERT INTO " . $table_prefix . "xtense_callbacks" . " ( `mod_id` , `function` , `type` )
                VALUES ( '" . $attack_id . "', 'attack_rc', 'rc')";
    $db->sql_query($query);
    // Insert les données pour récuperer les RR
    $query = "INSERT INTO " . $table_prefix . "xtense_callbacks" . " ( `mod_id` , `function` , `type` )
                VALUES ( '" . $attack_id . "', 'attack_rr', 'rc_cdr')";
    $db->sql_query($query);
}

?>