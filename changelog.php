<?php
/**
* Changelog.php 
* @package Attaques
* @author Verité/ericc
* @link http://www.ogsteam.fr
* @version 0.8j
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//Définitions
global $db;

//On vérifie que le mod est activé
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8j :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Compatibilité Xtense > 2.0b7</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8i :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Correction du footer. Le numéro de version est pris dans la database</li>";
echo"<li>Modification de la gestion des couleurs dans les graphes statistiques</li>";
echo"<li>Réecriture de l'import pour gameOgame et Xtense1</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8h :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Bug correction. Nom des tables en dur dans certainnes requètes SQL - Merci bozzo</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8e :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>petites corrections des histogrammes 3D</li>";
echo"<li>Ajout d'une page 'Statistiques' avec les graphes sur 7 jours et mensuel de tout les joueurs</li>";
echo"<li>Page Admin: Possibilité de supprimer les anciennes archives</li>";
echo"<li>Page Admin: Détection et suppression des rapports orphelins (joueurs supprimés ou inactifs)</li>";
echo"<li>Connection avec Xtense2: Rapports de combats et de recyclages remontés automatiquement</li>";
echo"<li>Page Admin: Détection de Xtense2 et connection</li>";
echo"<li>A l'installation la présence de la table mod_config est détecté et si non présente celle-ci est créé</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8d :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Correction du script de génération des histogrammes 3D pour support PHP4 (free.fr)</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8c :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Modification suite à réapparition des coordonnées dans les rapports de recyclages</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8a :</u></font></b></legend>";
echo"<p align='left'><font size='2'>";
echo "<ul>";
echo"<li>Modification de la barre de menu</li>";
echo"<li>Ajout d'une page 'Admin'</li>";
echo"<li>Ajout d'un 'layer' pour améliorer la lisibilité du mod sur les fonds clairs</li>";
echo"<li>Possibilité de désactiver le 'layer' dans la page 'Admin', et de modifier son pourcentage d'opacité</li>";
echo"<li>Possibilité de modifier les couleurs utilisés dans les bbcodes avec sélecteur de couleur en javascript</li>";
echo"<li>Ajout d'un 'historique mensuel' en barre histogramme 3D dans les pages 'Attaques','Recyclages' et 'Bilan'</li>";
echo"<li>Possibilité de désactiver l'affichage de 'l'historique mensuel' dans la page 'Admin' (le graphe met plus de 3s à s'afficher)</li>";
echo"<li>Les pages 'Admin' et 'Changelog' n'apparaissent que pour les administrateurs</li>";
echo"<li>Préparation au support multi-langue</li>";
echo"<li>Prise en compte des attaques subies (dont vous êtes le défenseur)</li>";
echo"<li>Possibilité de désactiver la prise en compte des attaques subies dans la page 'Admin'</li>";
echo"<li>Page Archive: affichage des mois archivés, clickable</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.7a :</u></font></b></legend>";
echo"<p align='left'>";
echo"Modification du RegEx pour import des rapports de combats<br/>";
echo"Corection de la quasi totalité des erreurs de type Notice<br/>";
echo"Insertion des données du RC reçues par la barre Xtense dans le module gameOgame si celui ci est actif<br/>";
echo"Prise en compte de la version 0.6 dans la mise à jour<br/>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5h :</u></font></b></legend>";
echo"<p align='left'>";
echo"Correction d'erreurs dans la page de changelog et numéro de version en pied de page<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5g :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilité avec Ogame version 0.78.<br>";
echo"Prise en compte des [] autour des coordonnées Attaquant/Défenseurs<br />";
echo"Mise à 1:1:1 des coordonées de recyclage en attendant quelles réapparaissent dans les rapports.";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5f :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilité avec Ogame au niveau des . dans les attaques<br>";
echo"<br><br>";
echo"Merci à oXid_Fox et à Santory2 pour avoir effectué les modifs nécéssaires";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5e :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilité avec Ogame version 0.76.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5d :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction des bugs de formulaire.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5c :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilité avec la barre Xtense pour l'envoie des RC.<br>";
echo"-Onglets du menu en liens.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction des erreurs de la 0.5<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction du bug de sauvegarde des resultats<br>";
echo"-Compatibilité avec la barre de Naqdazar.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.4b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction des bugs découvert dans la 0.4.<br>";
echo"-Ajout d'un espace bilan.<br>";
echo"-lorsque l'on clique sur un lien pour changer la date, les données sont rechargées automatiquement. Plus besoin de cliquer en plus sur le bouton afficher.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.4 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Impossibilité d'enregistrer deux fois la même attaque, ou le même recyclage.<br>";
echo"-Contrôle si la version acuelle est à jour<br>";
echo"-Plus grande liberté au niveau du choix des dates d'affichage.<br>";
echo"-Possibilité de récupérer les résultats et la liste des attaques en BBCode.<br>";
echo"-Séparation des attaques et des recyclages<br>";
echo"-Test de la présence ou non des tables dans les fichiers install et uninstall<br>";
echo"-Ajout de l'aide via les tooltips.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.3 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des recyclages.<br>";
echo"-Ajout de graphiques sur la page attaques du mois.<br>";
echo"-Pour la mise à jour et la suppression, le mod est appelé par son paramètre GET. Il est donc possible de changer le nom du mod sans problème<br>";
echo"-Amélioration du code.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.2b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des prefixes des tables.<br>";
echo"-Correction de bugs mineurs.<br>";
echo"-Sécurisation du mod.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.2 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des pertes attaquant.<br>";
echo"-Les gains des attaques des mois précédent sont sauvegardés.<br>";
echo"-Les chiffres sont affichés avec un séparateur de milliers.<br>";
echo"-Demande une confirmation avant de supprimer une attaque.<br>";
echo"-Amélioration du code.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";


echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.1b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction d'un bug au niveau des formulaires.<br>";
echo"-Correction du code.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.1 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Sortie du mod gestion des attaques.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";
echo"Merci à calidian pour les tests qu'il a effectués.";

echo"<br/>";
?>
