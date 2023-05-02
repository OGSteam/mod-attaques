<?php

/**
 * index.php
 *
 * @package Attaques
 * @author Verité - réécrit par ericc
 * @link http://www.ogsteam.eu
 * @version : 0.8a
 */
//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//On vérifie que le mod est activé
$query = "SELECT `active`,`root` FROM `" . TABLE_MOD . "` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die('Mod désactivé !');
$result = $db->sql_query($query);
list($active, $root) = $db->sql_fetch_row($result);

// définition du dossier du modules
define('FOLDER_ATTCK', 'mod/' . $root);
//Definition des tables du module
define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");
define("TABLE_ATTAQUES_ARCHIVES", $table_prefix . "attaques_archives");
if (!defined("TABLE_MOD_USER_CFG"))
    define("TABLE_MOD_USER_CFG", $table_prefix . "mod_user_config");


//récupération des paramètres de config
$config = mod_get_option('config');
$config = json_decode($config, true);

// Appel des fonctions du module
include(FOLDER_ATTCK . "/attack_include.php");
/**
 *Récupère le fichier de langue pour la langue approprié
 */
if (!empty($server_config['language'])) {
    if (is_dir(FOLDER_ATTCK . "/languages/" . $server_config['language'])) {
        require_once(FOLDER_ATTCK . "/languages/" . $server_config['language'] . "/lang_main.php");
        require_once(FOLDER_ATTCK . "/languages/" . $server_config['language'] . "/help.php");
    } else {
        require_once(FOLDER_ATTCK . "/languages/french/lang_main.php");
        require_once(FOLDER_ATTCK . "/languages/french/help.php");
    }
} else {
    if (!is_dir(FOLDER_ATTCK . "/languages/french")) {
        echo "Retélécharger le mod via : <a href='http://www.ogsteam.eu/downloadmod.php?mod=Attaques'>Zip link</a><br />\n";
        exit;
    } else {
        require_once(FOLDER_ATTCK . "/languages/french/lang_main.php");
        require_once(FOLDER_ATTCK . "/languages/french/help.php");
    }
}

// Entête du site
require_once("views/page_header.php");
// Insertion du css pour layer transparent si valider dans la configuration
if ($config['layer'] == 1) {
    include(FOLDER_ATTCK . "/css.php");
}
//Menu
// Si la page a afficher n'est pas définie, on affiche la première
if (!isset($pub_page)) $pub_page = "attaques";
menu($pub_page);

// Affichage du layer transparent
echo "<div class='attack_box'><div class='attack_box_background'> </div> <div class='attack_box_contents'>";
//On  affiche de la page demandée
switch ($pub_page) {
    case "bilan":
        include("bilan.php");
        break;
    case "bbcode":
        include("bbcode.php");
        break;
    case "archive":
        include("archives.php");
        break;
    case "statistiques":
        include("statistiques.php");
        break;
    case "recyclages":
        include("recyclages.php");
        break;
    case "admin":
        include("admin.php");
        break;
    case "changelog":
        include("changelog.php");
        break;
    case "config":
        include("config_user.php");
        break;
    default:
        include("attaques.php");
        break;
}

// Fin du layer transparent
echo "</div></div>";
// Version number at the bottom of the page
require_once(FOLDER_ATTCK . "/footer.php");
echo "<br>";
//Insertion du bas de page d'OGSpy
require_once("views/page_tail.php");
