<?php
/**
* archives.php
* @package Attaques
* @author Verité modifié par ericc
* @link http://www.ogsteam.fr
* @version : 0.8a
*/
?>
<SCRIPT language="JavaScript">
function selectionner() {
document.getElementById('bbcode').select();
}
</SCRIPT>
<?php

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//On vérifie que le mod est activé
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

//Définitions
global $db, $table_prefix, $pub_mois, $pub_annee, $resultgains;

$query = "SELECT archives_date FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE `archives_user_id`='".$user_data["user_id"]."'";
$result = $db->sql_query($query);
$nbline = $db->sql_numrows($result);

//Fieldset pour seletionner les dates de visualisation des gains
echo"<fieldset><legend><b><font color='#0080FF'>Date d'affichage des attaques ";
echo help("changer_affichage");
echo"</font></b></legend>";

echo"Afficher mes résultats anterieurs : ";
echo"<form action='index.php?action=attaques&page=archive' method='post'>";
echo"mois : <input type='text' name='mois' size='2' maxlength='2' value='$pub_mois' /> ";
echo"année : ";
echo"<input type='text' name='annee' size='4' maxlength='4' value='$pub_annee' />";
echo"<br /><br />";
echo"<table border=0><tr>";
$count=0;
while (list($date) = $db->sql_fetch_row($result))
{
  echo "<td class='c'><a href='index.php?action=attaques&page=archive&mois=".date("m",$date)."&annee=".date("Y",$date)."'>".date("M Y",$date)."</a></td>";
  $count += 1;
  if (($count/10)== (intval($count/10)))
    {
    echo "</tr><tr>";
    }
}
echo "</tr></table>";
//echo "<a href='index.php?action=attaques&page=archive&mois=01&annee=2008'>Test</a>";
echo"<br />";
echo"<input type='submit'	value='Afficher' name='B1'></form>";
echo"</fieldset>";
echo"<br><br>";

//Si le message de sauvegarde des resultats est défini, on l'affiche
if (isset($pub_message)) echo"<font color='FF0000'><big>La liste de vos attaques étant anterieure à ce mois, elle a été supprimée. Les résultats de vos attaques ont été sauvegardés, ils seront désormais accessibles sur cette page</big></font>";

