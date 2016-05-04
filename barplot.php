<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
/**
 * barplot.php génération d'histogramme en barres 3D
 *
 * @package Attaques
 * @author  ericc
 * @link http://www.ogsteam.fr
 * @version : 0.8d
 */
// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
// Appel de la librairie Artichow pour tracer des histogrammes
//require_once "library/artichow/BarPlot.class.php";
global $db, $table_prefix, $prefixe;
// Gestion des dates - récupère le mois et l'année courants 
$mois = date("m");
$annee = date("Y");

switch ($pub_subaction) {
    case "attaques" :
        $query = "SELECT DAY(FROM_UNIXTIME(attack_date)) AS day, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data['user_id'] . " and MONTH(FROM_UNIXTIME(attack_date))=" . $mois . " and YEAR(FROM_UNIXTIME(attack_date))=" . $annee . " GROUP BY day";
        break;
    case "recyclage" :
        $query2 = "SELECT DAY(FROM_UNIXTIME(recy_date)) AS day, SUM(recy_metal) AS metal, SUM(recy_cristal) AS cristal FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data['user_id'] . " and MONTH(FROM_UNIXTIME(recy_date))=" . $mois . " and YEAR(FROM_UNIXTIME(recy_date))=" . $annee . " GROUP BY day";
        break;
    case "bilan" :
        $query = "SELECT DAY(FROM_UNIXTIME(attack_date)) AS day, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data['user_id'] . " and MONTH(FROM_UNIXTIME(attack_date))=" . $mois . " and YEAR(FROM_UNIXTIME(attack_date))=" . $annee . " GROUP BY day";
        $query2 = "SELECT DAY(FROM_UNIXTIME(recy_date)) AS day, SUM(recy_metal) AS metal, SUM(recy_cristal) AS cristal FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data['user_id'] . " and MONTH(FROM_UNIXTIME(recy_date))=" . $mois . " and YEAR(FROM_UNIXTIME(recy_date))=" . $annee . " GROUP BY day";
        break;
}

// requète SQL pour récupérer le total par ressource par jour
$result = $db->sql_query($query);

// Initialisation des variables et tableau

$barre = array();
// Lecture de la base de données et stockage des valeurs dans le tableau
if ($pub_subaction != "recyclage") {
    while (list($jour, $metal, $cristal, $deut) = $db->sql_fetch_row($result)) {
        $barre[$jour][0] = $metal;
        $barre[$jour][1] = $cristal;
        $barre[$jour][2] = $deut;

        // on recherche la valeur la plus grande pour définir la valeur maxi de l'axe Y
        if ($metal > $maxy) {
            $maxy = $metal;
        }
        if ($cristal > $maxy) {
            $maxy = $cristal;
        }
        if ($deut > $maxy) {
            $maxy = $deut;
        }
    }
}

if (isset($query2)) {
    $result2 = $db->sql_query($query2);
    while (list($jour, $metal, $cristal) = $db->sql_fetch_row($result2)) {
        if (!isset($barre[$jour][0])) {
            $barre[$jour][0] = 0;
        }
        if (!isset($barre[$jour][1])) {
            $barre[$jour][1] = 0;
        }
        $barre[$jour][0] += $metal;
        $barre[$jour][1] += $cristal;

        // on recherche la valeur la plus grande pour définir la valeur maxi de l'axe Y
        if ($metal > $maxy) {
            $maxy = $metal;
        }
        if ($cristal > $maxy) {
            $maxy = $cristal;
        }
    }
}

$i = 0;
$categories = "";
$metal = "";
$cristal = "";
$deuterium = "";
for ($n = 1; $n < 32; $n++) {
    if (!isset($barre[$n][$i])) {
        $barre[$n][$i] = 0;
    }
    if ($i == 1) {
        $categories .= "'Jour " . $n . "'";
        $metal .= $barre[$n][0];
        $cristal .= $barre[$n][1];
        $deuterium .= $barre[$n][2];
    } else {
        $categories .= ",'Jour " . $n . "''";
        $metal .= "," . $barre[$n][0];
        $cristal .= "," . $barre[$n][1];
        $deuterium .= "," . $barre[$n][2];
    }
}

$series = "{name: 'M&eacute;tal', data: [" . $metal . "] }, " . "{name: 'Cristal', data: [" . $cristal . "] }, " . "{name: 'Pertes', data: [" . $deuterium . "] }";

echo $series;

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
         defaultSeriesType: 'column'
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
            text: 'Quantit&eacute;'
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
         backgroundColor: '#FFFFFF',
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

?> 