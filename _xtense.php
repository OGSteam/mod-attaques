<?php

/**
 *   _xtense.php - fichier d'interface avec Xtense2
 *
 * @package Attaques
 * @author ericc
 * @link http://www.ogsteam.eu
 * @version : 0.8e
 *   created    : 17/02/2008
 *   modified    :
 **/

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

if (class_exists("Callback")) {
    /**
     * Class Attaques_Callback
     */
    class Attaques_Callback extends Callback
    {
        public $version = '2.3.10';

        /**
         * @param $rapport
         * @return int
         */
        public function attack_rc($rapport)
        {
            return  attack_rc($rapport);
        }

        /**
         * @param $rapport
         * @return int
         */
        public function attack_rr($rapport)
        {
            return attack_rr($rapport);
        }

        /**
         * @return array
         */
        public function getCallbacks()
        {
            return array(array('function' => 'attack_rc', 'type' => 'rc'), array('function' => 'attack_rr', 'type' => 'rc_cdr'));
        }
    }
}


// Version minimum de Xtense2
$xtense_version = "2.3.9";

// Import des Rapports de combats
/**
 * @param $rapport
 * @return bool
 */
function attack_rc($rapport)
{
    global $db, $table_prefix, $attack_config, $user_data;
    define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
    read_config();

    if (!isset($rapport['json']))
        return false;

    //On regarde dans les coordonnées de l'espace personnel du joueur qui insère les données via le plugin si il fait partie des attaquants et/ou des défenseurs

    $query = "SELECT `coordinates` FROM " . TABLE_USER_BUILDING . " WHERE `user_id` ='" . $user_data['user_id'] . "'";
    //log_('debug', $query);
    $result = $db->sql_query($query);
    $coordinates = array();
    while ($coordinate = $db->sql_fetch_row($result)) {
        $coordinates[] = $coordinate[0];
    }
    $rc = json_decode($rapport['json']);

    //Coordonnées où a eu lieu l'attaque
    $coord_attaque = "{$rc->coordinates->galaxy}:{$rc->coordinates->system}:{$rc->coordinates->position}";
    //log_('debug', $coord_attaque);

    if ($rc->result != 'attacker') //Si l'attaquant ne gagne pas alors il ne prend pas de ressources !
    {
        $winmetal = 0;
        $wincristal = 0;
        $windeut = 0;
    } else {
        $winmetal = $rc->loot->metal;
        $wincristal = $rc->loot->crystal;
        $windeut = $rc->loot->deuterium;
    }

    $pertes = $rc->statistic->lostUnitsAttacker;
    $timestamp = $rc->event_timestamp;

    //Récupération des coordonnées des attaquants
    $coords_attaquants = array();
    $coords_defenseurs = array();
    foreach ($rc->attacker as $attacker)
        $coords_attaquants[] = $attacker->ownerCoordinates;
    foreach ($rc->defender as $defender)
        $coords_defenseurs[] = $defender->ownerCoordinates;

    $attaquant = 0;
    $defenseur = 0;

    if (count(array_intersect($coords_attaquants, $coordinates)) > 0) {
        $attaquant = 1;
    }
    if (count(array_intersect($coords_defenseurs, $coordinates)) > 0) {
        $defenseur = 1;
    }

    // le rapport ne concerne pas l'utilisateur, ou que l'on ne tiens pas compte des attaques subies
    // On ne va pas plus loin
    //log_('debug', "Attack Config: " . $attack_config['defenseur']);
    //log_('debug', "I am the Attacker: " . $attaquant);
    //log_('debug', "I am the Defender: " . $defenseur);
    /*Cas 1 : Attaquant = 0 Def = 0 Config = 0|1 -> RC Refusé
      Cas 2 : Attaquant = 0 Def = 1 Config = 0 -> RC Refusé
      Cas 3 : Attaquant = 0 Def = 1 Config = 1 -> RC Accepté
      Cas 4 : Attaquant = 1 Def = 0 Config = 0 -> RC Accepté
      Cas 5 : Attaquant = 1 Def = 0 Config = 1 -> RC Accepté
      Cas 6 : Impossible Att toujours différent de Def
     */
    if ($attaquant !== 1 && ($defenseur !== 1 || $attack_config['defenseur'] !== 1)) { // vrai & faux | vrai
        //log_('debug', "Erreur : RC non pris en compte");
        return false;
    } else {
        if ($defenseur === 1 && $attack_config['defenseur'] === 1) {
            //Récupération des pertes défenseurs
            $pertes = $rc->statistic->lostUnitsDefender;
            //On soustrait les ressources volées
            $winmetal = -$winmetal;
            $wincristal = -$wincristal;
            $windeut = -$windeut;
        }

        //On vérifie que cette attaque n'a pas déja été enregistrée
        $query = "SELECT `attack_id` FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE `attack_user_id` ='" . $user_data['user_id'] . "' AND `attack_date`='$timestamp' AND `attack_coord`='$coord_attaque' ";
        $result = $db->sql_query($query);
        $nb = $db->sql_numrows($result);

        if ($nb == 0) {
            //On insere ces données dans la base de données
            $query = "INSERT INTO " . TABLE_ATTAQUES_ATTAQUES . " ( `attack_id` , `attack_user_id` , `attack_coord` , `attack_date` , `attack_metal` , `attack_cristal` , `attack_deut` , `attack_pertes` )
                    VALUES (
                        NULL , '" . $user_data['user_id'] . "', '" . $coord_attaque . "', '" . $timestamp . "', '" . $winmetal . "', '" . $wincristal . "', '" . $windeut . "', '" . $pertes . "')";
            $db->sql_query($query);
        }
    }

    return true;
}

/**
 * @param $rapport
 * @return bool
 */
function attack_rr($rapport)
{
    global $db, $table_prefix, $user_data;

    define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");

    if (!$rapport['time']) {
        return false;
    } else {
        $timestamp = $rapport['time'];
        $coordonne = $rapport['coords'][0] . ":" . $rapport['coords'][1] . ":" . $rapport['coords'][2];
        //On vérifie que ce recyclage n'a pas déja été enregistrée
        $query = "SELECT `recy_id` FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE `recy_user_id` ='" . $user_data['user_id'] . "' AND `recy_date` ='$timestamp' AND `recy_coord` ='$coordonne' ";
        $result = $db->sql_query($query);
        $nb = $db->sql_numrows($result);
        // Si on ne trouve rien
        if ($nb == 0) {
            //On insere ces données dans la base de données
            $query = "INSERT INTO " . TABLE_ATTAQUES_RECYCLAGES . " ( `recy_id` , `recy_user_id` , `recy_coord` , `recy_date` , `recy_metal` , `recy_cristal` )
                VALUES (
                    NULL , '" . $user_data['user_id'] . "', '" . $coordonne . "', '" . $timestamp . "', '" . $rapport['M_reco'] . "', '" . $rapport['C_reco'] . "')";
            $db->sql_query($query);
        }
        return true;
    }
}

function read_config()
{
    global $attack_config, $db;

    //récupération des paramètres de config
    $request = "SELECT `value` FROM `" . TABLE_MOD_CFG . "` WHERE `mod` = 'Attaques' AND  `config` = 'config'";
    $queryResult = $db->sql_query($request);
    $configs = $db->sql_fetch_row($queryResult);
    $attack_config = json_decode($configs[0], true);
}
