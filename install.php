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
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_ATTAQUES;
$db->sql_query($query);

//Si la table attaques_recyclage existe, on la supprime
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_RECYCLAGES;
$db->sql_query($query);

//Si la table gains_save existe, on la supprime
$query = "DROP TABLE IF EXISTS " . TABLE_ATTAQUES_ARCHIVES;
$db->sql_query($query);

//Ensuite, on crée la table attaques_attaques
$query = "CREATE TABLE " . TABLE_ATTAQUES_ATTAQUES . " (" . " attack_id INT NOT NULL AUTO_INCREMENT, " . " attack_user_id INT NOT NULL, " . " attack_coord VARCHAR(8) NOT NULL, " . " attack_date INT NOT NULL, " . " attack_metal BIGINT NOT NULL, " . " attack_cristal BIGINT NOT NULL, " . " attack_deut BIGINT NOT NULL, " . " attack_pertes BIGINT NOT NULL, " . " primary key ( attack_id )" . " )";
$db->sql_query($query);

//Puis la table attaques_recyclages
$query = "CREATE TABLE " . TABLE_ATTAQUES_RECYCLAGES . " (" . " recy_id INT NOT NULL AUTO_INCREMENT, " . " recy_user_id INT NOT NULL, " . " recy_coord VARCHAR(8) NOT NULL, " . " recy_date INT NOT NULL, " . " recy_metal BIGINT NOT NULL, " . " recy_cristal BIGINT NOT NULL, " . " primary key ( recy_id )" . " )";
$db->sql_query($query);

//Enfin la table attaques_archives
$query = "CREATE TABLE " . TABLE_ATTAQUES_ARCHIVES . " (" . " archives_id INT NOT NULL AUTO_INCREMENT, " . " archives_user_id INT NOT NULL, " . " archives_nb_attaques INT NOT NULL, " . " archives_date INT NOT NULL, " . " archives_metal BIGINT NOT NULL, " . " archives_cristal BIGINT NOT NULL, " . " archives_deut BIGINT NOT NULL, " . " archives_pertes BIGINT NOT NULL, " . " archives_recy_metal BIGINT NOT NULL, " . " archives_recy_cristal BIGINT NOT NULL, " . " primary key ( archives_id )" . " )";
$db->sql_query($query);

// on insère les valeurs de configuration par défaut
$sqldata = '{"transp":75,"layer":1,"defenseur":1,"histo":1}';
mod_set_option('config', $sqldata);

// on insère les valeurs bbcodes par défaut
$sqldata = '{"title":"#FFA500","m_g":"#00FF40","c_g":"#00FF40","d_g":"#00FF40","m_r":"#00FF40","c_r":"#00FF40","perte":"#FF0000","renta":"#00FF40"}';
mod_set_option('bbcodes', $sqldata);

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

// On vérifie que la table mod_user_config existe
if (!$db->sql_numrows($db->sql_query("SHOW TABLES LIKE '" . $table_prefix . "mod_user_config'")))
{
	    // sinon on la crée 
    $query = "CREATE TABLE `" . $table_prefix . "mod_user_config` (
				`mod` VARCHAR(50) NOT NULL,
				`config` VARCHAR(255) NOT NULL,
				`user_id` INT(11) NOT NULL,
				`value` VARCHAR(255) NOT NULL,
			 PRIMARY KEY (`mod`, `config`, `user_id`),
			 UNIQUE KEY `config` (`config`)
			)
			DEFAULT CHARSET = utf8";
    $db->sql_query($query);
}
