<?php
/**
* lang_main.php - Création du fichier de traduction en anglais
 * @package Attaques
 * @author Verité
 * @link http://www.ogsteam.fr
 * @version : 0.5h
 * @translation KyleCo76
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//On vérifie que le mod est activé
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

global $key;
$lang['MOD_ATTAQUES_LOSS_MSG'] = "Les pertes s'&eacute;lèvent à ";
$lang['MOD_ATTAQUES_VICTORY_COORD'] = "victoire en ";
$lang['MOD_ATTAQUES_ATTACK_LIST'] = "Liste des attaques de  ";
$lang['MOD_ATTAQUES_AU'] = "au ";
$lang['MOD_ATTAQUES_RESOURCE_PIE'] = "Métal_x_Cristal_x_Deutérium_x_Pertes";
$lang['MOD_ATTAQUES_GAINS_ATTAQUES'] = "Gains des Attaques";
$lang['MOD_ATTAQUES_GRAPHIQUE'] = "graphique";
$lang['MOD_ATTAQUES_GRAPHIQUE_DISPONSIBLE'] = "Pas de graphique disponible";
$lang['MOD_ATTAQUES_RESULTATS_ATTAQUE'] = "Résultats des attaques du ";
$lang['MOD_ATTAQUES_AFFICHER'] = "Afficher";
$lang['MOD_ATTAQUES_AFFICHER_ATTAQUES'] = "Afficher les attaques : ";
$lang['MOD_ATTAQUES_DU'] = "du ";
$lang['MOD_ATTAQUES_PARAMETRES_AFFICHAGE_ATTAQUES'] = "Paramètres d'affichage des attaques ";
$lang['MOD_ATTAQUES_DELETE_WRONG_USER'] = " a tenté de supprimer une attaque qui appartient à un autre utilisateurs dans le module de gestion des attaques ";
$lang['MOD_ATTAQUES_CANT_DELETE_ATTACK'] = "Vous n'avez pas le droit d'effacer cette attaque";
$lang['MOD_ATTAQUES_ATTAQUE_SUPPRIMEE'] = "L'attaque a bien été supprimée";
$lang['MOD_ATTAQUES_LOG_SUPPRIME'] = " supprime l'une de ses attaque dans le module de gestion des attaques";
$lang['MOD_ATTAQUES_DE'] = " de";
$lang['MOD_ATTAQUES_ATTAQUES'] = " attaque(s) ";
$lang['MOD_ATTAQUES_COORDONNEES'] = "Coordonnées";
$lang['MOD_ATTAQUES_DATE_ATTAQUE'] = "Date de l'Attaque";
$lang['MOD_ATTAQUES_METAL_GAGNE'] = "Métal Gagné";
$lang['MOD_ATTAQUES_CRISTAL_GAGNE'] = "Cristal Gagné";
$lang['MOD_ATTAQUES_DEUT_GAGNE'] = "Deut&eacute;rium Gagné";
$lang['MOD_ATTAQUES_PERTES_ATTAQUANT'] = "Pertes Attaquant";
$lang['MOD_ATTAQUES_SUPPRIMER'] = "Supprimer";
$lang['MOD_ATTAQUES_HISTORIQUE_MOIS'] = "Historique du mois";
$lang['MOD_ATTAQUES_QUANTITE'] = "Quantité";
$lang['MOD_ATTAQUES_DU_JOUR'] = "du jour";
$lang['MOD_ATTAQUES_DE_LA_VEILLE'] = "de la veille";
$lang['MOD_ATTAQUES_7_DERNIERS_JOURS'] = "des 7 derniers jours";
$lang['MOD_ATTAQUES_DU_MOIS'] = "du mois";
$lang['MOD_ATTAQUES_METAL'] = "M&eacute;tal";
$lang['MOD_ATTAQUES_CRISTAL'] = "Cristal";
$lang['MOD_ATTAQUES_DEUT'] = "Deut&eacute;rium";
$lang['MOD_ATTAQUES_GAINS'] = "Gains";
$lang['MOD_ATTAQUES_PERTES'] = "Pertes";
$lang['MOD_ATTAQUES_RENTABILITE'] = "Rentabilit&eacute;";
$lang['MOD_ATTAQUES_SUPPRIME_MOIS'] = "Vos attaques du ou des mois précédent(s) ont été supprimé(s). Seuls les gains restent accessibles dans la partie Espace Archives<br>La liste de vos attaques qui viennent d'être supprimées est consultable une dernière fois. Pensez à la sauvegarder !!!";
$lang['MOD_ATTAQUES_LISTE_ATTAQUES_01'] = "Liste de vos attaques du 01";
