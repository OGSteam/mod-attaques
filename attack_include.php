<?php
/**
 * Fonctions du modules
 * @package Attaques
 * @author Vérité - réécrit par ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8a
 * dernière modification : 28 Dec 2007
 */
//------------------------------------------------------------------------------------------------------------------- 
// Renvoi 1 si l'utilisateur est admin, coadmin ou manager
function IsUserAdmin ()
{
    global $user_data;
    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) return 1; else return 0;
}

//-------------------------------------------------------------------------------------------------------------------
// Création du menu
function menu ($pub_page)
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

    $pages['fichier'][++$i] = 'admin';
    $pages['texte'][$i] = 'Admin';
    $pages['admin'][$i] = 1;

    $pages['fichier'][++$i] = 'changelog';
    $pages['texte'][$i] = '&nbsp;Changelog&nbsp;';
    $pages['admin'][$i] = 1;

    //Construction du menu
    echo "	<table><tr align='center'>";
    for ($i = 0; $i < count($pages['fichier']); $i++) if (($pages['admin'][$i] && IsUserAdmin()) || (!$pages['admin'][$i])) if ($pub_page != $pages['fichier'][$i]) {
        echo "\t<td class='c' width='150' onclick=\"window.location = 'index.php?action=attaques&page=" . $pages['fichier'][$i] . "';\">";
        echo "<a style='cursor:pointer'><font color='lime'>" . $pages['texte'][$i] . "</font></a></td>";
    } else
        echo "\t<th width='150'><a>" . $pages['texte'][$i] . "</a></th>";
    echo "\t\t</tr>\n\t\t</table>";
}

//-------------------------------------------------------------------------------------------------------------------


?>
