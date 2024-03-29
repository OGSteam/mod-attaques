<?php

/**
 * uninstall.php
 *
 * @package Attaques
 * @author Verité modifié par ericc
 * @link http://www.ogsteam.eu
 * @version : 0.8a
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//Définitions
global $db;
global $table_prefix;
define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");
define("TABLE_ATTAQUES_ARCHIVES", $table_prefix . "attaques_archives");

//Suppression des paramètres de configuration et bbcodes
mod_del_all_option();
// Suppression des paramètres utilisateur
//mod_del_all_user_option(); To be managed by OGSpy

$mod_uninstall_name = "attaques";
$mod_uninstall_table = TABLE_ATTAQUES_ATTAQUES . ', ' . TABLE_ATTAQUES_RECYCLAGES . ', ' . TABLE_ATTAQUES_ARCHIVES;
uninstall_mod($mod_uninstall_name, $mod_uninstall_table);
