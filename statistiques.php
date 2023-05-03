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
//lang_module_page('Attaques');
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
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes) FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data["user_id"] . " AND attack_date >= " . $debutdumois . " AND attack_date <= " . $findumois . " AND WEEKOFYEAR(FROM_UNIXTIME(attack_date)) = " . $semainedelannee;
$resultgains = $db->sql_query($query);
list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains);

//Cacul pour obtenir les gains des recyclages de la semaine
$query = "SELECT SUM(recy_metal), SUM(recy_cristal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data["user_id"] . " AND recy_date >= " . $debutdumois . " AND recy_date <= " . $findumois . " AND WEEKOFYEAR(FROM_UNIXTIME(recy_date)) = " . $semainedelannee;
$resultgainsrecy = $db->sql_query($query);
list($recy_metal, $recy_cristal) = $db->sql_fetch_row($resultgainsrecy);


//Définitions
global $db, $table_prefix, $prefixe;

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilité Hebdomadaire</span></b></legend>";
/** GRAPHIQUE **/
echo "<div id='graphique' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/
//echo create_pie(($attack_metal+$recy_metal) . "_x_" . ($attack_cristal+$recy_cristal) . "_x_" . $attack_deut . "_x_" . $attack_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Attaques et Recyclages", "graphique");
echo create_pie_numbers(($attack_metal + $recy_metal) . "_x_" . ($attack_cristal + $recy_cristal) . "_x_" . $attack_deut . "_x_" . $attack_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Attaques et Recyclages", "graphique");
/*
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
                 renderTo: 'graphique',
                 defaultSeriesType: 'pie',
                 margin: [50, 200, 60, 170]
              },
              title: {
                 text: 'Proportion des Gains'
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
                 name: 'Gains de la semaine',
                 data: [";
                    if($attack_metal!=0){
                        echo "['<b>M&eacute;tal</b>', ".number_format($attack_metal, 0, ',', '')."]";
                        if($attack_cristal!=0 || $attack_deut!=0 || $attack_pertes!=0 || $recy_metal!=0 || $recy_cristal!=0){
                            echo ",";
                        };
                    }
                    if($attack_cristal!=0){
                        echo "['<b>Cristal</b>', ".number_format($attack_cristal, 0, ',', '')."]";
                        if($attack_deut!=0 || $attack_pertes!=0 || $recy_metal!=0 || $recy_cristal!=0){
                            echo ",";
                        };
                    }
                    if($attack_deut!=0){
                        echo "['<b>Deut&eacute;rium</b>', ".number_format($attack_deut, 0, ',', '')."]";
                        if($attack_deut!=0 || $attack_pertes!=0 || $recy_metal!=0 || $recy_cristal!=0){
                        echo ",";
                        };
                    }
                    if($recy_metal!=0){
                        echo "['<b>M&eacute;tal Rec.</b>', ".number_format($recy_metal, 0, ',', '')."]";
                        if($recy_cristal!=0 || $attack_pertes!=0){
                        echo ",";
                        };
                    }
                    if($recy_cristal!=0){
                        echo "['<b>Cristal Rec.</b>', ".number_format($recy_cristal, 0, ',', '')."]";
                        if($attack_pertes!=0){
                            echo ",";
                        };
                    }
                    if($attack_pertes!=0){
                    echo "{
                       name: '<b>Pertes</b>',
                       y: ".number_format($attack_pertes, 0, ',', '').",
                       sliced: true,
                       selected: true
                    }";
                    }
                 echo "]
              }]
            });
        </script>";*/
//echo "<img src='index.php?action=attaques&graphic=week' alt='".T_("Attaques_pasdegraphique")."' />";
echo "</fieldset>";

echo "<br />";

//Cacul pour obtenir les gains
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes), WEEKOFYEAR(FROM_UNIXTIME(attack_date)) FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data["user_id"] . " AND attack_date >= " . $debutdumois . " AND attack_date <= " . $findumois . " GROUP BY WEEKOFYEAR(FROM_UNIXTIME(attack_date))";
$resultgains = $db->sql_query($query);
//list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes, $attack_week) = $db->sql_fetch_row($resultgains);

//echo "Attaques : ".$query."<br/>";

