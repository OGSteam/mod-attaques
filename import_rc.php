<?php
/**
* import_rc.php
* @package Attaques
* @author Verité - ericc 
* @link http://www.ogsteam.fr
* @version : 0.8b
**/

/**
* Importation d'un RC dans le mod à partir d'une barre (extension firefox)
* @param string $rapport Rapport à importer
* @return 0 si mod non activé
* @return 1 si RC non valide
* @return 2 si $pseudo n'est pas l'attaquant du RC
* @return 3 si le RC a déja été enregistré
* @return 4 si le RC à bien été inséré
**/
function import_rc($rapport)
{
  //Définitions
  global $db, $table_prefix, $user_data;
  define("TABLE_ATTAQUES_ATTAQUES", $table_prefix."attaques_attaques");
  //récupération des paramètres de config
  $query = "SELECT value FROM `".TABLE_MOD_CFG."` WHERE `mod`='Attaques' and `config`='config'";
  $result = $db->sql_query($query);
  $config = $db->sql_fetch_row($result);
  $config=unserialize($config[0]);  
  // Initialisation variable pour insertion GameOgame
  $rc_game=$rapport;
  // On vérifie que gameOgame est bien installé et actif
  $query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `title`='gameOgame' LIMIT 1";
  $result = $db->sql_query($query);
  $data = $db->sql_fetch_assoc($result);
  $gameOgame_plugin_file = "mod/gameOgame/index.php";
  $gameOgame_exists = file_exists($gameOgame_plugin_file);
  if($gameOgame_exists==true AND $data[active]==1) 
    {
    require_once('./mod/gameOgame/include.php');
    require_once('gog_function.php');
    report($rc_game);
    }
	$handle=fopen("gog.txt","w");
    fwrite($handle,"import_rc--------\n");
	fwrite($handle,$rc_game);
    fwrite($handle,"--------\n");
	fclose($handle);
	
   //fwrite($handle,"\r\n");
   //fwrite($handle,$config[defenseur]);
   //fwrite($handle,"\r\n");  */
	
	//On vérifie que le mod est activé
   $query = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
   if (!$db->sql_numrows($db->sql_query($query))) return 0;

   $rapport = str_replace(".","",$rapport);  
   //Compatibilité UNIX/Windows
   $rapport = str_replace("\r\n","\n",$rapport);
   //Compatibilité IE/Firefox
   $rapport = str_replace("\t",' ',$rapport);

    //$handle=fopen("test.txt","a");
   //fwrite($handle,$rapport);
   //fwrite($handle,"\r\n");
   //fwrite($handle,$config[defenseur]);
   //fwrite($handle,"\r\n");  
   //On regarde si le rapport soumis est un RC
   if (!preg_match('#Les\sflottes\ssuivantes\sse\ssont\saffrontées\sle\s(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2}) :#',$rapport,$date))
   {
      return 1;
   }
   //On vérifie que le pseudo de l'attaquant est bien le pseudo du joueur
  preg_match('#Attaquant\s.{3,110}\[(.{5,8})]#',$rapport,$pre_coordA);
  $coord_attaquant = $pre_coordA[1];
  
  //fwrite($handle,$coord_attaquant);
  //fwrite($handle,"\r\n");
  
  preg_match('#Défenseur\s.*\[(.*)]#',$rapport,$pre_coordD);
  $coord_defenseur = $pre_coordD[1];
  
  //fwrite($handle,$coord_defenseur);
  //fwrite($handle,"\r\n"); 
    
   //On regarde dans les coordonnées de l'espace personnel du joueur qui insère les données via le plugin si les coordonnées de l'attaquant correspondent à une de ses planètes
  $query = "SELECT coordinates FROM ".TABLE_USER_BUILDING." WHERE user_id='$user_data[user_id]'";
  $result = $db->sql_query($query);
  $attaquant = 0;
  $defenseur = 0;
   	while(list($coordinates) = $db->sql_fetch_row($result))
	{
		if($coordinates == $coord_attaquant) $attaquant = 1;
		if($coordinates == $coord_defenseur) $defenseur = 1;
	}
  if($attaquant != 1 && $config[defenseur] != 1) return 2;
  if($attaquant != 1 && $defenseur != 1) return 2;
  

  // récuperation des ressources pillées
  preg_match('#(\d*)\sunités\sde\smétal,\s(\d*)\sunités\sde\scristal\set\s(\d*)\sunités\sde\sdeutérium#',$rapport,$ressources);

  if ($attaquant == 1)
  {
    //Récupération des pertes Attaquant 
    preg_match('#attaquant\sa\sperdu\sau\stotal\s(\d*)\sunités#',$rapport,$pertesA);
    $pertes = $pertesA[1];
    $coord_attaque = $coord_defenseur; 
   }
  if ($defenseur == 1 && $config[defenseur] == 1)
  {
    // récupération des pertes défenseurs
    preg_match('#défenseur\sa\sperdu\sau\stotal\s(\d*)\sunités#',$rapport,$pertesD);
    $pertes = $pertesD[1];
    //les coordonnées de l'attaque deviennent celle de l'attaquant
    $coord_attaque = $coord_attaquant;
    //on soustrait les ressources volées
    $ressources[1] = -$ressources[1];
    $ressources[2] = -$ressources[2];
    $ressources[3] = -$ressources[3];
  }
    
  $timestamp = mktime($date[3],$date[4],$date[5],$date[1],$date[2],date('Y'));
      
   //Puis les informations pour les coordonnées
  //preg_match('#Défenseur\s.+\[(.+)]#',$rapport,$pre_coord);
  //$coord_attaque = $pre_coord[1];
   
   //On vérifie que cette attaque n'a pas déja été enregistrée
   $query = "SELECT attack_id FROM ".TABLE_ATTAQUES_ATTAQUES." WHERE attack_user_id='$user_data[user_id]' AND attack_date='$timestamp' AND attack_coord='$coord_attaque' ";
   $result = $db->sql_query($query);
   $nb = $db->sql_numrows($result);
   if ($nb != 0) return 3;
   
   //On insere ces données dans la base de données
   $query = "INSERT INTO ".TABLE_ATTAQUES_ATTAQUES." ( `attack_id` , `attack_user_id` , `attack_coord` , `attack_date` , `attack_metal` , `attack_cristal` , `attack_deut` , `attack_pertes` )
      VALUES (
         NULL , '$user_data[user_id]', '$coord_attaque', '$timestamp', '$ressources[1]', '$ressources[2]', '$ressources[3]', '$pertes'
      )";
   $db->sql_query($query);
   
   //On ajoute l'action dans le log
   $line = $user_data[user_name]." ajoute une attaque dans le module de gestion des attaques via le plugin Xtense";
   $fichier = "log_".date("ymd").'.log';
   $line = "/*".date("d/m/Y H:i:s").'*/ '.$line;
   write_file(PATH_LOG_TODAY.$fichier, "a", $line);
   //Et on valide l'ajout du rc
   return 4;
}

