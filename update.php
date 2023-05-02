<?php

/**
 * update.php
 *
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.eu
 * @version : 2.1.0
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

//Définitions
global $db;
global $table_prefix;
define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");
define("TABLE_ATTAQUES_ARCHIVES", $table_prefix . "attaques_archives");

//On récupère la version actuelle du mod
$query = "SELECT id, version FROM " . TABLE_MOD . " WHERE action='attaques'";
$result = $db->sql_query($query);
list($mod_id, $version) = $db->sql_fetch_row($result);


if (version_compare($version, '2.1.0.', '<')) {

    // on insère les valeurs de configuration par défaut car changement de format de données
    $config = '{"transp":75,"layer":1,"defenseur":1,"histo":1}';
    mod_set_option('config', $config);

    // on insère les valeurs bbcodes par défaut
    $bbcodes = '{"title":"#FFA500","m_g":"#00FF40","c_g":"#00FF40","d_g":"#00FF40","m_r":"#00FF40","c_r":"#00FF40","perte":"#FF0000","renta":"#00FF40"}';
    mod_set_option('bbcodes', $bbcodes);

    $requests = array();

    $requests[] = "ALTER TABLE " . TABLE_ATTAQUES_ATTAQUES . " MODIFY `attack_metal` BIGINT, MODIFY `attack_cristal` BIGINT, MODIFY `attack_deut` BIGINT,  MODIFY `attack_pertes` BIGINT";
    $requests[] = "ALTER TABLE " . TABLE_ATTAQUES_RECYCLAGES . " MODIFY `recy_metal` BIGINT, MODIFY `recy_cristal` BIGINT";
    $requests[] = "ALTER TABLE " . TABLE_ATTAQUES_ARCHIVES . " MODIFY `archives_metal` BIGINT, MODIFY `archives_cristal` BIGINT, MODIFY `archives_deut` BIGINT,  MODIFY `archives_pertes` BIGINT, MODIFY `archives_recy_metal` BIGINT, MODIFY `archives_recy_cristal` BIGINT";

    foreach ($requests as $request) {
        $db->sql_query($request);
    }
}

// Puis on change le numéro de version
$mod_folder = "attaques";
$mod_name = "Gestion des attaques";
update_mod($mod_folder, $mod_name);
