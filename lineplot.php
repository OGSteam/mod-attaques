<?php
// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");
//$handle=fopen("line.txt","w");

// Appel de la librairie Artichow pour tracer des lignes
require_once "library/artichow/LinePlot.class.php";
global $db;
// Initialisation des couleurs
$blue 	= new Blue;
$red 	= new Red;
$green 	= new Green;
$yellow = new Yellow;
$cyan 	= new Cyan;
$magenta= new Magenta;
$orange = new Orange;
$pink 	= new Pink;
$purple = new Purple;
$white = new White;
$darkred = new DarkRed;
$darkgreen = new DarkGreen;
$darkblue = new DarkBlue;
$darkyellow = new DarkYellow;
$darkpink = new DarkPink;
   	
$tbcolor = array ($red,$blue,$green,$yellow,$cyan,$magenta,$orange,$pink,$purple,$white,$darkred,$darkgreen,$darkblue,$darkyellow,$darkpink);
$num_color = 0;

//récupère la liste des users actifs
$query="SELECT `user_id`,`user_name`,`user_active` from ".TABLE_USER." ORDER BY `user_id`";
$result=$db->sql_query($query);
$count = 0;
while (list($userid,$username,$actif)=$db->sql_fetch_row($result))
  {
  if ($actif != 0)
    {
    $membre[$count] = array($userid,$username);
    $count+=1;
    } 
  }

// Initialisation du graphe
$graph = new Graph(800, 600);
$graph->setAntiAliasing(TRUE);
$group = new PlotGroup;
$group->setPadding(60, NULL, NULL, NULL);
$group->setBackgroundColor(new Color(0, 0, 20));
$graph->border->hide();
$group->grid->setNoBackground();
$group->grid->setType(2);
$group->axis->bottom->label->setCallbackFunction('setday');

switch ($pub_graphic)
  {
  case "week" :
    $maxy = 0;
    $aujourd = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
    $sept = date("d")-6;
    if ($sept < 1) $sept=1; 
    $sevenday = mktime(0, 0, 0, date("m"), $sept,   date("Y"));
    for ($i = 0; $i < $count; $i++)
      {
      $point=array();
      $userid = $membre[$i][0];
      $query = "SELECT DAY(FROM_UNIXTIME(attack_date)) AS day, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut,SUM(attack_pertes) as pertes FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id='".$userid."' and attack_date BETWEEN ".$sevenday." AND ".$aujourd." GROUP BY day";
      $result=$db->sql_query($query);
      $bilan=0;
      while (list($day,$metal,$cristal,$deut,$pertes)=$db->sql_fetch_row($result))
        {
        $bilan = $metal+$cristal+$deut-$pertes;
        $point[$day]=$bilan;
        if ($point[$day]>$maxy) {$maxy=$point[$day];}
        }
      $query = "SELECT DAY(FROM_UNIXTIME(recy_date)) AS day, SUM(recy_metal) AS metal, SUM(recy_cristal) AS cristal FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id='".$userid."' and recy_date BETWEEN ".$sevenday." AND ".$aujourd." GROUP BY day";
      $result=$db->sql_query($query);
      while (list($day,$metal,$cristal)=$db->sql_fetch_row($result))
        {
        $bilan = $metal+$cristal;
        if (! isset($point[$day])) {$point[$day] = 0;}
        $point[$day]+=$bilan;
        if ($point[$day]>$maxy) {$maxy=$point[$day];}
        }
      ksort($point);
      $value=array();
      if (!is_null(key($point)))
        {
        for ($j = intval(date("d",$sevenday)); $j <= intval(date("d",$aujourd)); $j++)
          {
          if (isset($point[$j]))
            {
            $value[]=$point[$j];
            }else{
            $value[]=0;
            }
          }
          $plot = new LinePlot($value);
          
          $plot->setColor($tbcolor[($num_color-(15*intval($num_color/15)))]);
          $num_color ++;
              
          $group->add($plot);
          $group->legend->add($plot, $membre[$i][1], LEGEND_LINE);
        }
      }
    $days=array();
    for ($j = intval(date("d",$sevenday)); $j <= intval(date("d",$aujourd)); $j++)
      {
      $days[]= date("D",mktime(0, 0, 0, date("m"), $j,   date("Y")));
      }  
    break;   
      
  case "mois" :
    $maxy = 0;
    $older = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
    for ($i = 0; $i < $count; $i++)
      {
      $point=array();
      $userid = $membre[$i][0];
      $query = "SELECT `archives_date`,`archives_metal`,`archives_cristal`,`archives_deut`,`archives_pertes`,`archives_recy_metal` ,`archives_recy_cristal` FROM ".TABLE_ATTAQUES_ARCHIVES." WHERE `archives_user_id`='".$userid."' ORDER BY `archives_date` ASC";
      $result=$db->sql_query($query);
      while (list($date,$metal,$cristal,$deut,$pertes,$rmetal,$rcristal)=$db->sql_fetch_row($result))
        {
        $mois = intval(date("m",$date));
        $year = intval(date("y",$date));
        if ($date<$older) {$older=$date;}
        $bilan = $metal + $rmetal + $cristal + $rcristal + $deut - $pertes;
        $point[$mois][$year] = $bilan;
        if ($point[$mois][$year]>$maxy) {$maxy=$point[$mois][$year];}
        }
      $value=array();
      if (!is_null(key($point)))
        {
        for ($j = intval(date("y",$older)); $j <= (date("y")); $j++)
          {
          for ($k = 1; $k <= 12; $k++)
            {
            if (!(($j == intval(date("y",$older))) && ($k < intval(date("m",$older)))))
              {    
              if (isset($point[$k][$j]))
                {
                $value[]=$point[$k][$j];
                }else{
                $value[]=0;
                }
              }
            if (($j == intval(date("y"))) && ($k == intval(date("m")-1))) break;
            }
          }
          $plot = new LinePlot($value);
          $plot->setColor($tbcolor[($num_color-(15*intval($num_color/15)))]);
          $num_color ++;
              
          $group->add($plot);
          $group->legend->add($plot, $membre[$i][1], LEGEND_LINE);
        }
      
      }
    $days=array();
    for ($j = intval(date("y",$older)); $j <= intval(date("y")); $j++)
      {
      for ($k = 1; $k <= 12; $k++)
        {
        if (!(($j == intval(date("y",$older))) && ($k < intval(date("m",$older)))))
          {
          $days[]= date("My",mktime(0, 0, 0, $k, 01, $j));
          }
          if (($j == intval(date("y"))) && ($k == intval(date("m")-1))) break;
          }
      }
    break;
  }

$group->axis->left->setColor($white);
$group->axis->left->label->setColor($white);
$group->axis->bottom->setColor($white);
$group->axis->bottom->label->setColor($white);
$group->axis->left->setLabelPrecision(0);
$group->setYMax($maxy);
$group->axis->bottom->setLabelText($days);
$group->legend->shadow->setSize(0);
$group->legend->setAlign(LEGEND_CENTER);
$group->legend->setSpace(6);
$group->legend->setTextFont(new Tuffy(8));
$group->legend->setPosition(0.50, 0.1);
$group->legend->setBackgroundColor(new Color(255, 255, 255, 25));
$group->legend->setColumns(2);
$graph->add($group);
$graph->draw();

?>