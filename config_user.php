<?php
/**
 * config.php Page de configuration utilisateur
 *
 * @package Attaques
 * @author  ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8e
 */

namespace Ogsteam\Ogspy;

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

global $db, $table_prefix;

// lecture des configs dans la db
$user_config = \Ogsteam\Ogspy\mod_get_user_option($user_data["user_id"]);

// Paramètres de configurations transmis par le form
if (isset($pub_submit)) {
		$diffusion = isset($pub_diffusion) && $pub_diffusion == true ? 1 : 0;
        \Ogsteam\Ogspy\mod_set_user_option('diffusion_rapports', $user_data['user_id'], $diffusion);
		$user_config['diffusion_rapports'] = $diffusion;
		    
		$masquer_coord = isset($pub_masquer_coord) && $pub_masquer_coord == true ? 1 : 0;
        \Ogsteam\Ogspy\mod_set_user_option('masquer_coord', $user_data['user_id'], $masquer_coord);
		$user_config['masquer_coord'] = $masquer_coord;

    echo "<span  style=\"font-size: x-small; color: #00FF40; \">Configuration sauvegardée</span><br />";
}
// Fin paramètres de configuration


// cadre autour des paramètres
echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Configuration ";
echo help("user_config");
echo "</font></b></legend>";
// Formulaire des paramètres du module
echo "<form name='form1' style=\"margin:0px;padding:0px; alignment: center;\" action='index.php?action=attaques&page=config' enctype='multipart/form-data' method='post'>";
echo "<table width='60%' border='0'>
<tr>
<td class='c' colspan='2'>Paramètres de visibilité</td>
</tr>
<tr>
<th>Diffuser les rapports " . help("diffusion_rapports") . " : </th>
<th><input type='checkbox' name='diffusion' value='true' ";
if (isset($user_config['diffusion_rapports']) && $user_config['diffusion_rapports'] == 1) {
    echo 'checked=checked';
}
echo "></th>
</tr>
<tr>
<th> Masquer les coordonnees " . help("masquer_coord") . "&nbsp;: </th>
<th><input type='checkbox' name='masquer_coord' value='true' ";
if (isset($user_config['masquer_coord']) && $user_config['masquer_coord'] == 1) {
    echo 'checked=checked';
}
echo "></th>
</tr>
<tr>
<td class='c' colspan='2'>&nbsp;</td>
</tr>
<tr>
<tr>
<td colspan='2' class='c' align='center'><input name='submit' type='submit' value='Envoyer'></td>
</tr>
";
echo "</table></form>";
echo "</center></fieldset>";
