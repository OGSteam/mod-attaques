<?php
/**
* bilan.php 
 * @package Attaques
 * @author Verité
 * @link http://www.ogsteam.fr
 * @version : 0.8a
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
//On vérifie que le mod est activé
$query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

// Appel des Javascripts
echo"<script type='text/javascript' language='javascript' src='".FOLDER_ATTCK."/attack.js'></script>";

//Définitions
global $db, $table_prefix;

//Gestion des dates
$date = date("j");
$mois = date("m");
$annee = date("Y");
$septjours = $date-7;
$yesterday = $date-1;

if($septjours < 1) $septjours = 1;
if($yesterday < 1) $yesterday = 1;


//Si les dates d'affichage ne sont pas définies, on affiche par défaut les attaques du jours,
if(!isset($pub_date_from)) $pub_date_from = mktime(0, 0, 0, $mois, $date, $annee);
else $pub_date_from = mktime(0, 0, 0, $mois, $pub_date_from, $annee);

if(!isset($pub_date_to)) $pub_date_to = mktime(23, 59, 59, $mois, $date, $annee);
else $pub_date_to = mktime(23, 59, 59, $mois, $pub_date_to, $annee);

$pub_date_from = intval($pub_date_from);
$pub_date_to = intval($pub_date_to);



//Requete pour afficher la liste des attaques 
$query = "SELECT attack_coord, attack_date, attack_metal, attack_cristal, attack_deut, attack_pertes, attack_id FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id=".$user_data["user_id"]." AND attack_date BETWEEN ".$pub_date_from." and ".$pub_date_to."  ORDER BY attack_date DESC,attack_id DESC";
$result = $db->sql_query($query);

//On recupère le nombre d'attaques
$nb_attack = mysql_num_rows($result);

//Cacul pour obtenir les gains
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes) FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id=".$user_data["user_id"]." AND attack_date BETWEEN ".$pub_date_from." and ".$pub_date_to." GROUP BY attack_user_id"; 
$resultgains = $db->sql_query($query);

//Cacul pour obtenir les gains des recyclages
$query = "SELECT SUM(recy_metal), SUM(recy_cristal) FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data["user_id"]." AND recy_date BETWEEN ".$pub_date_from." and ".$pub_date_to." GROUP BY recy_user_id"; 
$resultgainsrecy = $db->sql_query($query);


//On récupère la date au bon format
$pub_date_from = strftime("%d %b %Y", $pub_date_from);
$pub_date_to = strftime("%d %b %Y", $pub_date_to);


//Création du field pour choisir l'affichage (attaque du jour, de la semaine ou du mois
echo"<fieldset><legend><b><font color='#0080FF'>Date d'affichage du bilan ";
echo help("changer_affichage");
echo"</font></b></legend>";

echo"Afficher le bilan : ";
echo"<form action='index.php?action=attaques&page=bilan' method='post' name='date'>";
echo"du : <input type='text' name='date_from' id='date_from' size='11' maxlength='2' value='$pub_date_from' /> ";
echo"au : ";
echo"<input type='text' name='date_to' id='date_to' size='11' maxlength='2' value='$pub_date_to' />";
echo"<br>";
?>		
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $date; ?>'); setDateTo('<?php echo $date; ?>'); valid();">du jour</a> |
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $yesterday; ?>'); setDateTo('<?php echo $yesterday; ?>'); valid();">de la veille</a> | 
<a href="#haut" onclick="javascript: setDateFrom('<?php echo $septjours ; ?>'); setDateTo('<?php echo $date; ?>'); valid();">des 7 derniers jours</a> |
<a href="#haut" onclick="javascript: setDateFrom('01'); setDateTo('<?php echo $date; ?>'); valid();">du mois</a>
<?php
echo"<br><br>";
echo"<input type='submit' value='Afficher' name='B1'></form>";
echo"</fieldset>";
echo"<br><br>";

//Création du field pour voir les gains des attaques
echo"<fieldset><legend><b><font color='#0080FF'>Bilan du ".$pub_date_from." au ".$pub_date_to." </font></b></legend>";
echo"<table width='100%'><tr align='left'>";
		
//Résultat requete
list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains);	

//Résultat requete
list($recy_metal, $recy_cristal) = $db->sql_fetch_row($resultgainsrecy);	

//Calcul des gains totaux
$totalgains=$attack_metal+$attack_cristal+$attack_deut;

echo"<table width='100%'><tr align='left'>";
echo "<td width='25%'>".
				"<table width='100%'><colgroup><col width='55%'/><col/></colgroup><tbody>".
				"<tr>".
				"<td style='font-size: 18px;color: white;'><b>M&eacute;tal gagn&eacute;</b></td>".
				"<td class='metal number' style='font-size: 18px;'>" . number_format($attack_metal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Cristal gagn&eacute;</b></td>".
				"<td class='cristal number' style='font-size: 18px;'>" . number_format($attack_cristal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Deut&eacute;rium gagn&eacute;</b></td>".
				"<td class='deuterium number' style='font-size: 18px;'>" . number_format($attack_deut, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Gains attaques</b></td>" . 
				"<td class='number' style='font-size: 18px;color: white;'>" . number_format($totalgains, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Pertes attaques</b></td>" .
				"<td class='perte number' style='font-size: 18px;'>" . number_format($attack_pertes, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Rentabilit&eacute; attaques</b></td>" .
				"<td class='renta number' style='font-size: 18px;'>" . number_format(($totalgains-$attack_pertes), 0, ',', ' ') . "</td>" .
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>M&eacute;tal recycl&eacute;</b></td>".
				"<td class='metal number' style='font-size: 18px;'>" . number_format($recy_metal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Cristal recycl&eacute;</b></td>".
				"<td class='cristal number' style='font-size: 18px;'>" . number_format($recy_cristal, 0, ',', ' ') . "</td>" .
				"</tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Gains recyclages</b></td>" . 
				"<td class='renta number' style='font-size: 18px;'>" . number_format(($recy_metal+$recy_cristal), 0, ',', ' ') . "</td>" .
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"</tr><tr><td colspan='2'>&#160;</td></tr><tr>".
				"<td style='font-size: 18px;color: white;'><b>Rentabilité totale</b></td>" . 
				"<td class='renta number' style='font-size: 18px;'>" . number_format((($totalgains-$attack_pertes) + ($recy_metal+$recy_cristal)), 0, ',', ' ') . "</td>" .
				"</tr><tbody></table></td>";

// Afficher l'image du graphique
echo"<td width='75%' align='center'>";

if((!isset($attack_metal)) && (!isset($attack_cristal)) && (!isset($attack_deut)) && (!isset($attack_pertes)) && (!isset($recy_metal)) && (!isset($recy_cristal))) {
	echo "Pas de graphique disponible";
} else {	
	/** GRAPHIQUE **/
	echo "<div id='graphique' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
	/** GRAPHIQUE **/	
	//echo create_pie(($attack_metal+$recy_metal) . "_x_" . ($attack_cristal+$recy_cristal) . "_x_" . $attack_deut . "_x_" . $attack_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Bilan des Attaques et Recyclages", "graphique");
	echo  create_pie_numbers(($attack_metal+$recy_metal) . "_x_" . ($attack_cristal+$recy_cristal) . "_x_" . $attack_deut . "_x_" . $attack_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Bilan des Attaques et Recyclages", "graphique");
}


