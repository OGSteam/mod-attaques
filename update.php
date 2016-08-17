<?php
/**
 * update.php
 *
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8j
 */
namespace Ogsteam\Ogspy;
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

global $db,$table_prefix;


// On vérifie que la table mod_user_config existe
if (!$db->sql_numrows($db->sql_query("SHOW TABLES LIKE '" . $table_prefix . "mod_user_config" . "'"))) 
{
	    // sinon on la crée 
    $query = "CREATE TABLE `" . $table_prefix . "mod_user_config` (
				`mod` VARCHAR(50) NOT NULL,
				`config` VARCHAR(255) NOT NULL,
				`user_id` INT(10) NOT NULL,
				`value` VARCHAR(255) NOT NULL,
			 PRIMARY KEY (`mod`, `config`, `user_id`),
			INDEX `fk_user_userid` (`user_id`),
			CONSTRAINT `fk_user_userid` FOREIGN KEY (`user_id`) REFERENCES `ogspy_user` (`user_id`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB
			;";
    $db->sql_query($query);
}

// Puis on change le numéro de version
$mod_folder = "attaques";
$mod_name = "attaques";
update_mod($mod_folder, $mod_name);