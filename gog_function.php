<?php
/**
* gog_function.php 
* @package Attaques
* @author ericc
* @link http://www.ogsteam.fr
* @version 0.7a
* fonction de transfert des données d'un rapport de combat recu par le module Attaques vers le module gameOgame si celui ci est installé.
*/

define('TABLE_GAME',$table_prefix.'game');
define('TABLE_GAME_USERS',$table_prefix.'game_users');
define('TABLE_GAME_RECYCLAGE',$table_prefix.'game_recyclage');

function report($pub_data)
{
  global $db, $table_prefix, $user_data, $server_config, $config;

  //récupération des paramètres de config
$query = "SELECT value FROM `".TABLE_MOD_CFG."` WHERE `mod`='gameOgame' and `config`='config'";
$result = $db->sql_query($query);
$config = $db->sql_fetch_row($result);
$config=unserialize($config[0]);

  //Vérifie que le rapport n'est pas vide
  if (isset($pub_data) && $pub_data<>'')
  {	
    $data = stripslashes($pub_data);
    //Compatibilité UNIX/Windows
    $data = str_replace("\r\n","\n",$data);
    //Compatibilité IE/Firefox
    $data = str_replace("\t",' ',$data);
    //A priori, certains obtiennent des rapports avec de multiples espaces, donc on élimine le problème à la base
    cleanDoubleSpace($data);
    //Compatibilité avec la 0.76
    $data = str_replace(".","",$data);
    $data = str_replace("\'","'",$data);
	//Fait le nettoyage du rapport
	$data = str_replace("\n"," ",$data);
    $data = html_entity_decode($data);
	$data = str_replace("<br>"," ",$data);
	$data = str_replace("<th>"," ",$data);
	$data = strip_tags($data);
    // remove double space
	while (!(strpos($data,'  ')===FALSE))
	{
			$data = str_replace('  ',' ',$data);
	}
	// -----------------Fin du nettoyage -------------------
	// Vérifie que c'est bien un RC valide
    if (!preg_match('#Les\sflottes\ssuivantes\sse\ssont\saffrontées\sle\s(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2}) :#',$data,$date))
    {
      //echo 'Rapport de combat invalide';
      return 1;
    } else {
    //récupère le pseudo de l'attaquant 
    preg_match('#Attaquant\s(.{3,50})\s\(#',$data,$attaquant);
	//récupère les coordonnées de l'attaquant
	preg_match('#Attaquant\s.{3,110}\[(.{5,8})]#',$data,$coord_att);
    //On regarde dans les coordonnées de l'espace personnel du joueur qui insère les données via le plugin si les coordonnées de l'attaquant correspondent à une de ses planètes
    $query = "SELECT coordinates FROM ".TABLE_USER_BUILDING." WHERE user_id='".$user_data['user_id']."'";
    $result =    $db->sql_query($query);
	$attaqu = 0;
    	while(list($coordinates) = mysql_fetch_row($result))
		{
			if($coordinates == $coord_att[1]) $attaqu = 1;
		}
    	if ($attaqu == 0)
    	{
    		// Vous n'êtes pas l'attaquant, je sors !!
    		return;
    	}

      //récupère le pseudo du défenseur
      preg_match('#Défenseur\s(.{3,50})\s\(#',$data,$defenseur);
	  //récupère les coordonnées du défenseur
	  preg_match('#Défenseur\s.{3,110}\[(.{5,8})]#',$data,$coord_def);
      // Récupère les pertes de l'attaquant et du défenseur
      preg_match('#L\'attaquant\sa\sperdu\sau\stotal\s(\d*)\sunités#',$data,$pertesA);
      preg_match('#Le\sdéfenseur\sa\sperdu\sau\stotal\s(\d*)\sunités#',$data,$pertesD);
      preg_match('#(\d*)\sunités\sde\smétal,\s(\d*)\sunités\sde\scristal\set\s(\d*)\sunités\sde\sdeutérium#',$data,$ressources);
      if (!preg_match('#Un\schamp\sde\sdébris\scontenant\s(\d*)\sunités\sde\smétal\set\s(\d*)\sunités\sde\scristal\sse\sforme\sdans\sl\'orbite\sde\scette\splanète#',$data,$recyclage)) $recyclage[1]=$recyclage[2]=0; 
      if (!preg_match('#La\sprobabilité\sde\scréation\sd\'une\slune\sest\sde\s(\d*)\s%#',$data,$plune)) $plune[1] = 0;
      $lune = preg_match('#Les\squantités\sénormes\sde\smétal\set\sde\scristal\ss\'attirent,\sformant\sainsi\sune\slune\sdans\sl\'orbite\sde\scette\splanète#',$data);
      // calcul la date et l'heure du rapport
      $date = mktime($date[3],$date[4],$date[5],$date[1],$date[2],date('Y'));
      //Calcul des points en fonction des coeficients
      $points = ceil(($ressources[1]+$ressources[2]+$ressources[3])/100000*$config['pillage'] + $pertesA[1]/100000*$config['pertes'] + $pertesD[1]/100000*$config['degats'] + $lune*$config['clune']);
      //On vérifie que cette attaque n'a pas déja été enregistrée
      $query = "SELECT id FROM ".TABLE_GAME." WHERE sender='$user_data[user_id]' AND date='$date' AND attaquant='$attaquant[1]' ";
      $result = $db->sql_query($query);
      $nb = $db->sql_numrows($result);
      // Si le RC existe déjà on sort 
      if ($nb != 0) return 3;
      //Insert dans la base de données
	  $sql = 'INSERT INTO '.TABLE_GAME.' (id,sender,date,attaquant,coord_att,defenseur,coord_def,pertesA,pertesD,lune,`%lune`,pillageM,pillageC,pillageD,recyclageM,recyclageC,recycleM,recycleC,points,rawdata) VALUES (\'\',\''.$user_data['user_id'].'\',\''.$date.'\',\''.mysql_real_escape_string($attaquant[1]).'\',\''.mysql_real_escape_string($coord_att[1]).'\',\''.mysql_real_escape_string($defenseur[1]).'\',\''.mysql_real_escape_string($coord_def[1]).'\',\''.$pertesA[1].'\',\''.$pertesD[1].'\',\''.$lune.'\',\''.$plune[1].'\',\''.$ressources[1].'\',\''.$ressources[2].'\',\''.$ressources[3].'\',\''.$recyclage[1].'\',\''.$recyclage[2].'\',\'0\',\'0\',\''.$points.'\',\''.mysql_real_escape_string($data).'\')';
      //$sql = 'INSERT INTO '.TABLE_GAME.' (id,sender,date,attaquant,defenseur,pertesA,pertesD,lune,`%lune`,pillageM,pillageC,pillageD,recyclageM,recyclageC,recycleM,recycleC,points,rawdata) VALUES (\'\',\''.$user_data['user_id'].'\',\''.$date.'\',\''.mysql_real_escape_string($attaquant[1]).'\',\''.mysql_real_escape_string($defenseur[1]).'\',\''.$pertesA[1].'\',\''.$pertesD[1].'\',\''.$lune.'\',\''.$plune[1].'\',\''.$ressources[1].'\',\''.$ressources[2].'\',\''.$ressources[3].'\',\''.$recyclage[1].'\',\''.$recyclage[2].'\',\'0\',\'0\',\''.$points.'\',\''.mysql_real_escape_string($data).'\')';
      $db->sql_query($sql);
	
    }
  }
}
?>