echo"</td></tr>";


echo "</table>";

echo"</p></fieldset>";

if ($config['histo']==1)
{

	/**** DEBUT HISTO ******/
	$mois = date("m");
	$annee = date("Y");
	
	$query="SELECT DAY(FROM_UNIXTIME(attack_date)) AS day, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id=".$user_data['user_id']." and MONTH(FROM_UNIXTIME(attack_date))=".$mois." and YEAR(FROM_UNIXTIME(attack_date))=".$annee." GROUP BY day";
	$query2="SELECT DAY(FROM_UNIXTIME(recy_date)) AS day, SUM(recy_metal) AS metal, SUM(recy_cristal) AS cristal FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data['user_id']." and MONTH(FROM_UNIXTIME(recy_date))=".$mois." and YEAR(FROM_UNIXTIME(recy_date))=".$annee." GROUP BY day";
	
	// requète SQL pour récupérer le total par ressource par jour
	$result = $db->sql_query($query);
	
	// Initialisation des variables et tableau
	
	$barre = array();
	// Lecture de la base de données et stockage des valeurs dans le tableau
	if ( $pub_subaction !="recyclage") {
		while (list($jour, $metal, $cristal, $deut) = $db->sql_fetch_row($result)) {
			$barre[$jour][0]=$metal;
			$barre[$jour][1]=$cristal;
			$barre[$jour][2]=$deut;
		  
			// on recherche la valeur la plus grande pour définir la valeur maxi de l'axe Y
			if ($metal>$maxy) {
				$maxy=$metal;
			}
			if ($cristal>$maxy) {
				$maxy=$cristal;
			}
			if ($deut>$maxy)  {
				$maxy=$deut;
			}
		}
	}
	
	if (isset($query2)) {
		$result2 = $db->sql_query($query2);
		while (list($jour, $metal, $cristal) = $db->sql_fetch_row($result2)) {
			if ( !isset($barre[$jour][0])) {
				$barre[$jour][0]=0;
			}
			if ( !isset($barre[$jour][1])) {
				$barre[$jour][1]=0;
			}
			$barre[$jour][0] += $metal;
			$barre[$jour][1] += $cristal;
	
			// on recherche la valeur la plus grande pour définir la valeur maxi de l'axe Y
			if ($metal>$maxy) {
				$maxy=$metal;
			}
			if ($cristal>$maxy) {
				$maxy=$cristal;
			}
		}
	}
	
	$i=0;
	$categories="";$metal="";$cristal="";$deuterium="";
	for($n = 1; $n < 32; $n++) {
		if ( !isset($barre[$n][0])) { $barre[$n][0]=0;}
		if ( !isset($barre[$n][1])) { $barre[$n][1]=0;}
		if ( !isset($barre[$n][2])) { $barre[$n][2]=0;}
		
		if($n==1){
			$categories .= "'".$n."'";
			$metal .= $barre[$n][0];
			$cristal .= $barre[$n][1];
			$deuterium .= $barre[$n][2];
		} else {
			$categories .= ",'".$n."'";
			$metal .= ",".$barre[$n][0];
			$cristal .= ",".$barre[$n][1];
			$deuterium .= ",".$barre[$n][2];
		}
	}
	
	$series = "{name: 'M&eacute;tal', data: [".$metal."] }, " .
			  "{name: 'Cristal', data: [".$cristal."] }, " .
			  "{name: 'Deut&eacute;rium', data: [".$deuterium."] }";
		
/** GRAPHIQUE **/
echo "<div id='graphiquemois' style='height: 350px; width: 1200px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/

echo "<script type='text/javascript'>
	   			function number_format(number, decimals, dec_point, thousands_sep) {
	    			var n = !isFinite(+number) ? 0 : +number, 
	        		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	        		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			        s = '',
			        toFixedFix = function (n, prec) {
			            var k = Math.pow(10, prec);
			            return '' + Math.round(n * k) / k;
			        };
	    			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	    			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
				    if (s[0].length > 3) {
				        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);    }
				    if ((s[1] || '').length < prec) {
				        s[1] = s[1] || '';
				        s[1] += new Array(prec - s[1].length + 1).join('0');
				    }    return s.join(dec);
				}
				
			var chart;
			
				chart = new Highcharts.Chart({
	      chart: {
	         renderTo: 'graphiquemois',
	         defaultSeriesType: 'column',
	         backgroundColor: {
		         linearGradient: [0, 0, 250, 500],
		         stops: [
		            [0, 'rgb(48, 48, 96)'],
		            [1, 'rgb(0, 0, 0)']
		         ]
		      },
		      borderColor: '#000000',
		      borderWidth: 2,
		      className: 'dark-container',
		      plotBackgroundColor: 'rgba(255, 255, 255, .1)',
		      plotBorderColor: '#CCCCCC',
		      plotBorderWidth: 1
	      },
	      title: {
	         text: 'Historique du mois'
	      },
	      xAxis: {
	         categories: [".$categories."]
	      },
	      yAxis: {
	         min: 0,
	         title: {
	            text: 'Quantité'
	         }
	      },
	      legend: {
	         layout: 'vertical',
	         style: {
			   left: 'auto',
			   bottom: 'auto',
	           right: '50px',
	           top: '50px'
			 },
			 itemStyle: {
		         font: '9pt Trebuchet MS, Verdana, sans-serif',
		         color: '#A0A0A0'
		     },
	         backgroundColor: '#666',
	         align: 'left',
	         verticalAlign: 'top',
	         x: 100,
	         y: 70
	      },
	      tooltip: {
	         formatter: function() {
	            return '<b>' + this.series.name + '</b>: ' + number_format(this.y, 0, ',', ' ');
	         }
	      },
	      plotOptions: {
	         column: {
	            pointPadding: 0.2,
	            borderWidth: 0
	         }
	      },
	           series: [".$series."]
	   });     
	</script>";

//echo"</fieldset>";
}
echo"<br />";
echo"<br />";
?>