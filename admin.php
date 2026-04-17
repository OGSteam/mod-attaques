<?php

/**
 * admin.php Page de configuration - Accessible uniquement par les admins
 *
 * @package Attaques
 * @author  ericc
 * @link https://www.OGSteam.eu
 * @version : 0.8e
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

global $db, $log, $table_prefix, $prefixe;
// lecture des bbcodes dans la db
$bbcolor = mod_get_option('bbcodes');
$bbcolor = json_decode($bbcolor, true);

// Paramètres de configurations transmis par le form
if (isset($pub_submit)) {
    if (isset($pub_transp)) {
        settype($pub_transp, "integer");
        if (is_int($pub_transp)) {
            $config['transp'] = $pub_transp;
        }
        if (isset($pub_layer)) {
            $config['layer'] = 1;
        } else {
            $config['layer'] = 0;
        }
        if (isset($pub_defenseur)) {
            $config['defenseur'] = 1;
        } else {
            $config['defenseur'] = 0;
        }
        if (isset($pub_histo)) {
            $config['histo'] = 1;
        } else {
            $config['histo'] = 0;
        }
        if (isset($pub_timezone) && @timezone_open(trim($pub_timezone)) !== false) {
            $config['timezone'] = trim($pub_timezone);
        } else {
            $config['timezone'] = $config['timezone'] ?? 'UTC';
        }
        $sqldata = json_encode($config);
        mod_set_option('config', $sqldata);
    }
    echo "<span style='color: #00FF40;'>Configuration sauvegardée</span><br />";
}
// Fin paramètres de configuration

// Paramètres couleurs BBcodes
if (isset($pub_submitbb)) {
    $bbcolor['title'] = substr($pub_title, 0, 7);
    $bbcolor['m_g'] = substr($pub_m_g, 0, 7);
    $bbcolor['c_g'] = substr($pub_c_g, 0, 7);
    $bbcolor['d_g'] = substr($pub_d_g, 0, 7);
    $bbcolor['m_r'] = substr($pub_m_r, 0, 7);
    $bbcolor['c_r'] = substr($pub_c_r, 0, 7);
    $bbcolor['perte'] = substr($pub_perte, 0, 7);
    $bbcolor['renta'] = substr($pub_renta, 0, 7);
    //echo "#".dechex(hexdec(substr($pub_title,0,7)))."\n\r"; test de validation de code hexa .. pb si débute par 00
    $sqldata = json_encode($bbcolor);
    mod_set_option('bbcodes', $sqldata);
    echo "<span style='color: #00FF40;'>Couleurs BBcode enregistr&eacute;es</span><br />";
}
// Fin paramètres couleurs BBcodes
// Purge des anciennes archives
if (isset($pub_submitpurg)) {
    // On récupère les dates des archives présentes dans la base de données par ordre croissant
    $query = "SELECT DISTINCT `archives_date` FROM " . TABLE_ATTAQUES_ARCHIVES . " order by `archives_date`";
    $result = $db->sql_query($query);
    while (list($date) = $db->sql_fetch_row($result)) {
        $ann[] = $date;
    }
    for ($i = 0; $i < count($ann); $i++) {
        if ($ann[$i] <= $pub_purge) {
            $query = "DELETE FROM " . TABLE_ATTAQUES_ARCHIVES . " WHERE `archives_date`=" . $ann[$i];
            if (!$db->sql_query($query)) die("erreur SQL");
        } else {
            break;
        }
    }
    // On optimize la table
    $query = "OPTIMIZE TABLE " . TABLE_ATTAQUES_ARCHIVES;
    if (!$db->sql_query($query)) die("erreur SQL");
    echo "<span style='color: #00FF40;'>Purge effectu&eacute;e</span><br />";
}
// Fin de la purge
// Nettoyage des valeurs non attribués dans la DB
if (isset($pub_submitid)) {
    //on récupère les données recues par $_POST sans oublié d'enlevé les slash ^^
    $userid = json_decode(stripslashes($pub_val_id));
    foreach ($userid as $value) {
        // En fonction du type de la table, on génère la query
        if ($value[0] == "archive") {
            $query = "DELETE FROM " . TABLE_ATTAQUES_ARCHIVES . " WHERE `archives_user_id`=" . $value[1];
        } elseif ($value[0] == "attack") {
            $query = "DELETE FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE `attack_user_id`=" . $value[1];
        } elseif ($value[0] == "recy") {
            $query = "DELETE FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE `recy_user_id`=" . $value[1];
        }
        // et on execute
        if (!$db->sql_query($query)) die("erreur SQL");
    }
    // On optimize les tables parce que je suis propre :-)
    $query = "OPTIMIZE TABLE " . TABLE_ATTAQUES_ARCHIVES;
    if (!$db->sql_query($query)) die("erreur SQL");
    $query = "OPTIMIZE TABLE " . TABLE_ATTAQUES_ATTAQUES;
    if (!$db->sql_query($query)) die("erreur SQL");
    $query = "OPTIMIZE TABLE " . TABLE_ATTAQUES_RECYCLAGES;
    if (!$db->sql_query($query)) die("erreur SQL");
    echo "<span style='color: #00FF40;'>Nettoyage effectu&eacute;</span><br />";
}
// Fin du nettoyage
// Connexion Xtense2
if (isset($pub_submitxt2)) {
    // on récupère le n° d'id du mod
    $query = "SELECT `id` FROM `" . TABLE_MOD . "` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
    $result = $db->sql_query($query);
    $attack_id = $db->sql_fetch_row($result);
    $attack_id = $attack_id[0];
    // on fait du nettoyage au cas ou
    $query = "DELETE FROM `" . $table_prefix . "xtense_callbacks" . "` WHERE `mod_id`=" . $attack_id;
    $db->sql_query($query);
    // Insert les données pour récuperer les RC
    $query = "INSERT INTO " . $table_prefix . "xtense_callbacks" . " ( `mod_id` , `function` , `type` )
                VALUES ( '" . $attack_id . "', 'attack_rc', 'rc')";
    $db->sql_query($query);
    // Insert les données pour récuperer les RR
    $query = "INSERT INTO " . $table_prefix . "xtense_callbacks" . " ( `mod_id` , `function` , `type` )
                VALUES ( '" . $attack_id . "', 'attack_rr', 'rc_cdr')";
    $db->sql_query($query);
}

// cadre autour des paramètres
echo "<div class='og-msg' style='max-width: 800px;'>";
echo "<h3 class='og-title'>Administration " . help("attaques_Administration") . "</h3>";
echo "<div class='og-content'>";
// Formulaire des paramètres du module
echo "<form name='form1' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'>";
echo "<div class='attaques-filter-row'><b>Param&egrave;tres du module</b></div>";
echo "<div class='attaques-filter-row'><label>Activer le layer " . help("attaques_layer") . " : <input type='checkbox' name='layer' value='true' ";
if ($config['layer'] == 1) {
    echo 'checked=checked';
}
echo "></label></div>";
echo "<div class='attaques-filter-row'><label>Valeur d'opacit&eacute; " . help("attaques_transparence") . " : <input type='text' name='transp' value='" . $config['transp'] . "' size='4'> %</label></div>";
echo "<div class='attaques-filter-row'><label>Prise en compte des Attaques subies : <input type='checkbox' name='defenseur' value='true' ";
if ($config['defenseur'] == 1) {
    echo 'checked=checked';
}
echo "></label></div>";
echo "<div class='attaques-filter-row'><label>Affichage des histogrammes : <input type='checkbox' name='histo' value='true' ";
if ($config['histo'] == 1) {
    echo 'checked=checked';
}
echo "></label></div>";
echo "<div class='attaques-filter-row'><label>Fuseau horaire (ex: Europe/Paris) : <input type='text' name='timezone' value='" . htmlspecialchars($config['timezone'] ?? 'UTC') . "' size='30' placeholder='UTC'></label></div>";
echo "<div class='attaques-filter-row'><input name='submit' type='submit' value='Enregistrer' class='og-button'></div>";
echo "</form>";

// Formulaire des BBcodes
echo "<hr style='border-color: var(--color-button-border); margin: 16px 0;'>";
echo "<form name='form2' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'>";
echo "<div class='attaques-filter-row'><b>BBCodes&nbsp;" . help("attaques_bbcolor") . "</b></div>";
echo "<div style='display:grid; grid-template-columns: 1fr 100px 1fr 100px; gap: 8px; align-items: center; margin-bottom: 8px;'>";
echo "<span>Titre</span><input type='text' name='title' value='" . $bbcolor['title'] . "' size='7'>";
echo "<span>M&eacute;tal gagn&eacute;</span><input type='text' name='m_g' value='" . $bbcolor['m_g'] . "' size='7'>";
echo "<span>Cristal gagn&eacute;</span><input type='text' name='c_g' value='" . $bbcolor['c_g'] . "' size='7'>";
echo "<span>Deut&eacute;rium gagn&eacute;</span><input type='text' name='d_g' value='" . $bbcolor['d_g'] . "' size='7'>";
echo "<span>M&eacute;tal recycl&eacute;</span><input type='text' name='m_r' value='" . $bbcolor['m_r'] . "' size='7'>";
echo "<span>Cristal recycl&eacute;</span><input type='text' name='c_r' value='" . $bbcolor['c_r'] . "' size='7'>";
echo "<span>Perte</span><input type='text' name='perte' value='" . $bbcolor['perte'] . "' size='7'>";
echo "<span>Rentabilit&eacute;</span><input type='text' name='renta' value='" . $bbcolor['renta'] . "' size='7'>";
echo "</div>";
echo "<div class='attaques-filter-row'><a href='https://www.w3schools.com/colors/colors_picker.asp' target='_blank'>Color Picker</a></div>";
echo "<div class='attaques-filter-row'><input name='submitbb' type='submit' value='Enregistrer' class='og-button'></div>";
echo "</form>";

// Formulaire "Base de Donnees"
echo "<hr style='border-color: var(--color-button-border); margin: 16px 0;'>";
echo "<form name='form3' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'>";
echo "<div class='attaques-filter-row'><b>Base de donn&eacute;es " . help("attaques_mysql") . "</b></div>";
// On récupère les dates des archives présentes dans la base de données par ordre croissant
$query = "SELECT DISTINCT `archives_date` FROM " . TABLE_ATTAQUES_ARCHIVES . " order by `archives_date`";
$result = $db->sql_query($query);
//reinitialise l'array
$ann = array();
while (list($date) = $db->sql_fetch_row($result)) {
    $ann[] = $date;
}
if (isset($ann[0])) {
    echo "<div class='attaques-filter-row'>Vous avez des archives depuis " . date("M Y", $ann[0]) . ". A partir de quand souhaitez vous purger ?</div>";
    echo "<div class='attaques-filter-row' style='display:flex; flex-wrap:wrap; gap:8px;'>";
    $count = 0;
    for ($i = 0; $i < count($ann); $i++) {
        echo "<label><input type='radio' name='purge' value='" . $ann[$i] . "'> " . date("M Y", $ann[$i]) . "</label>";
        $count += 1;
    }
    echo "</div>";
} else {
    echo "<div class='attaques-filter-row'>Vous n'avez pas d'archives &agrave; purger</div>";
}
echo "<div class='attaques-filter-row'><input name='submitpurg' type='submit' value='Purger' class='og-button'></div>";
echo "</form>";
// Controle de la base de données
$query = "SELECT DISTINCT `archives_user_id` FROM " . TABLE_ATTAQUES_ARCHIVES . " ORDER BY `archives_user_id`";
$result = $db->sql_query($query);
$arch = array();
while (list($data) = $db->sql_fetch_row($result)) {
    $arch[] = $data;
}
$query = "SELECT DISTINCT `attack_user_id` FROM " . TABLE_ATTAQUES_ATTAQUES . " ORDER BY `attack_user_id`";
$result = $db->sql_query($query);
$attck = array();
while (list($data) = $db->sql_fetch_row($result)) {
    $attck[] = $data;
}
$query = "SELECT DISTINCT `recy_user_id` FROM " . TABLE_ATTAQUES_RECYCLAGES . " ORDER BY `recy_user_id`";
$result = $db->sql_query($query);
$recy = array();
while (list($data) = $db->sql_fetch_row($result)) {
    $recy[] = $data;
}
$inval_id = 0;
//récupère la liste des users actifs
$query = "SELECT `id`,`active` from " . TABLE_USER . " ORDER BY `id`";
$result = $db->sql_query($query);
$count = 0;
$id = array();
// Seul le userid m'interesse
while (list($userid, $actif) = $db->sql_fetch_row($result)) {
    if ($actif != 0) {
        $membre[$count] = $userid;
        $count += 1;
    }
}
//On vérifie si les ID trouvé dans la DB sont dans la liste des userid actifs
for ($i = 0; $i < count($arch); $i++) {
    if (!in_array($arch[$i], $membre)) {
        // si non, on sauvegarde l'ID trouvé avec le type de la table correspondant et on incrémente le compteur
        $id[] = array("archive", $arch[$i]);
        $inval_id++;
    }
}
for ($i = 0; $i < count($attck); $i++) {
    if (!in_array($attck[$i], $membre)) {
        $id[] = array("attack", $attck[$i]);
        $inval_id++;
    }
}
for ($i = 0; $i < count($recy); $i++) {
    if (!in_array($recy[$i], $membre)) {
        $id[] = array("recy", $recy[$i]);
        $inval_id++;
    }
}
//Si j'ai trouvé des données orphelines et seulement dans ce cas, j'affiche un simple form
if ($inval_id > 0) {
    //On serialize l'array pour le transmettre par $_POST dans le form
    $trans_id = json_encode($id);
    echo "<hr style='border-color: var(--color-button-border); margin: 16px 0;'>";
    echo "<form name='form4' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'>";
    echo "<div class='attaques-filter-row'>Des donn&eacute;es n'appartenant &agrave; aucun joueur actif ont &eacute;t&eacute; trouv&eacute;es dans la base de donn&eacute;es.</div>";
    echo "<div class='attaques-filter-row'>Souhaitez-vous les supprimer ?";
    echo "<input name='val_id' type='hidden' value='" . $trans_id . "'>";
    echo "</div>";
    echo "<div class='attaques-filter-row'><input name='submitid' type='submit' value='Supprimer' class='og-button'></div>";
    echo "</form>";
}

//Connexion Xtense2
echo "<hr style='border-color: var(--color-button-border); margin: 16px 0;'>";
echo "<form name='form5' action='index.php?action=attaques&page=admin' enctype='multipart/form-data' method='post'>";
echo "<div class='attaques-filter-row'><b>Xtense&nbsp;" . help("attaques_xtense") . "</b></div>";
//On vérifie que la table xtense_callbacks existe
if (!$db->sql_numrows($db->sql_query("SHOW TABLES LIKE '" . $table_prefix . "xtense_callbacks" . "'"))) {
    echo "<div class='attaques-filter-row'>Le Module Xtense semble ne pas &ecirc;tre install&eacute;</div>";
} else {
    // Si oui, on récupère le n° d'id du mod
    $query = "SELECT `id` FROM `" . TABLE_MOD . "` WHERE `action`='attaques' AND `active`='1' LIMIT 1";
    $result = $db->sql_query($query);
    $attack_id = $db->sql_fetch_row($result);
    $attack_id = $attack_id[0];
    // Maintenant on vérifie que le mod est déclaré dans la table
    $query = "SELECT `id` FROM " . $table_prefix . "xtense_callbacks" . " WHERE `mod_id`=" . $attack_id;
    $result = $db->sql_query($query);
    // On doit avoir 2 entrées dans la table : une pour les RC, une pour les RR
    if ($db->sql_numrows($result) != 2) {
        echo "<div class='attaques-filter-row'>Le module 'Gestion des Attaques' n'est pas enregistr&eacute; aupr&egrave;s de Xtense</div>";
        echo "<div class='attaques-filter-row'>Souhaitez-vous &eacute;tablir la connexion ?</div>";
        echo "<div class='attaques-filter-row'><input name='submitxt2' type='submit' value='Connecter Xtense' class='og-button'></div>";
    } else {
        echo "<div class='attaques-filter-row'>Le module 'Gestion des Attaques' est correctement enregistr&eacute; aupr&egrave;s de Xtense</div>";
    }
}
echo "</form>";
echo "</div></div>";
