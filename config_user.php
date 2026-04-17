<?php

/**
 * config.php Page de configuration utilisateur
 *
 * @package Attaques
 * @author  ericc
 * @link https://www.ogsteam.eu
 * @version : 0.8e
 */


// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

global $db, $log, $table_prefix;

// lecture des configs dans la db
$user_config['diffusion_rapports'] = mod_get_user_option($user_data["id"], "diffusion_rapports");
$user_config['masquer_coord']      = mod_get_user_option($user_data["id"], "masquer_coord");

// Paramètres de configurations transmis par le form
if (isset($pub_submit)) {
    $diffusion = isset($pub_diffusion) && $pub_diffusion == true ? 1 : 0;
    mod_set_user_option($user_data['id'], 'diffusion_rapports', $diffusion);
    $user_config['diffusion_rapports'] = $diffusion;

    $masquer_coord = isset($pub_masquer_coord) && $pub_masquer_coord == true ? 1 : 0;
    mod_set_user_option($user_data['id'], 'masquer_coord', $masquer_coord);
    $user_config['masquer_coord'] = $masquer_coord;

    echo "<span style='color: #00FF40;'>Configuration sauvegard&eacute;e</span><br />";
}
// Fin paramètres de configuration


// cadre autour des paramètres
echo "<div class='og-msg'>";
echo "<h3 class='og-title'>Configuration " . help("attaques_user_config") . "</h3>";
echo "<div class='og-content'>";
// Formulaire des paramètres du module
echo "<form name='form1' action='index.php?action=attaques&page=config' enctype='multipart/form-data' method='post'>";
echo "<div class='attaques-filter-row'><b>Paramètres de visibilité</b></div>";
echo "<div class='attaques-filter-row'>";
echo "<label>Diffuser les rapports " . help("attaques_diffusion_rapports") . " : ";
echo "<input type='checkbox' name='diffusion' value='true' ";
if (isset($user_config['diffusion_rapports']) && $user_config['diffusion_rapports'] == 1) {
    echo 'checked=checked';
}
echo "></label>";
echo "</div>";
echo "<div class='attaques-filter-row'>";
echo "<label>Masquer les coordonnées " . help("attaques_masquer_coord") . " : ";
echo "<input type='checkbox' name='masquer_coord' value='true' ";
if (isset($user_config['masquer_coord']) && $user_config['masquer_coord'] == 1) {
    echo 'checked=checked';
}
echo "></label>";
echo "</div>";
echo "<div class='attaques-filter-row'>";
echo "<input name='submit' type='submit' value='Enregistrer' class='og-button'>";
echo "</div>";
echo "</form>";
echo "</div></div>";

