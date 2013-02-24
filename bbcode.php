<?php
/**
* bbcode.php 
 * @package Attaques
 * @author Verit� modifi� par ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8a
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
//On v�rifie que le mod est activ�
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

// Appel des Javascripts
echo"<script type='text/javascript' language='javascript' src='".FOLDER_ATTCK."/attack.js'></script>";

//D�finitions
global $db, $table_prefix;

// lecture des bbcodes dans la db
$query = "SELECT value FROM `".TABLE_MOD_CFG."` WHERE `mod`='Attaques' and `config`='bbcodes'";
$result = $db->sql_query($query);
$bbcolor = $db->sql_fetch_row($result);
$bbcolor=unserialize($bbcolor[0]);

//Gestion des dates
$date = date("j");
$mois = date("m");
$annee = date("Y");
$septjours = $date-7;
$yesterday = $date-1;

if($septjours < 1) $septjours = 1;
if($yesterday < 1) $yesterday = 1;

//Si les dates d'affichage ne sont pas d�finies, on affiche par d�faut les attaques du jours,
if(!isset($pub_date_from)) $pub_date_from = mktime(0, 0, 0, $mois, $date, $annee);
else $pub_date_from = mktime(0, 0, 0, $mois, $pub_date_from, $annee);

if(!isset($pub_date_to)) $pub_date_to = mktime(23, 59, 59, $mois, $date, $annee);
else $pub_date_to = mktime(23, 59, 59, $mois, $pub_date_to, $annee);

$pub_date_from = intval($pub_date_from);
$pub_date_to = intval($pub_date_to);

//Requete pour afficher la liste des attaques 
$query = "SELECT attack_coord, attack_date, attack_metal, attack_cristal, attack_deut, attack_pertes, attack_id FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id=".$user_data["user_id"]." AND attack_date BETWEEN ".$pub_date_from." and ".$pub_date_to."  ORDER BY attack_date DESC,attack_id DESC";
$attaques = $db->sql_query($query);

//Requete pour afficher la liste des recyclages
$query = "SELECT recy_coord, recy_date, recy_metal, recy_cristal, recy_id FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data["user_id"]." AND recy_date BETWEEN ".$pub_date_from." and ".$pub_date_to."  ORDER BY recy_date DESC,recy_id DESC";
$recyclages = $db->sql_query($query);

//Requete pour obtenir les gains des attaques
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes) FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id=".$user_data["user_id"]." AND attack_date BETWEEN ".$pub_date_from." and ".$pub_date_to." GROUP BY attack_user_id"; 
$resultgains = $db->sql_query($query);

//Requete pour obtenir les gains des recyclages
$query = "SELECT SUM(recy_metal), SUM(recy_cristal) FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data["user_id"]." AND recy_date BETWEEN ".$pub_date_from." and ".$pub_date_to." GROUP BY recy_user_id";
$resultgains_recy = $db->sql_query($query);

//On recup�re le nombre d'attaques
$nb_attack = mysql_num_rows($attaques);

//On recup�re le nombre de recyclages
$nb_recy = mysql_num_rows($recyclages);

//On r�cup�re la date au bon format
$pub_date_from = strftime("%d %b %Y", $pub_date_from);
$pub_date_to = strftime("%d %b %Y", $pub_date_to);

//Cr�ation du field pour choisir l'affichage (attaque du jour, de la semaine ou du mois
echo"<fieldset><legend><b><font color='#0080FF'>Date d'affichage des attaques et des recyclages en BBCode ";
echo help("changer_affichage");
echo"</font></b></legend>";

echo"Afficher mes attaques et mes recyclages en BBCode : ";
echo"<form action='index.php?action=attaques&page=bbcode' method='post' name='date'>";
echo"du : <input type='text' name='date_from' id='date_from' size='11' maxlength='2' value='$pub_date_from' /> ";
echo"au : ";
echo"<input type='text' name='date_to' id='date_to' size='11' maxlength='2' value='$pub_date_to' />";
echo"<br>";
?>		
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $date; ?>'); setDateTo('<?php echo $date; ?>');  valid();">du jour</a> |
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $yesterday; ?>'); setDateTo('<?php echo $yesterday; ?>'); valid();">de la veille</a> | 
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $septjours ; ?>'); setDateTo('<?php echo $date; ?>'); valid();">des 7 derniers jours</a> |
<a href="#haut" onclick="javascript: setDateFrom('01'); setDateTo('<?php echo $date; ?>'); valid();">du mois</a>
<?php
echo"<br><br>";
echo"<input type='submit' value='Afficher' name='B1'></form>";
echo"</fieldset>";
echo"<br><br>";

$bbcode = "[color=".$bbcolor['title']."] [b]Liste des attaques de ".$user_data['user_name']."[/b] [/color]\n";
$bbcode .="du ".$pub_date_from." au ".$pub_date_to."\n\n";

//R�sultat requete
while (list($attack_coord, $attack_date, $attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($attaques))
{	
	$attack_date = strftime("%d %b %Y � %Hh%M", $attack_date);
	$attack_metal = number_format($attack_metal, 0, ',', ' ');
	$attack_cristal = number_format($attack_cristal, 0, ',', ' ');
	$attack_deut = number_format($attack_deut, 0, ',', ' ');
	$attack_pertes = number_format($attack_pertes, 0, ',', ' ');
	$bbcode .="Le ".$attack_date." victoire en ".$attack_coord.".\n";
	$bbcode .="[color=".$bbcolor['m_g']."]".$attack_metal."[/color] de m�tal, [color=".$bbcolor['c_g']."]".$attack_cristal."[/color] de cristal et [color=".$bbcolor['d_g']."]".$attack_deut."[/color] de deuterium ont �t� rapport�s.\n";
	$bbcode .="Les pertes s'�l�vent � [color=".$bbcolor['perte']."]".$attack_pertes."[/color].\n\n";
}
$bbcode .="[url=http://www.ogsteam.fr/forums/sujet-1358-mod-gestion-attaques]G�n�r� par le module de gestion des attaques[/url]";


//Cr�ation du field pour voir la liste des attaques en BBCode
echo"<fieldset><legend><b><font color='#0080FF'>Liste des attaques en BBCode du ".$pub_date_from." au ".$pub_date_to." ";
echo help("bbcode");
echo"</font></b></legend>";
echo"<p align='left'><a href='#haut' onclick='selectionner()'>Selectionner</a></p>";
echo"<form method='post'><textarea rows='7' cols='15' id='bbcode'>$bbcode</textarea></form></fieldset>";
echo"<br>";
echo"<br>";
echo"</fieldset>";

$bbcode = "[color=".$bbcolor['title']."][b]Liste des recyclages de ".$user_data['user_name']."[/b] [/color]\n";
$bbcode .="du ".$pub_date_from." au ".$pub_date_to."\n\n";

//R�sultat requete
while (list($recy_coord, $recy_date, $recy_metal, $recy_cristal,) = $db->sql_fetch_row($recyclages))
{	
	$recy_date = strftime("%d %b %Y � %Hh%M", $recy_date);
	$recy_metal = number_format($recy_metal, 0, ',', ' ');
	$recy_cristal = number_format($recy_cristal, 0, ',', ' ');
	$bbcode .="Le ".$recy_date." recyclage en ".$recy_coord.".\n";
	$bbcode .="[color=".$bbcolor['m_r']."]".$recy_metal."[/color] de m�tal, [color=".$bbcolor['c_r']."]".$recy_cristal."[/color] de cristal ont �t� recycl�s.\n\n";
}
$bbcode .="[url=http://www.ogsteam.fr/forums/sujet-1358-mod-gestion-attaques]G�n�r� par le module de gestion des attaques[/url]";


//Cr�ation du field pour voir la liste des recyclages en BBCode
echo"<fieldset><legend><b><font color='#0080FF'>Liste des recyclages en BBCode du ".$pub_date_from." au ".$pub_date_to." ";
echo help("bbcode");
echo"</font></b></legend>";
echo"<p align='left'><a href='#haut' onclick='selectionner2()'>Selectionner</a></p>";
echo"<form method='post'><textarea rows='7' cols='15' id='bbcode2'>$bbcode</textarea></form></fieldset>";
echo"<br>";
echo"<br>";
echo"</fieldset>";

//R�sultat requetes
list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains);
list($recy_metal, $recy_cristal) = $db->sql_fetch_row($resultgains_recy);	
	
//Calcul des gains totaux
$totalgains=$attack_metal+$attack_cristal+$attack_deut+$recy_metal+$recy_cristal;

//Calcul de la rentabilit�
$renta = $totalgains-$attack_pertes;

//Formatage
$attack_metal = number_format($attack_metal, 0, ',', ' ');
$attack_cristal = number_format($attack_cristal, 0, ',', ' ');
$attack_deut = number_format($attack_deut, 0, ',', ' ');
$totalgains = number_format($totalgains, 0, ',', ' ');
$attack_pertes = number_format($attack_pertes, 0, ',', ' ');
$recy_metal = number_format($recy_metal, 0, ',', ' ');
$recy_cristal = number_format($recy_cristal, 0, ',', ' ');
$renta = number_format($renta, 0, ',', ' ');

//On pr�pare les resultats au format bbcode
$bbcode = "[color=".$bbcolor['title']."][b]R�sultats des attaques et recyclages de ".$user_data['user_name']."[/b] [/color]\n";
$bbcode .="du ".$pub_date_from." au ".$pub_date_to."\n\n";
$bbcode .="Nombre d'attaques durant cette periode : ".$nb_attack."\n\n";
$bbcode .="M�tal gagn� : [color=".$bbcolor['m_g']."]".$attack_metal."[/color]\n";
$bbcode .="Cristal gagn� : [color=".$bbcolor['c_g']."]".$attack_cristal."[/color]\n";
$bbcode .="Deuterium gagn� : [color=".$bbcolor['d_g']."]".$attack_deut."[/color]\n\n";
$bbcode .="Total des ressources gagn�es : [color=".$bbcolor['renta']."]".$totalgains."[/color]\n";
$bbcode .="Total des pertes attaquant : [color=".$bbcolor['perte']."]".$attack_pertes."[/color]\n\n";
$bbcode .="Nombre de recyclages durant cette periode : ".$nb_recy."\n\n";
$bbcode .="Metal recycl� : [color=".$bbcolor['m_r']."]".$recy_metal."[/color]\n";
$bbcode .="Cristal recycl� : [color=".$bbcolor['c_r']."]".$recy_cristal."[/color]\n\n";
if ($renta > 0) $bbcode .="Rentabilit� : [color=".$bbcolor['renta']."]".$renta."[/color]\n\n";
else $bbcode .="Rentabilit� : [color=".$bbcolor['perte']."]".$renta."[/color]\n\n";
$bbcode .="[url=http://www.ogsteam.fr/forums/sujet-1358-mod-gestion-attaques]G�n�r� par le module de gestion des attaques[/url]";


//Cr�ation du field pour voir les gains en BBCode
echo"<fieldset><legend><b><font color='#0080FF'>Gains en BBCode du ".$pub_date_from." au ".$pub_date_to." ";
echo help("bbcode");
echo"</font></b></legend>";
echo"<p align='left'><a href='#haut' onclick='selectionner3()'>Selectionner</a></p>";
echo"<form method='post'><textarea rows='7' cols='15' id='bbcode3'>$bbcode</textarea></form></fieldset>";
echo"<br>";
echo"<br>";
echo"</fieldset>";
echo"<br/>";
?>