<?php

/**
 * archives.php
 *
 * @package Attaques
 * @author Verité modifié par ericc
 * @link https://www.ogsteam.eu
 * @version : 0.8a
 */

//L'appel direct est interdit
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

//Définitions
global $db, $log, $table_prefix, $prefixe;

//On vérifie que le mod est activé
$query = "SELECT `active` FROM `" . TABLE_MOD . "` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

// Appel des Javascripts
echo "<script type='text/javascript' src='" . FOLDER_ATTCK . "/attack.js'></script>";

//Gestion des dates
$jour = date("j");
$mois = date("m");
$annee = date("Y");

$joursdanslemois = date("t");

$jourdelasemaine = date("w"); // 0 dimanche , 6 samedi
$semainedelannee = date("W");


if ($jourdelasemaine == 0) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 6), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 1) {
    $pub_date_from = mktime(0, 0, 0, $mois, $jour, $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 2) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 1), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 3) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 2), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 4) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 3), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 5) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 4), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
} else if ($jourdelasemaine == 6) {
    $pub_date_from = mktime(0, 0, 0, $mois, ($jour - 5), $annee);
    $pub_date_to = mktime(23, 59, 59, $mois, $jour, $annee);
}

$pub_date_from = intval($pub_date_from);
$pub_date_to = intval($pub_date_to);

$debutdumois = mktime(0, 0, 0, $mois, 1, $annee);
$findumois = mktime(23, 59, 59, $mois, $joursdanslemois, $annee);

/*

SELECT* FROM news WHERE date > SUBDATE(SYSDATE(), INTERVAL 7 DAY) // de la semaine
SELECT* FROM news WHERE date > SUBDATE(SYSDATE(), INTERVAL 1 MONTH) // du mois

*/


//Cacul pour obtenir les gains de la semaine
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes) FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data["id"] . " AND attack_date >= " . $debutdumois . " AND attack_date <= " . $findumois . " AND WEEKOFYEAR(FROM_UNIXTIME(attack_date)) = " . $semainedelannee;
$resultgains = $db->sql_query($query);
$row_week = $db->sql_fetch_row($resultgains);
$attack_metal = isset($row_week[0]) ? $row_week[0] : 0;
$attack_cristal = isset($row_week[1]) ? $row_week[1] : 0;
$attack_deut = isset($row_week[2]) ? $row_week[2] : 0;
$attack_pertes = isset($row_week[3]) ? $row_week[3] : 0;

//Cacul pour obtenir les gains des recyclages de la semaine
$query = "SELECT SUM(recy_metal), SUM(recy_cristal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data["id"] . " AND recy_date >= " . $debutdumois . " AND recy_date <= " . $findumois . " AND WEEKOFYEAR(FROM_UNIXTIME(recy_date)) = " . $semainedelannee;
$resultgainsrecy = $db->sql_query($query);
$row_week_recy = $db->sql_fetch_row($resultgainsrecy);
$recy_metal = isset($row_week_recy[0]) ? $row_week_recy[0] : 0;
$recy_cristal = isset($row_week_recy[1]) ? $row_week_recy[1] : 0;

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilit&eacute; Hebdomadaire</span></b></legend>";
/** GRAPHIQUE **/
if (($attack_metal + $recy_metal + $attack_cristal + $recy_cristal + $attack_deut + $attack_pertes) > 0) {
    echo "<div id='graphique' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
    echo create_pie_numbers(($attack_metal + $recy_metal) . "_x_" . ($attack_cristal + $recy_cristal) . "_x_" . $attack_deut . "_x_" . $attack_pertes, "M&eacute;tal_x_Cristal_x_Deut&eacute;rium_x_Pertes", "Attaques et Recyclages", "graphique");
} else {
    echo "<p style='text-align:center; color:#aaa;'>Aucune donn&eacute;e disponible pour cette semaine.</p>";
}
echo "</fieldset>";

echo "<br />";

//Cacul pour obtenir les gains
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes), WEEKOFYEAR(FROM_UNIXTIME(attack_date)) FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data["id"] . " AND attack_date >= " . $debutdumois . " AND attack_date <= " . $findumois . " GROUP BY WEEKOFYEAR(FROM_UNIXTIME(attack_date))";
$resultgains = $db->sql_query($query);

//echo "Attaques : ".$query."<br/>";

