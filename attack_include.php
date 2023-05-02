<?php

/**
 * Fonctions du modules
 * @package Attaques
 * @author Vérité - réécrit par ericc
 * @link http://www.ogsteam.eu
 * @version : 0.8a
 * dernière modification : 28 Dec 2007
 */

//-------------------------------------------------------------------------------------------------------------------
// Renvoi 1 si l'utilisateur est admin, coadmin ou manager
function IsUserAdmin()
{
    global $user_data;
    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) return 1;
    else return 0;
}

//-------------------------------------------------------------------------------------------------------------------

/**
 * Mod Configs: Add or updates a configuration option for the mod
 * @param string $param Name of the parameter
 * @param integer $user_id Id of the user
 * @param string $value Value of the parameter
 * @return array returns true if the parameter is correctly saved. false in other cases.
 */
function mod_set_user_option($user_id, $param, $value)
{
    global $db;

    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!check_var($user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $query = "REPLACE INTO `" . TABLE_MOD_USER_CFG . "`(`mod`, `user_id`, `config`, `value`) VALUES('Attaques' ," . $user_id . ", '" . $param . "' , '" . $value . "')";
    $result = $db->sql_query($query);
    return $result;
}

/**
 * Mod Configs: Add or updates a configuration option for the mod
 * @param integer $user_id Id of the user
 * @param string $param Name of the parameter
 * @return array returns true if the parameter is correctly saved. false in other cases.
 */
function mod_get_user_option($user_id, $param)
{
    global $db;

    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!check_var($user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $query = "SELECT `value` FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod`='Attaques' and `user_id`=" . $user_id . " and `config`='" . $param . "'";
    $result = $db->sql_query($query);

    if ($db->sql_numrows() > 0) {
        $userConfig = $db->sql_fetch_row($result);
        $userValue = $userConfig['value'];
        return $userValue;
    }

    return false;
}

/**
 * Mod Configs: Deletes a parameter for a mod and a user
 * @param string $param Name of the parameter
 * @param $user_id
 * @return void returns true if the parameter is correctly saved. false in other cases.
 */
function mod_del_user_option($user_id, $param)
{
    global $db;
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!check_var($user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $query = "DELETE FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod`='Attaques' and `user_id`=" . $user_id . " and `config`=" . $param;
    $result = $db->sql_query($query);
}
// Création du menu
/**
 * @param $pub_page
 */
function menu($pub_page)
{
    global $pages;
    // Definition des pages du module
    $i = -1;
    $pages['fichier'][++$i] = 'attaques';
    $pages['texte'][$i] = '&nbsp;Attaques&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'recyclages';
    $pages['texte'][$i] = '&nbsp;Recyclages&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'bilan';
    $pages['texte'][$i] = '&nbsp;Bilan&nbsp';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'bbcode';
    $pages['texte'][$i] = '&nbsp;Espace BBCode&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'archive';
    $pages['texte'][$i] = '&nbsp;Espace Archives&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'statistiques';
    $pages['texte'][$i] = '&nbsp;Statistiques&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'config';
    $pages['texte'][$i] = '&nbsp;Config&nbsp;';
    $pages['admin'][$i] = 0;

    $pages['fichier'][++$i] = 'admin';
    $pages['texte'][$i] = 'Admin';
    $pages['admin'][$i] = 1;

    //Construction du menu
    echo "  <table><tr align='center'>";
    for ($i = 0; $i < count($pages['fichier']); $i++) if (($pages['admin'][$i] && IsUserAdmin()) || (!$pages['admin'][$i])) if ($pub_page != $pages['fichier'][$i]) {
        echo "\t<td class='c' width='150' onclick=\"window.location = 'index.php?action=attaques&page=" . $pages['fichier'][$i] . "';\">";
        echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $pages['texte'][$i] . "</span></a></td>";
    } else
        echo "\t<th width='150'><a>" . $pages['texte'][$i] . "</a></th>";
    echo "\t\t</tr>\n\t\t</table>";
}