if ((isset($pub_mois)) && (isset($pub_annee)))
{
$pub_mois = intval($pub_mois);
$pub_annee = intval($pub_annee);

$date_from = mktime(0, 0, 0, $pub_mois, 01, $pub_annee);

//Requete pour afficher la liste des gains anterieurs
$sql = "SELECT archives_nb_attaques, archives_date, archives_metal, archives_cristal, archives_deut, archives_pertes, archives_recy_metal, archives_recy_cristal, archives_id FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE archives_user_id=".$user_data["user_id"]." AND archives_date=".$date_from."";
$result = $db->sql_query($sql);

$nb_result = mysql_num_rows($result);

if($nb_result == 0)
{
$date_from = strftime("%b %Y", $date_from);

echo"<fieldset><legend><b><font color='#0080FF'>Archives des attaques du mois de ".$date_from."</font></b></legend>";
echo"<font color='#FF0000'>Vous n'avez pas de resultat pour cette date</font>";
echo"</fieldset><br><br>";
}
else
{
list($archives_nb_attaques, $archives_date, $archives_metal, $archives_cristal, $archives_deut, $archives_pertes, $archives_recy_metal, $archives_recy_cristal, $archives_id) = $db->sql_fetch_row($resultgains);

$date_from = strftime("%b %Y", $date_from);

//On fait les calculs du total des gains, et la rentabilité, et du total des recyclages
$total_gains = $archives_metal+$archives_cristal+$archives_deut;
$renta = $total_gains-$archives_pertes+$archives_recy_metal+$archives_recy_cristal;
$total_recy = $archives_recy_metal+$archives_recy_cristal;

echo"<fieldset><legend><b><font color='#0080FF'>Archives des attaques du mois de ".$date_from."</font></b></legend>";
//echo"<table width='100%'><tr align='left'>";

// Afficher l'image du graphique
//echo"<td width='410px' align='center'>";


//Separateur de milliers
/*$archives_nb_attaques = number_format($archives_nb_attaques, 0, ',', ' ');
$archives_metal = number_format($archives_metal, 0, ',', ' ');
$archives_cristal = number_format($archives_cristal, 0, ',', ' ');
$archives_deut = number_format($archives_deut, 0, ',', ' ');
$total_gains = number_format($total_gains, 0, ',', ' ');
$archives_pertes = number_format($archives_pertes, 0, ',', ' ');
$renta = number_format($renta, 0, ',', ' ');
$archives_recy_metal = number_format($archives_recy_metal, 0, ',', ' ');
$archives_recy_cristal = number_format($archives_recy_cristal, 0, ',', ' ');
$total_recy = number_format ($total_recy, 0, ',', ' ');*/

//On prépare les resultats au format bbcode
$bbcode = "[b]Résultats des attaques de ".$user_data['user_name']."[/b]\n";
$bbcode .="du mois de ".$date_from."\n\n";
$bbcode .="Nombre d'attaques durant le mois : ".number_format($archives_nb_attaques, 0, ',', ' ')."\n\n";
$bbcode .="Métal gagné : ".number_format($archives_metal, 0, ',', ' ')."\n";
$bbcode .="Cristal gagné : ".number_format($archives_cristal, 0, ',', ' ')."\n";
$bbcode .="Deuterium gagné : ".number_format($archives_deut, 0, ',', ' ')."\n\n";
$bbcode .="Total des ressources gagnées : ".number_format($total_gains, 0, ',', ' ')."\n";
$bbcode .="Total des pertes attaquant : ".number_format($archives_pertes, 0, ',', ' ')."\n\n";
$bbcode .="Total du métal recyclé : ".number_format($archives_recy_metal, 0, ',', ' ')."\n";
$bbcode .="Total du cristal recyclé : ".number_format($archives_recy_cristal, 0, ',', ' ')."\n\n";
if ($renta > 0) $bbcode .="Rentabilité : [color=#00FF40]".number_format($renta, 0, ',', ' ')."[/color]\n\n";
else $bbcode .="Rentabilité : [color=#FF0000]".number_format($renta, 0, ',', ' ')."[/color]\n\n";
$bbcode .="[url=http://www.ogsteam.fr/forums/sujet-1358-mod-gestion-attaques]Généré par le module de gestion des attaques[/url]";

echo"<table width='100%'><tr align='left'>";
echo "<td width='40%'>".
				"<table width='100%'><colgroup><col width='55%'/><col/></colgroup><tbody>".
				"<tr>".
				"<td style='font-size: 18px;color: white;'><b>Nombre d'attaques durant le mois</b></td>".
				"<td class='metal number' style='font-size: 18px;'>" . number_format($archives_nb_attaques, 0, ',', ' ') . "</td>" .
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>M&eacute;tal gagn&eacute;</b></td>".
				"<td class='metal number' style='font-size: 18px;'>" . number_format($archives_metal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Cristal gagn&eacute;</b></td>".
				"<td class='cristal number' style='font-size: 18px;'>" . number_format($archives_cristal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Deut&eacute;rium gagn&eacute;</b></td>".
				"<td class='deuterium number' style='font-size: 18px;'>" . number_format($archives_deut, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Gains attaques</b></td>" . 
				"<td class='number' style='font-size: 18px;color: white;'>" . number_format($total_gains, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Pertes attaques</b></td>" .
				"<td class='perte number' style='font-size: 18px;'>" . number_format($archives_pertes, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Rentabilit&eacute; attaques</b></td>" .
				"<td class='renta number' style='font-size: 18px;'>" . number_format(($total_gains-$archives_pertes), 0, ',', ' ') . "</td>" .
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>M&eacute;tal recycl&eacute;</b></td>".
				"<td class='metal number' style='font-size: 18px;'>" . number_format($archives_recy_metal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Cristal recycl&eacute;</b></td>".
				"<td class='cristal number' style='font-size: 18px;'>" . number_format($archives_recy_cristal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Gains recyclages</b></td>" . 
				"<td class='renta number' style='font-size: 18px;'>" . number_format(($archives_recy_metal+$archives_recy_cristal), 0, ',', ' ') . "</td>" .
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Rentabilité totale</b></td>" . 
				"<td class='renta number' style='font-size: 18px;'>" . number_format((($total_gains-$archives_pertes) + ($archives_recy_metal+$archives_recy_cristal)), 0, ',', ' ') . "</td>" .
				"</tr>".
				"<tr><td colspan='2'>&#160;</td></tr>".
				"<tr><td colspan='2'>&#160;</td></tr>".
				"<tr><td colspan='2'>&#160;</td></tr>".
				"<tr><td colspan='2'>".
				"<big>Résultats en BBCode</big><br/>".
				"<a href='#haut' onclick='selectionner()'>Selectionner</a>".
				"<form method='post'><textarea rows='3' cols='15' id='bbcode'>" . $bbcode . "</textarea></form>".
				"</td></tr><tbody></table>".
				"</td>";

// Afficher l'image du graphique
echo"<td width='60%' align='center'>";

/** GRAPHIQUE **/
echo "<div id='graphique1' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/
//echo create_pie(($archives_metal+$archives_recy_metal) . "_x_" . ($archives_cristal+$archives_recy_cristal) . "_x_" . $archives_deut . "_x_" . $archives_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Ressources gagnées", "graphique1");
echo  create_pie_numbers(($archives_metal+$archives_recy_metal) . "_x_" . ($archives_cristal+$archives_recy_cristal) . "_x_" . $archives_deut . "_x_" . $archives_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Ressources gagnées", "graphique1");
?>
<br/>
<?php
/** GRAPHIQUE **/
echo "<div id='graphique2' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/
//echo create_pie($total_gains . "_x_" . $total_recy, "Gains_x_Recyclages", "Gains - Recyclages", "graphique2");
echo  create_pie_numbers($total_gains . "_x_" . $total_recy, "Gains_x_Recyclages", "Gains - Recyclages", "graphique2");
?>
<?php

echo"</td>";
echo"</tr>";
echo"</table>";
echo"</fieldset>";
echo"</big><br>";
echo"<br>";
}
}
echo"<br/>";
?>