//Cacul pour obtenir les gains des recyclages
$query = "SELECT SUM(recy_metal), SUM(recy_cristal), WEEKOFYEAR(FROM_UNIXTIME(recy_date)) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data["user_id"] . " AND recy_date >= " . $debutdumois . " AND recy_date <= " . $findumois . " GROUP BY WEEKOFYEAR(FROM_UNIXTIME(recy_date))";
$resultgainsrecy = $db->sql_query($query);
list($recy_metal, $recy_cristal, $recy_week) = $db->sql_fetch_row($resultgainsrecy);

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

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilité Mensuelle</span></b></legend>";

/** GRAPHIQUE **/
echo "<div id='graphiquemois' style='height: 350px; width: 410px; margin: 0pt auto; clear: both;'></div>";
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
echo "</fieldset>";

// *******************************************************************************************************************


//Cacul pour obtenir les gains
//$query = "SELECT attack_user_id, SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes), WEEKOFYEAR(FROM_UNIXTIME(attack_date)) FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_date >= ".$debutdumois." AND attack_date <= ".$findumois." GROUP BY attack_user_id";

$query = "SELECT user_stat_name, user_id, " . "SUM(attacks.attack_metal), " . "(SELECT SUM(recy_metal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id = users.user_id AND recy_date BETWEEN 1367359200 AND 1370037599) AS recy_metal, " . "SUM(attacks.attack_cristal), " . "(SELECT SUM(recy_cristal) FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id = users.user_id AND recy_date BETWEEN 1367359200 AND 1370037599) AS recy_cristal, " . "SUM(attacks.attack_deut), SUM(attacks.attack_pertes) " . "FROM " . TABLE_USER . " users " . "INNER JOIN " . TABLE_ATTAQUES_ATTAQUES . " attacks ON attacks.attack_user_id = users.user_id " . "WHERE attacks.attack_date BETWEEN 1367359200 AND 1370037599 " . "GROUP BY users.user_id " . "ORDER BY user_stat_name ASC";

$resultgains = $db->sql_query($query);

//echo $query;

/*$i=0;
$seriesglobal="";
while(list($user, $attack_user_id, $attack_metal, $recy_metal, $attack_cristal, $recy_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains) ){
    $i++;
    $renta = (($attack_metal + $recy_metal + $attack_cristal + $recy_cristal + $attack_deut) - $attack_pertes);

    if($renta > 0 && !(strpos($seriesglobal,$user) !== FALSE)) {
        if($i > 1){
            $seriesglobal .= ",";
        }
        $serie = "{name: '" . $user . "', data: ["  . $attack_metal . "," . $recy_metal . "," . $attack_cristal . "," .$recy_cristal . "," . $attack_deut . "," . $attack_pertes . "," . $renta . "]}";
        $seriesglobal .= $serie;
    }
}*/

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

echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Rentabilité Mensuelle Globale</span></b></legend>";

/** GRAPHIQUE **/
echo "<div id='graphiquemoisglobal' style='height: 350px; width: 850px; margin: 0pt auto; clear: both;'></div>";
/** GRAPHIQUE **/

echo create_pie_numbers($valeurs, $noms, "Historique du mois", "graphiquemoisglobal");
/*
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
                    type: 'column',
                    renderTo: 'graphiquemoisglobal',
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
                      className: 'white-container',
                      plotBackgroundColor: 'rgba(255, 255, 255, .1)',
                      plotBorderColor: '#CCCCCC',
                      plotBorderWidth: 1
                },
                title: {
                    text: 'Rentabilite Globale du mois'
                },
                xAxis: {
                    categories: ['Metal', 'Metal recycle', 'Cristal', 'Cristal recycle', 'Deuterium', 'Pertes', 'Rentabilite']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Total'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: 'white'
                        }
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
                 align: 'center',
                 verticalAlign: 'top',
                 x: 100,
                 y: 70
              },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.x +'</b><br/>'+
                            this.series.name +': '+ number_format(this.y, 0, ',', ' ') +'<br/>'+
                            'Total: '+ number_format(this.point.stackTotal, 0, ',', ' ');
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: [".$seriesglobal."]
            });
</script>";*/
echo "</fieldset>";
