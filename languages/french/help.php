<?php
/**
* help.php - R�utilisation de la fonction help() du fichier /includes/help.php . on ne fait que (re)d�finir des entr�es dans le array
 * @package Attaques
 * @author Verit�
 * @link http://www.ogsteam.fr
 * @version : 0.8a
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//On v�rifie que le mod est activ�
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

global $key;
$help["bbcode"] = "Pour ins�rer vos r�sultats sur un forum, selectionner le texte ci-dessous, copier le, puis coller le dans votre post.";
$help["ajouter_attaque"] = "Pour ajouter une nouvelle attaque copiez le rapport de combat dans le formulaire ci-dessous, puis cliquez sur envoyer.";
$help["changer_affichage"] = "Ici vous pouvez choisir la p�riode d'affichage en cliquant sur les liens ou en entrant les dates manuellement.";
$help["resultats"] = "Ici vous pouvez consulter les r�sultats en fonction de l'affichage choisi, et les graphiques correspondants.";
$help["liste_attaques"] = "Ici vous pouvez voir la liste de vos attaques en fonction de l'affichage choisi.";
$help["ajouter_recy"] = "Pour ajouter un nouveau recyclage copiez le rapport de recyclage dans le formulaire ci-dessous, puis cliquer sur envoyer.";
$help["liste_recy"] = "Ici vous pouvez voir la liste de vos recyclages en fonction de l'affichage choisi.";
$help["Administration"] = "Modifiez les param�tres du modules";
$help["layer"] = "Permet d'affich� un fond semi-transparent au cas o� l'image de fond de votre skin dimunuerais la lisibilit� du module";
$help["transparence"] = "Mettez ici le pourcentage de transparence souhait�.<br>0%= compl�tement transparent<br>100%= compl�tement opaque.";
$help["bbcolor"] = "Vous pouvez modifier les couleurs utilis�s dans les bbcodes de l'espace bbcode<br>Cliquez sur les roues de couleur pour faire apparaitre un s�lecteur.";

?>