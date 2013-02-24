<?php
/**
* recyclages.php 
 * @package Attaques
 * @author Verité/ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8b
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


//Fonction de suppression d'un rapport d'attaque
if (isset($pub_recy_id))
{
	$pub_recy_id = intval($pub_recy_id);

	//On récupère l'id de l'utilisateur qui a enregistré cette attaque
	$query = "SELECT recy_user_id FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_id='$pub_recy_id'";
	$result = $db->sql_query($query);
	list($user) = $db->sql_fetch_row($result);
	
	if($user == $user_data['user_id'])
	{
		$query = "DELETE FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_id='$pub_recy_id'";
		$db->sql_query($query);
		echo"<blink><font color='FF0000'>Le recyclage a bien été supprimée.</font></blink>";
		
		//On ajoute l'action dans le log
		$line = $user_data['user_name']." supprime l'un de ses recyclage dans le module de gestion des attaques";
		$fichier = "log_".date("ymd").'.log';
		$line = "/*".date("d/m/Y H:i:s").'*/ '.$line;
		write_file(PATH_LOG_TODAY.$fichier, "a", $line);
	}
	else
	{
		echo"<blink><font color='FF0000'>Vous n'avez pas le droit d'effacer ce recyclage !!!</font></blink>";
		
		//On ajoute l'action dans le log
		$line = $user_data[user_name]." a tenté de supprimer un recyclage qui appartient à un autre utilisateurs dans le module de gestion des attaques";
		$fichier = "log_".date("ymd").'.log';
		$line = "/*".date("d/m/Y H:i:s").'*/ '.$line;
		write_file(PATH_LOG_TODAY.$fichier, "a", $line);
	}
}

//Si les dates d'affichage ne sont pas définies, on affiche par défaut les attaques du jours,
if(!isset($pub_date_from)) $pub_date_from = mktime(0, 0, 0, $mois, $date, $annee);
else $pub_date_from = mktime(0, 0, 0, $mois, $pub_date_from, $annee);

if(!isset($pub_date_to)) $pub_date_to = mktime(23, 59, 59, $mois, $date, $annee);
else $pub_date_to = mktime(23, 59, 59, $mois, $pub_date_to, $annee);

$pub_date_from = intval($pub_date_from);
$pub_date_to = intval($pub_date_to);

//Requete pour afficher la liste des recyclages
$query = "SELECT recy_coord, recy_date, recy_metal, recy_cristal, recy_id FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data["user_id"]." AND recy_date BETWEEN ".$pub_date_from." and ".$pub_date_to."  ORDER BY recy_date DESC,recy_id DESC";
$result = $db->sql_query($query);

//On recupère le nombre de recyclages
$nb_recy = mysql_num_rows($result);

//Cacul pour obtenir les gains des recyclages
$query = "SELECT SUM(recy_metal), SUM(recy_cristal) FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data["user_id"]." AND recy_date BETWEEN ".$pub_date_from." and ".$pub_date_to." GROUP BY recy_user_id"; 
$resultgains = $db->sql_query($query);

//On récupère la date au bon format
$pub_date_from = strftime("%d %b %Y", $pub_date_from);
$pub_date_to = strftime("%d %b %Y", $pub_date_to);

//Création du field pour choisir l'affichage (attaque du jour, de la semaine ou du mois
echo"<fieldset><legend><b><font color='#0080FF'>Date d'affichage des recyclages ";
echo help("changer_affichage");
echo"</font></b></legend>";

echo"Afficher mes recyclages : ";
echo"<form action='index.php?action=attaques&page=recyclages' method='post' name='date'>";
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
echo"<fieldset><legend><b><font color='#0080FF'>Résultats des recyclages du ".$pub_date_from." au ".$pub_date_to." ";
echo help("resultats");
echo"</font></b></legend>";

//Résultat requete
list($recy_metal, $recy_cristal) = $db->sql_fetch_row($resultgains);	

//Calcul des gains totaux
$totalgains=$recy_metal+$recy_cristal;

echo"<table width='100%'><tr align='left' valign='center'>";

// Afficher l'image du graphique
echo"<td width='410px' align='center'>";
//echo $recy_metal."?".(!isset($recy_metal));
   if ((!isset($recy_metal)) && (!isset($recy_cristal)) && $recy_metal==0 && $recy_cristal==0) {
   	echo "Pas de graphique disponible";
   } else {   	
   	/**   GRAPHIQUE   **/
   	echo "<div id='graphique' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
   	/** FIN GRAPHIQUE **/
   	echo  create_pie($recy_metal . "_x_" . $recy_cristal, "Métal_x_Cristal", "Gains des recyclages", "graphique");
   	/*echo "<script type='text/javascript'>
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
	            renderTo: 'graphique',
	            defaultSeriesType: 'pie',
	            margin: [50, 200, 60, 170]
	         },
	         title: {
	            text: 'Proportion des gains des recyclages affichées'
	         },
	         plotArea: {
	            shadow: null,
	            borderWidth: null,
	            backgroundColor: null
	         },
	         tooltip: {
	            formatter: function() {
	               return '<b>'+ this.point.name +'</b>: '+ number_format(this.y, 0, ',', ' ');
	            }
	         },
	         plotOptions: {
	            pie: {
	               allowPointSelect: true,
	               cursor: 'pointer',
	               dataLabels: {
	                  enabled: true,
	                  formatter: function() {
	                     return this.point.name;
	                  },
	                  color: 'white',
	                  style: {
	                     font: '13px Trebuchet MS, Verdana, sans-serif'
	                  }
	               }
	            }
	         },
	         legend: {
	            layout: 'vertical',
	            style: {
	               left: 'auto',
	               bottom: 'auto',
	               left: '50px',
	               top: '50px'
	            }
	         },
	         series: [{
	            type: 'pie',
	            name: 'Gain des Recyclages',
	            data: [";
					if($recy_metal!=0){
				    	echo "['<b>Métal</b>', ".number_format($recy_metal, 0, ',', '')."]";
					}
					if($recy_cristal!=0){
				    	//echo ",['<b>Cristal</b>', ".number_format($recy_cristal, 0, ',', '')."]";
						echo ",{
							name: '<b>Cristal</b>',
							y: ".number_format($recy_cristal, 0, ',', '').",
							sliced: true,
							selected: true
						}";
					}
	         echo "]
	         }]
   		});
      </script>";*/
   	//echo "<img src='index.php?action=graphic_pie&values=".$recy_metal."_x_".$recy_cristal."&legend=Metal_x_Cristal&title=Proportion%20des%20gains%20des%20recyclages%20affich%E9es' alt='".T_("Attaques_pasdegraphique")."'>";
   }
   echo"</td></tr>";

