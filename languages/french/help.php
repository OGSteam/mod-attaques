<?php
/**
* help.php - Réutilisation de la fonction help() du fichier /includes/help.php . on ne fait que (re)définir des entrées dans le array
 * @package Attaques
 * @author Verité
 * @link http://www.ogsteam.fr
 * @version : 0.8a
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

global $key;
$lang["help_attaques_bbcode"] = "Pour insérer vos résultats sur un forum, selectionner le texte ci-dessous, copier le, puis coller le dans votre post.";
$lang["help_attaques_ajouter_attaque"] = "Pour ajouter une nouvelle attaque copiez le rapport de combat dans le formulaire ci-dessous, puis cliquez sur envoyer.";
$lang["help_attaques_changer_affichage"] = "Ici vous pouvez choisir la période d'affichage en cliquant sur les liens ou en entrant les dates manuellement.";
$lang["help_attaques_resultats"] = "Ici vous pouvez consulter les résultats en fonction de l'affichage choisi, et les graphiques correspondants.";
$lang["help_attaques_liste_attaques"] = "Ici vous pouvez voir la liste de vos attaques en fonction de l'affichage choisi.";
$lang["help_attaques_ajouter_recy"] = "Pour ajouter un nouveau recyclage copiez le rapport de recyclage dans le formulaire ci-dessous, puis cliquer sur envoyer.";
$lang["help_attaques_liste_recy"] = "Ici vous pouvez voir la liste de vos recyclages en fonction de l'affichage choisi.";
$lang["help_attaques_Administration"] = "Modifiez les paramètres du modules";
$lang["help_attaques_layer"] = "Permet d'afficher un fond semi-transparent au cas où l'image de fond de votre skin dimunuerais la lisibilité du module";
$lang["help_attaques_transparence"] = "Mettez ici le pourcentage de transparence souhaité.<br>0%= complètement transparent<br>100%= complètement opaque.";
$lang["help_attaques_bbcolor"] = "Vous pouvez modifier les couleurs utilisés dans les bbcodes de l'espace bbcode.";
$lang["help_attaques_diffusion_rapports"] = "Autorise les autres utilisateurs à voir vos rapports";
$lang["help_attaques_masquer_coord"] = "Masque les coordonn&eacute;es des attaques pour les autres joueurs";
$lang["help_attaques_xtense"] = "Lien avec le module Xtense pour la récupération des données";
$lang["help_attaques_mysql"] = "Etat de la base de donnnées";