/**
* Importation d'un Rapport de recyclage dans le mod à partir d'une barre (extension firefox)
* @param string $rapport Rapport à importer
* @return 0 si mod non activé
* @return 1 si le rapport a été correctement enregistré
* @return 2 si le rapport n'est pas correcte
* @return 3 si le rapport a déja été enregistré
**/

//Fonction d'ajout d'un rapport de recyclage
function import_recycl ( $pub_rapport )
{
  global $user_data, $db, $table_prefix, $fp;
  define ( 'TABLE_ATTAQUES_RECYCLAGES', $table_prefix . 'attaques_recyclages' );
  $handle=fopen("firespy.txt","a");
  $data = $pub_rapport;
  fwrite($handle,$data);
  if ( defined ( 'OGS_PLUGIN_DEBUG' ) ) fwrite ( $fp, 'Début importation rapport de recyclage(import_recycl) ' . count ( $pub_rapport )." lignes\n" );
  //if (preg_match('#(\d{2})-(\d{2})\s+(\d{2})\:(\d{2})\:(\d{2})\s+Flotte\s+Rapport\sd\'exploitation\sdu\schamp\sde\sdébris\saux\scoordonnées#',$pub_rapport,$tab_recy_header)===false)
  if (preg_match('#.+(\d{2})-(\d{2})\s+(\d{2})\:(\d{2})\:(\d{2})\s+Flotte\s+Rapport\sd\'exploitation\sdu\schamp\sde\sdébris#',$pub_rapport,$tab_recy_header)===false)
    return 2;
  
  $timestamp = mktime($tab_recy_header[3],$tab_recy_header[4],$tab_recy_header[5],$tab_recy_header[1],$tab_recy_header[2],date('Y'));
  if (defined("OGS_PLUGIN_DEBUG")) fwrite($fp, "Analyse rapport recyclage(".count($tab_recy_header)."): ".$tab_recy_header[1]." ".$tab_recy_header[2]." ".$tab_recy_header[3]." ".$tab_recy_header[4]." ".$tab_recy_header[5]." ".$tab_recy_header[6]."\n");
  
  if (preg_match('#Vos\s(\d+)\srecycleurs\sont\sune\scapacité\stotale\sde\s([\d\.]+).\s([\d\.]+)\sunités\sde\smétal\set\s([\d\.]+)\sunités\sde\scristal\ssont\sdispersées\sdans\sce\schamp.\sVous\savez\scollecté\s([\d\.]+)\sunités\sde\smétal\set\s([\d\.]+)\sunités\sde\scristal.#',$pub_rapport,$tab_recy_body)===false)
    return 2;
  if (defined("OGS_PLUGIN_DEBUG")) fwrite($fp, "Analyse rapport recyclage(".count($tab_recy_body)."): ".$tab_recy_body[1]." ".$tab_recy_body[2]." ".$tab_recy_body[3]." ".$tab_recy_body[4]." ".$tab_recy_body[5]." ".$tab_recy_body[6]."\n");
  
  preg_match('#Flotte\s+Rapport\sd\'exploitation\sdu\schamp\sde\sdébris.*\[(.*)]#',$pub_rapport,$recy_coord);
  $recy_coord=$recy_coord[1];
  if (!isset($recy_coord)or($recy_coord==""))
    {
    $recy_coord = '1:1:1';
    }
    
  $recy_metal = str_replace('.', '', $tab_recy_body[5]);
  $recy_cristal = str_replace('.', '',$tab_recy_body[6]);
  if (defined("OGS_PLUGIN_DEBUG")) fwrite($fp, "Analyse rapport recyclage: métal $recy_metal, cristal $recy_cristal à $recy_coord \n");
  
  //On vérifie que ce recyclage n'a pas déja été enregistré
  $query = "SELECT recy_id  FROM ".TABLE_ATTAQUES_RECYCLAGES." WHERE recy_user_id ='$user_data[user_id]' AND recy_date = '$timestamp' AND recy_coord = '$recy_coord' ";
  if (defined("OGS_PLUGIN_DEBUG")) fwrite($fp, "requète verif recyclage: ".$query."\n");
  $result = $db->sql_query($query);
  $nb = $db->sql_numrows($result);
  if ($nb > 0) return 3; // déjà enregistré
  
  //On insere ces données dans la base de données
  $query = "INSERT INTO ".TABLE_ATTAQUES_RECYCLAGES." ( `recy_id` , `recy_user_id` , `recy_coord` , `recy_date` , `recy_metal` , `recy_cristal` )
    VALUES ( NULL , '$user_data[user_id]', '$recy_coord', '$timestamp', '$recy_metal', '$recy_cristal' )";
  $db->sql_query($query);
  
  //On ajoute l'action dans le log
  $line = $user_data[user_name]." ajoute un rapport de recyclage dans le module de gestion des attaques via l'extension OGS Plugin/Xtense";
  $fichier = "log_".date("ymd").'.log';
  $line = "/*".date("d/m/Y H:i:s").'*/ '.$line;
  write_file(PATH_LOG_TODAY.$fichier, "a", $line);
  
  //Et on valide l'ajout du rc
  return 1;
}
?>