//Affichage des gains en métal, en cristal et en deut
$recy_metal = number_format($recy_metal, 0, ',', ' ');
$recy_cristal = number_format($recy_cristal, 0, ',', ' ');
echo "<td><p align='left'><font color='#FFFFFF'><big><big><big>Métal recyclé : ".$recy_metal."<br>Cristal recyclé : ".$recy_cristal."<br><br>";

//Affichage du total des gains
$totalgains = number_format($totalgains, 0, ',', ' ');
echo "<b>Soit un total de : ".$totalgains."</b><br><br>";

echo"</big></big>";
echo"</big></big></font></td></tr></table>";
echo"</p></fieldset><br><br>";

//Création du field pour voir la liste des attaques
echo"<fieldset><legend><b><font color='#0080FF'>Liste des recyclages du ".$pub_date_from." au ".$pub_date_to." ";
echo" : ".$nb_recy." recyclage(s) ";
echo help("liste_recy");
echo"</font></b></legend>";

//Tableau donnant la liste des attaques
echo"<table width='100%'>";
echo"<tr>";
echo"<td class=".'c'." align=".'center'."><b>Coordonnées</b></td>";
echo"<td class=".'c'." align=".'center'."><b>Date du recyclage</b></td>";
echo"<td class=".'c'." align=".'center'."><b>Métal Recyclé</b></td>";
echo"<td class=".'c'." align=".'center'."><b>Cristal Recyclé</b></td>";
echo"<td class=".'c'." align=".'center'."><b><font color='#FF0000'>Supprimer</font></b></td>";

echo"</tr>";
echo"<tr>";

while( list($recy_coord, $recy_date, $recy_metal, $recy_cristal, $recy_id) = $db->sql_fetch_row($result) )
{
	$recy_date = strftime("%d %b %Y à %Hh%M", $recy_date);
	$recy_metal = number_format($recy_metal, 0, ',', ' ');
	$recy_cristal = number_format($recy_cristal, 0, ',', ' ');
	echo"<th align='center'>".$recy_coord."</th>";
	echo"<th align='center'>".$recy_date."</th>";
	echo"<th align='center'>".$recy_metal."</th>";
	echo"<th align='center'>".$recy_cristal."</th>";
	echo"<th align='center' valign='middle'><form action='index.php?action=attaques&page=recyclages' method='post'><input type='hidden' name='date_from' value='$pub_date_from'><input type='hidden' name='date_to' value='$pub_date_to'><input type='hidden' name='recy_id' value='$recy_id'><input type='submit'	value='Supprimer' name='B1' style='color: #FF0000'></form></th>";
	echo"</tr>";
	echo"<tr>";
}
echo"</tr>";
echo"</table>";
echo"</fieldset>";

if ($config['histo']==1)
{
	/**** DEBUT HISTO ******/
	$mois = date("m");
	$annee = date("Y");
	
	$query="SELECT DAY(FROM_UNIXTIME(recy_date)) AS day, SUM(recy_metal) AS metal, SUM(recy_cristal) AS cristal FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id=".$user_data['user_id']." and MONTH(FROM_UNIXTIME(recy_date))=".$mois." and YEAR(FROM_UNIXTIME(recy_date))=".$annee." GROUP BY day";
	
	// requète SQL pour récupérer le total par ressource par jour
	$result = $db->sql_query($query);
	
	// Initialisation des variables et tableau
	
	$barre = array();
	
	if (isset($query)) {
		while (list($jour, $metal, $cristal) = $db->sql_fetch_row($result)) {
			if ( !isset($barre[$jour][0])) {
				$barre[$jour][0]=0;
			}
			if ( !isset($barre[$jour][1])) {
				$barre[$jour][1]=0;
			}
			$barre[$jour][0] += $metal;
			$barre[$jour][1] += $cristal;	
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
	
	$series = "{name: 'Métal', data: [".$metal."] }, " .
			  "{name: 'Cristal', data: [".$cristal."] }, " .
			  "{name: 'Deutérium', data: [".$deuterium."] }";
		
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
echo"<br>";
echo"<br>";
?>
