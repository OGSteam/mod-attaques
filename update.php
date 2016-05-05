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

// Puis on change le numéro de version
$mod_folder = "attaques";
$mod_name = "attaques";
update_mod($mod_folder, $mod_name);