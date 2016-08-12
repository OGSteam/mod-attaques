<?php
/**
 * config.php Page de configuration utilisateur
 *
 * @package Attaques
 * @author  ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8e
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

global $db, $table_prefix;

// lecture des configs dans la db
$query = "SELECT config, value FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod`='Attaques' and `user_id`=" . $user_data["user_id"];
$result = $db->sql_query($query);
$user_config = array();
while($row = $db->sql_fetch_row($result))
	$user_config[$row[0]] = $row[1];

// Paramètres de configurations transmis par le form
if (isset($pub_submit)) {		
	$queries = array();
	$where = " WHERE `mod`='Attaques' and `user_id`=" . $user_data["user_id"] . " ";
    
		$diffusion = isset($pub_diffusion) && $pub_diffusion == true ? 1 : 0;
		if(isset($user_config['diffusion_rapports']))
			$queries[] = "UPDATE " . TABLE_MOD_USER_CFG . " SET value = '" . $diffusion . "'" . $where . " and `config`='diffusion_rapports'";
		else
			$queries[] = "INSERT INTO " . TABLE_MOD_USER_CFG . "(`mod`, `config`, `user_id`, `value`) VALUES ('Attaques', 'diffusion_rapports', ". $user_data["user_id"] . ", " . $diffusion . ")";
		$user_config['diffusion_rapports'] = $diffusion;
		    
		$masquer_coord = isset($pub_masquer_coord) && $pub_masquer_coord == true ? 1 : 0;
		if(isset($user_config['masquer_coord']))
			$queries[] = "UPDATE " . TABLE_MOD_USER_CFG . " SET value = '" . $masquer_coord . "'" . $where . " and `config`='masquer_coord'";
		else
			$queries[] = "INSERT INTO " . TABLE_MOD_USER_CFG . "(`mod`, `config`, `user_id`, `value`) VALUES ('Attaques', 'masquer_coord', ". $user_data["user_id"] . ", " . $masquer_coord . ")";				
		$user_config['masquer_coord'] = $masquer_coord;
	
		foreach($queries as $query)
			$db->sql_query($query);
    
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