//Cacul pour obtenir les gains des recyclages
$query = "SELECT SUM(recy_metal), SUM(recy_cristal), WEEKOFYEAR(FROM_UNIXTIME(recy_date)) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data["id"] . " AND recy_date >= " . $debutdumois . " AND recy_date <= " . $findumois . " GROUP BY WEEKOFYEAR(FROM_UNIXTIME(recy_date))";
$resultgainsrecy = $db->sql_query($query);
$row_month_recy = $db->sql_fetch_row($resultgainsrecy);
$recy_metal = isset($row_month_recy[0]) ? $row_month_recy[0] : 0;
$recy_cristal = isset($row_month_recy[1]) ? $row_month_recy[1] : 0;
$recy_week = isset($row_month_recy[2]) ? $row_month_recy[2] : 0;

echo "<div width='410px'>";
$i = 0;
$categories = "";
$metal = "";
$cristal = "";
$deuterium = "";
$pertes = "";
while (list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes, $attack_week) = $db->sql_fetch_row($resultgains)) {
    //echo "Semaine ".$attack_week." : ".$attack_metal." : ".$attack_cristal." : ".$attack_deut." : ".$attack_pertes."<br/>";
    $i++;
    if ($i == 1) {
        $categories .= "'Semaine " . $attack_week . "'";
        $metal .= $attack_metal;
        $cristal .= $attack_cristal;
        $deuterium .= $attack_deut;
        $pertes .= $attack_pertes;
    } else {
        $categories .= ",'Semaine " . $attack_week . "'";
        $metal .= "," . $attack_metal;
        $cristal .= "," . $attack_cristal;
        $deuterium .= "," . $attack_deut;
        $pertes .= "," . $attack_pertes;
    }
}

$series = "{name: 'Métal', data: [" . $metal . "] }, " . "{name: 'Cristal', data: [" . $cristal . "] }, " . "{name: 'Deutérium', data: [" . $deuterium . "] }, " . "{name: 'Pertes', data: [" . $pertes . "] }";

echo "</div>";

// *****************************************************************************************************

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilit&eacute; Mensuelle</span></b></legend>";

/** GRAPHIQUE **/
if (!empty($categories)) {
echo "<div id='graphiquemois' style='height: 350px; width: 410px; margin: 0pt auto; clear: both;'></div>";

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
             categories: [" . $categories . "]
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
               series: [" . $series . "]
       });
</script>";

//echo "<img src='index.php?action=attaques&graphic=mois' alt='".T_("Attaques_pasdegraphique")."' />";
} else {
    echo "<p style='text-align:center; color:#aaa;'>Aucune donn&eacute;e disponible pour ce mois.</p>";
}
echo "</fieldset>";

// *******************************************************************************************************************


//Cacul pour obtenir les gains

$query = "SELECT users.name, users.id, " . "SUM(attacks.attack_metal), " . "(SELECT SUM(recy_metal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id = users.id AND recy_date BETWEEN " . $debutdumois . " AND " . $findumois . ") AS recy_metal, " . "SUM(attacks.attack_cristal), " . "(SELECT SUM(recy_cristal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id = users.id AND recy_date BETWEEN " . $debutdumois . " AND " . $findumois . ") AS recy_cristal, " . "SUM(attacks.attack_deut), SUM(attacks.attack_pertes) " . "FROM " . TABLE_USER . " users " . "INNER JOIN " . TABLE_ATTAQUES_ATTAQUES . " attacks ON attacks.attack_user_id = users.id " . "WHERE attacks.attack_date BETWEEN " . $debutdumois . " AND " . $findumois . " " . "GROUP BY users.id " . "ORDER BY users.name ASC";

$resultgains = $db->sql_query($query);


$names = array();
$values = array();
while (list($user, $user_id, $attack_metal, $recy_metal, $attack_cristal, $recy_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains)) {
    $i++;
    $renta = (($attack_metal + $recy_metal + $attack_cristal + $recy_cristal + $attack_deut) - $attack_pertes);

    if ($renta > 0) {
        array_push($names, $user);
        array_push($values, $renta);
    }
}

$noms = "";
$valeurs = "";
for ($i = 0; $i < count($names); ++$i) {
    if ($i > 0) {
        $noms .= "_x_";
        $valeurs .= "_x_";
    }
    $noms .= $names[$i];
    $valeurs .= $values[$i];
}

//echo $noms;
//echo $valeurs;

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilit&eacute; Mensuelle Globale</span></b></legend>";

/** GRAPHIQUE **/
echo "<div id='graphiquemoisglobal' style='height: 350px; width: 850px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/

if (!empty($valeurs)) {
    echo create_pie_numbers($valeurs, $noms, "Historique du mois", "graphiquemoisglobal");
} else {
    echo "<p style='text-align:center; color:#aaa;'>Aucune donn&eacute;e disponible pour ce mois.</p>";
}
echo "</fieldset>";

