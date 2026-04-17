<?php

/**
 *   _xtense.php - fichier d'interface avec Xtense2
 *
 * @package Attaques
 * @author ericc
 * @link https://www.ogsteam.eu
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
    global $db, $table_prefix, $attack_config, $user_data, $xtense_user_data, $log;
    define("TABLE_ATTAQUES_ATTAQUES", $table_prefix . "attaques_attaques");
    read_config();

    $log->debug('Attaques::attack_rc called', ['rapport_keys' => array_keys($rapport)]);

    if (!isset($rapport['json'])) {
        $log->warning('Attaques::attack_rc: rapport[json] missing');
        return false;
    }

    // $rapport['json'] is the full outer xtense wrapper JSON string.
    // The actual RC payload is in the 'data' field (itself a JSON string).
    $outer = json_decode($rapport['json'], true);
    $log->debug('Attaques::attack_rc: outer decode', [
        'json_error' => json_last_error_msg(),
        'outer_keys' => is_array($outer) ? array_keys($outer) : 'NOT_ARRAY',
    ]);
    if (!isset($outer['data'])) {
        $log->warning('Attaques::attack_rc: outer[data] missing');
        return false;
    }

    $rc = json_decode($outer['data'], true);
    $log->debug('Attaques::attack_rc: inner decode', [
        'json_error' => json_last_error_msg(),
        'rc_keys'    => is_array($rc) ? array_keys($rc) : 'NOT_ARRAY',
    ]);
    if (!is_array($rc)) {
        $log->warning('Attaques::attack_rc: inner data is not an array');
        return false;
    }

    // Coordonnées où a eu lieu l'attaque — stored as "g:s:p" in the new format
    $coord_attaque = $rc['coordinates'] ?? null;
    $log->debug('Attaques::attack_rc: coordinates', ['coord_attaque' => $coord_attaque]);
    if ($coord_attaque === null) {
        $log->warning('Attaques::attack_rc: rc[coordinates] missing');
        return false;
    }

    // Winner / loot
    $winner = $rc['result']['winner'] ?? '';
    $log->debug('Attaques::attack_rc: winner', ['winner' => $winner]);
    if ($winner !== 'attacker') {
        $winmetal   = 0;
        $wincristal = 0;
        $windeut    = 0;
    } else {
        $winmetal   = 0;
        $wincristal = 0;
        $windeut    = 0;
        foreach ($rc['result']['loot']['resources'] ?? [] as $resource) {
            switch ($resource['resource']) {
                case 'metal':     $winmetal   = (int)$resource['amount']; break;
                case 'crystal':   $wincristal = (int)$resource['amount']; break;
                case 'deuterium': $windeut    = (int)$resource['amount']; break;
            }
        }
    }
    $log->debug('Attaques::attack_rc: loot', ['metal' => $winmetal, 'crystal' => $wincristal, 'deut' => $windeut]);

    // Unit losses
    $pertes_attacker = 0;
    $pertes_defender = 0;
    foreach ($rc['result']['totalValueOfUnitsLost'] ?? [] as $loss) {
        if ($loss['side'] === 'attacker') $pertes_attacker = (int)$loss['value'];
        if ($loss['side'] === 'defender') $pertes_defender = (int)$loss['value'];
    }
    $pertes = $pertes_attacker;
    $log->debug('Attaques::attack_rc: losses', ['attacker' => $pertes_attacker, 'defender' => $pertes_defender]);

    $timestamp = $rc['timestamp'] ?? null;
    $log->debug('Attaques::attack_rc: timestamp', ['timestamp' => $timestamp]);
    if ($timestamp === null) {
        $log->warning('Attaques::attack_rc: rc[timestamp] missing');
        return false;
    }

    // Récupération des coordonnées des attaquants et défenseurs
    $coords_attaquants = array();
    $coords_defenseurs = array();
    foreach ($rc['fleets'] ?? [] as $fleet) {
        $c     = $fleet['spaceObject']['coordinates'] ?? null;
        if ($c === null) {
            $log->warning('Attaques::attack_rc: fleet missing spaceObject.coordinates', ['fleet_side' => $fleet['side'] ?? 'unknown']);
            continue;
        }
        $coord = "{$c['galaxy']}:{$c['system']}:{$c['position']}";
        if ($fleet['side'] === 'attacker') {
            $coords_attaquants[] = $coord;
        } else {
            $coords_defenseurs[] = $coord;
        }
    }
    $log->debug('Attaques::attack_rc: fleet coords', [
        'attackers' => $coords_attaquants,
        'defenders' => $coords_defenseurs,
    ]);

    //On regarde dans les coordonnées de l'espace personnel du joueur qui insère les données via le plugin si il fait partie des attaquants et/ou des défenseurs
    $log->debug('Attaques::attack_rc: user_id', ['id' => $xtense_user_data['id'] ?? 'NULL']);
    $query = "SELECT CONCAT(`galaxy`, ':', `system`, ':', `row`) FROM " . TABLE_USER_BUILDING . " WHERE `last_update_user_id` = '" . (int)$xtense_user_data['id'] . "'";
    $result = $db->sql_query($query);
    $coordinates = array();
    while ($coordinate = $db->sql_fetch_row($result)) {
        $coordinates[] = $coordinate[0];
    }
    $log->debug('Attaques::attack_rc: user coordinates', ['count' => count($coordinates), 'coords' => $coordinates]);

    $attaquant = 0;
    $defenseur = 0;

    if (count(array_intersect($coords_attaquants, $coordinates)) > 0) {
        $attaquant = 1;
    }
    if (count(array_intersect($coords_defenseurs, $coordinates)) > 0) {
        $defenseur = 1;
    }
    $log->debug('Attaques::attack_rc: role', ['attaquant' => $attaquant, 'defenseur' => $defenseur, 'config_defenseur' => $attack_config['defenseur'] ?? 0]);

    /*Cas 1 : Attaquant = 0 Def = 0 Config = 0|1 -> RC Refusé
      Cas 2 : Attaquant = 0 Def = 1 Config = 0 -> RC Refusé
      Cas 3 : Attaquant = 0 Def = 1 Config = 1 -> RC Accepté
      Cas 4 : Attaquant = 1 Def = 0 Config = 0 -> RC Accepté
      Cas 5 : Attaquant = 1 Def = 0 Config = 1 -> RC Accepté
      Cas 6 : Impossible Att toujours différent de Def
     */
    if ($attaquant !== 1 && ($defenseur !== 1 || ($attack_config['defenseur'] ?? 0) !== 1)) {
        $log->debug('Attaques::attack_rc: RC refusé (utilisateur non impliqué)');
        return false;
    } else {
        if ($defenseur === 1 && ($attack_config['defenseur'] ?? 0) === 1) {
            //Récupération des pertes défenseurs
            $pertes = $pertes_defender;
            //On soustrait les ressources volées
            $winmetal = -$winmetal;
            $wincristal = -$wincristal;
            $windeut = -$windeut;
        }

        //On vérifie que cette attaque n'a pas déja été enregistrée
        $query = "SELECT `attack_id` FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE `attack_user_id` ='" . $xtense_user_data['id'] . "' AND `attack_date`='$timestamp' AND `attack_coord`='$coord_attaque' ";
        $result = $db->sql_query($query);
        $nb = $db->sql_numrows($result);

        if ($nb == 0) {
            //On insere ces données dans la base de données
            $query = "INSERT INTO " . TABLE_ATTAQUES_ATTAQUES . " ( `attack_id` , `attack_user_id` , `attack_coord` , `attack_date` , `attack_metal` , `attack_cristal` , `attack_deut` , `attack_pertes` )
                    VALUES (
                        NULL , '" . $xtense_user_data['id'] . "', '" . $coord_attaque . "', '" . $timestamp . "', '" . $winmetal . "', '" . $wincristal . "', '" . $windeut . "', '" . $pertes . "')";
            $db->sql_query($query);
            $log->info('Attaques::attack_rc: RC enregistré', ['coord' => $coord_attaque, 'timestamp' => $timestamp]);
        } else {
            $log->debug('Attaques::attack_rc: RC déjà enregistré (doublon ignoré)', ['coord' => $coord_attaque, 'timestamp' => $timestamp]);
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
    global $db, $table_prefix, $user_data, $xtense_user_data;

    define("TABLE_ATTAQUES_RECYCLAGES", $table_prefix . "attaques_recyclages");

    if (!$rapport['time']) {
        return false;
    } else {
        $timestamp = $rapport['time'];
        $coordonne = $rapport['coords'][0] . ":" . $rapport['coords'][1] . ":" . $rapport['coords'][2];
        //On vérifie que ce recyclage n'a pas déja été enregistrée
        $query = "SELECT `recy_id` FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE `recy_user_id` ='" . $xtense_user_data['id'] . "' AND `recy_date` ='$timestamp' AND `recy_coord` ='$coordonne' ";
        $result = $db->sql_query($query);
        $nb = $db->sql_numrows($result);
        // Si on ne trouve rien
        if ($nb == 0) {
            //On insere ces données dans la base de données
            $query = "INSERT INTO " . TABLE_ATTAQUES_RECYCLAGES . " ( `recy_id` , `recy_user_id` , `recy_coord` , `recy_date` , `recy_metal` , `recy_cristal` )
                VALUES (
                    NULL , '" . $xtense_user_data['id'] . "', '" . $coordonne . "', '" . $timestamp . "', '" . $rapport['M_reco'] . "', '" . $rapport['C_reco'] . "')";
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
    $attack_config = isset($configs[0]) ? json_decode($configs[0], true) : [];
    if (!is_array($attack_config)) {
        $attack_config = [];
    }
}

