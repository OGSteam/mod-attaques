<?php
/**
 * update.php
 *
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8j
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

global $db,$table_prefix;

//On récupère la version actuelle du mod
$query = "SELECT id, version FROM ".TABLE_MOD." WHERE action='attaques'";
$result = $db->sql_query($query);
list($mod_id, $version) = $db->sql_fetch_row($result);

$query = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "mod_user_config` (
            `mod` VARCHAR(50) NOT NULL,
            `config` VARCHAR(255) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `value` VARCHAR(255) NOT NULL,
         PRIMARY KEY (`mod`, `config`, `user_id`),
         UNIQUE KEY `config` (`config`)
        )
        DEFAULT CHARSET = utf8";

$db->sql_query($query);

if (version_compare( $version , '2.0.0.', '<')) {

    // on insère les valeurs de configuration par défaut car changement de format de données
    $config = '{"transp":75,"layer":1,"defenseur":1,"histo":1}';
    mod_set_option('config', $config);

// on insère les valeurs bbcodes par défaut
    $bbcodes = '{"title":"#FFA500","m_g":"#00FF40","c_g":"#00FF40","d_g":"#00FF40","m_r":"#00FF40","c_r":"#00FF40","perte":"#FF0000","renta":"#00FF40"}';
    mod_set_option('bbcodes', $bbcodes);
}


// Puis on change le numéro de version
$mod_folder = "attaques";
$mod_name = "Gestion des attaques";
update_mod($mod_folder, $mod_name);