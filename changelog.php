<?php
/**
* Changelog.php 
* @package Attaques
* @author Verit�/ericc
* @link http://www.ogsteam.fr
* @version 0.8j
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//D�finitions
global $db;

//On v�rifie que le mod est activ�
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8j :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Compatibilit� Xtense > 2.0b7</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8i :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Correction du footer. Le num�ro de version est pris dans la database</li>";
echo"<li>Modification de la gestion des couleurs dans les graphes statistiques</li>";
echo"<li>R�ecriture de l'import pour gameOgame et Xtense1</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8h :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Bug correction. Nom des tables en dur dans certainnes requ�tes SQL - Merci bozzo</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8e :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>petites corrections des histogrammes 3D</li>";
echo"<li>Ajout d'une page 'Statistiques' avec les graphes sur 7 jours et mensuel de tout les joueurs</li>";
echo"<li>Page Admin: Possibilit� de supprimer les anciennes archives</li>";
echo"<li>Page Admin: D�tection et suppression des rapports orphelins (joueurs supprim�s ou inactifs)</li>";
echo"<li>Connection avec Xtense2: Rapports de combats et de recyclages remont�s automatiquement</li>";
echo"<li>Page Admin: D�tection de Xtense2 et connection</li>";
echo"<li>A l'installation la pr�sence de la table mod_config est d�tect� et si non pr�sente celle-ci est cr��</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8d :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Correction du script de g�n�ration des histogrammes 3D pour support PHP4 (free.fr)</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8c :</u></font></b></legend>";
echo"<p align='left'><font size='2'><ul>";
echo"<li>Modification suite � r�apparition des coordonn�es dans les rapports de recyclages</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.8a :</u></font></b></legend>";
echo"<p align='left'><font size='2'>";
echo "<ul>";
echo"<li>Modification de la barre de menu</li>";
echo"<li>Ajout d'une page 'Admin'</li>";
echo"<li>Ajout d'un 'layer' pour am�liorer la lisibilit� du mod sur les fonds clairs</li>";
echo"<li>Possibilit� de d�sactiver le 'layer' dans la page 'Admin', et de modifier son pourcentage d'opacit�</li>";
echo"<li>Possibilit� de modifier les couleurs utilis�s dans les bbcodes avec s�lecteur de couleur en javascript</li>";
echo"<li>Ajout d'un 'historique mensuel' en barre histogramme 3D dans les pages 'Attaques','Recyclages' et 'Bilan'</li>";
echo"<li>Possibilit� de d�sactiver l'affichage de 'l'historique mensuel' dans la page 'Admin' (le graphe met plus de 3s � s'afficher)</li>";
echo"<li>Les pages 'Admin' et 'Changelog' n'apparaissent que pour les administrateurs</li>";
echo"<li>Pr�paration au support multi-langue</li>";
echo"<li>Prise en compte des attaques subies (dont vous �tes le d�fenseur)</li>";
echo"<li>Possibilit� de d�sactiver la prise en compte des attaques subies dans la page 'Admin'</li>";
echo"<li>Page Archive: affichage des mois archiv�s, clickable</li>";
echo"</ul></font></p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.7a :</u></font></b></legend>";
echo"<p align='left'>";
echo"Modification du RegEx pour import des rapports de combats<br/>";
echo"Corection de la quasi totalit� des erreurs de type Notice<br/>";
echo"Insertion des donn�es du RC re�ues par la barre Xtense dans le module gameOgame si celui ci est actif<br/>";
echo"Prise en compte de la version 0.6 dans la mise � jour<br/>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5h :</u></font></b></legend>";
echo"<p align='left'>";
echo"Correction d'erreurs dans la page de changelog et num�ro de version en pied de page<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5g :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilit� avec Ogame version 0.78.<br>";
echo"Prise en compte des [] autour des coordonn�es Attaquant/D�fenseurs<br />";
echo"Mise � 1:1:1 des coordon�es de recyclage en attendant quelles r�apparaissent dans les rapports.";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5f :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilit� avec Ogame au niveau des . dans les attaques<br>";
echo"<br><br>";
echo"Merci � oXid_Fox et � Santory2 pour avoir effectu� les modifs n�c�ssaires";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.5e :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Compatibilit� avec Ogame version 0.76.<br>";
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
echo"-Compatibilit� avec la barre Xtense pour l'envoie des RC.<br>";
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
echo"-Compatibilit� avec la barre de Naqdazar.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.4b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Correction des bugs d�couvert dans la 0.4.<br>";
echo"-Ajout d'un espace bilan.<br>";
echo"-lorsque l'on clique sur un lien pour changer la date, les donn�es sont recharg�es automatiquement. Plus besoin de cliquer en plus sur le bouton afficher.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.4 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Impossibilit� d'enregistrer deux fois la m�me attaque, ou le m�me recyclage.<br>";
echo"-Contr�le si la version acuelle est � jour<br>";
echo"-Plus grande libert� au niveau du choix des dates d'affichage.<br>";
echo"-Possibilit� de r�cup�rer les r�sultats et la liste des attaques en BBCode.<br>";
echo"-S�paration des attaques et des recyclages<br>";
echo"-Test de la pr�sence ou non des tables dans les fichiers install et uninstall<br>";
echo"-Ajout de l'aide via les tooltips.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.3 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des recyclages.<br>";
echo"-Ajout de graphiques sur la page attaques du mois.<br>";
echo"-Pour la mise � jour et la suppression, le mod est appel� par son param�tre GET. Il est donc possible de changer le nom du mod sans probl�me<br>";
echo"-Am�lioration du code.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.2b :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des prefixes des tables.<br>";
echo"-Correction de bugs mineurs.<br>";
echo"-S�curisation du mod.<br>";
echo"</p>";
echo"</fieldset>";
echo"<br>";
echo"<br>";

echo"<fieldset><legend><b><font color='#0080FF'><u>Version 0.2 :</u></font></b></legend>";
echo"<p align='left'>";
echo"-Prise en compte des pertes attaquant.<br>";
echo"-Les gains des attaques des mois pr�c�dent sont sauvegard�s.<br>";
echo"-Les chiffres sont affich�s avec un s�parateur de milliers.<br>";
echo"-Demande une confirmation avant de supprimer une attaque.<br>";
echo"-Am�lioration du code.<br>";
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
echo"Merci � calidian pour les tests qu'il a effectu�s.";

echo"<br/>";
?>
