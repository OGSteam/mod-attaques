<?php

/**
 * attaques.php
 *
 * @package Attaques
 * @author Verité modifié par ericc
 * @link http://www.ogsteam.eu
 * @version : 0.8a
 */

// L'appel direct est interdit....
if (!defined('IN_SPYOGAME')) die("Hacking attempt");

// Appel des Javascripts
echo "<script type='text/javascript' src='" . FOLDER_ATTCK . "/attack.js'></script>";

//Définitions
global $db, $table_prefix, $prefixe;

//Gestion des dates
$date = date("j");
$mois = date("m");
$annee = date("Y");
$septjours = $date - 7;
$yesterday = $date - 1;

$septjours = $septjours ?? 1;
$yesterday = $yesterday ?? 1;


//On verifie si il y a des attaques qui ne sont pas du mois actuel
$query = "SELECT MONTH(FROM_UNIXTIME(attack_date)) AS month, YEAR(FROM_UNIXTIME(attack_date)) AS year, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut, SUM(attack_pertes) as pertes FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data['user_id'] . " AND MONTH(FROM_UNIXTIME(attack_date)) <> $mois GROUP BY month, year";
$result = $db->sql_query($query);

$nb_result = $db->sql_numrows($result);

//Si le nombre d'attaques n'appartenant pas au mois actuel est different de 0, on entre alors dans la partie sauvegarde des résultats anterieurs
if ($nb_result != 0) {
    echo "<span style=\"color: #FF0000; \">Vos attaques du ou des mois précédent(s) ont été supprimé(s). Seuls les gains restent accessibles dans la partie Espace Archives<br>La liste de vos attaques qui viennent d'être supprimées est consultable une dernière fois. Pensez à la sauvegarder !!!</span>";
    // On récupère les paramètres bbcolors
    $bbcolor = mod_get_option('bbcodes');
    $bbcolor = json_decode($bbcolor, true);

    while (list($month, $year, $metal, $cristal, $deut, $pertes) = $db->sql_fetch_row($result)) {
        //On recupère la liste complète des attaques de la période afin de pouvoir les compter
        $query = "SELECT attack_coord, attack_date, attack_metal, attack_cristal, attack_deut, attack_pertes FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_data['user_id'] . " AND MONTH(FROM_UNIXTIME(attack_date))=" . $month . " AND YEAR(FROM_UNIXTIME(attack_date))=" . $year . "";

        $list = $db->sql_query($query);

        $nb_ancattack = $db->sql_numrows($list);

        //On recupere les gains des recyclages
        $query = "SELECT SUM(recy_metal) as metal_recy, SUM(recy_cristal) as cristal_recy FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data['user_id'] . " AND MONTH(FROM_UNIXTIME(recy_date))=" . $month . " AND YEAR(FROM_UNIXTIME(recy_date))=" . $year . " GROUP BY recy_user_id";
        $resultrecy = $db->sql_query($query);

        //On definit le timestamp
        $timestamp = mktime(0, 0, 0, $month, 01, $year);

        list($metal_recy, $cristal_recy) = $db->sql_fetch_row($resultrecy);
        // Sql Fetch Row can set values to null if no result
        $metal_recy = $metal_recy ?? 0;
        $cristal_recy = $cristal_recy ?? 0;

        //On sauvegarde les résultats
        $query = "INSERT INTO " . TABLE_ATTAQUES_ARCHIVES . " (`archives_user_id` , `archives_nb_attaques` , `archives_date` , `archives_metal` , `archives_cristal` , `archives_deut` , `archives_pertes`, `archives_recy_metal`, `archives_recy_cristal` )
                VALUES (" . $user_data['user_id'] . ", $nb_ancattack, $timestamp, $metal, $cristal, $deut , $pertes, $metal_recy, $cristal_recy)";
        $db->sql_query($query);

        //On supprime les attaques qui viennent d'être sauvegardées
        $query = "DELETE FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id= " . $user_data['user_id'] . " AND MONTH(FROM_UNIXTIME(attack_date))=$month AND YEAR(FROM_UNIXTIME(attack_date))=$year";
        $db->sql_query($query);

        //On supprime les recyclages qui viennent d'être sauvegardés
        $query = "DELETE FROM " . TABLE_ATTAQUES_RECYCLAGES . " WHERE recy_user_id=" . $user_data['user_id'] . " AND MONTH(FROM_UNIXTIME(recy_date))=$month AND YEAR(FROM_UNIXTIME(recy_date))=$year";
        $db->sql_query($query);

        //On prépare la liste des attaques en BBCode
        $bbcode = "[color=" . $bbcolor['title'] . "][b]Liste des attaques de " . $user_data['user_name'] . "[/b] [/color]\n";
        $bbcode .= "du 01/" . $month . "/" . $year . " au 31/" . $month . "/" . $year . "\n\n";

        while (list($attack_coord, $attack_date, $attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($list)) {
            $attack_date = date('d M Y H:i:s', $attack_date);
            $bbcode .= "Le " . $attack_date . " victoire en " . $attack_coord . ".\n";
            $bbcode .= "[color=" . $bbcolor['m_g'] . "]" . $attack_metal . "[/color] de métal, [color=" . $bbcolor['c_g'] . "]" . $attack_cristal . "[/color] de cristal et [color=" . $bbcolor['d_g'] . "]" . $attack_deut . "[/color] de deutérium ont été rapportés.\n";
            $bbcode .= "Les pertes s'&eacute;lèvent à [color=" . $bbcolor['perte'] . "]" . $attack_pertes . "[/color].\n\n";
        }

        $bbcode .= "[url=https://forum.ogsteam.eu/index.php?topic=100.0]Généré par le module de gestion des attaques avec OGSpy[/url]";

        echo "<br><br>";
        echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Liste de vos attaques du 01/" . $month . "/" . $year . " au 31/" . $month . "/" . $year . "</span></legend>";
        echo "<br>";
        echo "<form method='post'><textarea rows='10' cols='15' id='$bbcode'>$bbcode</textarea></form></fieldset>";
    }

    require_once("footer.php");

    //Insertion du bas de page d'OGSpy
    require_once("views/page_tail.php");

    //On ajoute l'action dans le log
    $line = "La listes des attaques de " . $user_data['user_name'] . " a &eacute;t&eacute; supprimée. Les gains ont été archivés dans le module de gestion des attaques";
    $fichier = "log_" . date("ymd") . '.log';
    $line = "/*" . date("d m Y H:i:s") . '*/ ' . $line;
    write_file(PATH_LOG_TODAY . $fichier, "a", $line);

    exit;
}

//Fonction de suppression d'un rapport d'attaque
if (isset($pub_attack_id)) {
    $pub_attack_id = intval($pub_attack_id);

    //On récupère l'id de l'utilisateur qui a enregistré cette attaque
    $query = "SELECT attack_user_id FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_id='$pub_attack_id'";
    $result = $db->sql_query($query);
    list($user) = $db->sql_fetch_row($result);

    if ($user == $user_data['user_id']) {
        $query = "DELETE FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_id='$pub_attack_id'";
        $db->sql_query($query);
        echo "<blink><span style=\"color: FF0000; \">L'attaque a bien été supprimée.</span></blink>";

        //On ajoute l'action dans le log
        $line = $user_data['user_name'] . " supprime l'une de ses attaque dans le module de gestion des attaques";
        $fichier = "log_" . date("ymd") . '.log';
        $line = "/*" . date("d/m/Y H:i:s") . '*/ ' . $line;
        write_file(PATH_LOG_TODAY . $fichier, "a", $line);
    } else {
        echo "<blink><span style=\"color: FF0000; \">Vous n'avez pas le droit d'effacer cette attaque !!!</span></blink>";

        //On ajoute l'action dans le log
        $line = $user_data['user_name'] . " a tenté de supprimer une attaque qui appartient à un autre utilisateurs dans le module de gestion des attaques";
        $fichier = "log_" . date("ymd") . '.log';
        $line = "/*" . date("d/m/Y H:i:s") . '*/ ' . $line;
        write_file(PATH_LOG_TODAY . $fichier, "a", $line);
    }
}

// On récupère la liste des utilisateurs dont on peut afficher les attaques
$query = "SELECT DISTINCT u.`user_id`, u.`user_name` FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_MOD_USER_CFG . " mu
                ON mu.`user_id` = u.`user_id`
          WHERE u.`user_id` = " . $user_data['user_id'] . " OR (mu.`user_id` is not null AND mu.`config` = 'diffusion_rapports' AND mu.`mod` = 'Attaques')
          ORDER BY u.`user_name`";

$result = $db->sql_query($query);
$users = array();
while ($row = $db->sql_fetch_row($result)) {
    $users[$row[0]] = $row[1];
}

// Si un utilisateur a été sélectionné, on vérifie que l'on peut afficher les rapports de celui-ci
if (isset($pub_user_id) && isset($users[$pub_user_id]))
    $user_id = $pub_user_id;
else
    $user_id = $user_data['user_id'];

$estUtilisateurCourant = $user_id == $user_data['user_id'];
$masquer_coord = false;
if (!$estUtilisateurCourant) {
    $result = mod_get_user_option($user_id, 'masquer_coord');
    if ($result == null || $result['masquer_coord'] == '1')
        $masquer_coord = true;
}

//Si les dates d'affichage ne sont pas définies, on affiche par défaut les attaques du jour,
if (!isset($pub_date_from))
    $pub_date_from = mktime(0, 0, 0, $mois, $date, $annee);
else {
    // Si la date est au format jour/mois/annee
    $pub_date = date_parse_from_format('j M Y H:i', $pub_date_from);
    if ($pub_date['error_count'] == 0)
        $pub_date_from = mktime($pub_date['hour'], $pub_date['minute'], 00, $pub_date['month'], $pub_date['day'], $pub_date['year']);
    else
        $pub_date_from = mktime(0, 0, 0, $mois, $pub_date_from, $annee);
}

if (!isset($pub_date_to))
    $pub_date_to = mktime(23, 59, 59, $mois, $date, $annee);
else {
    // Si la date est au format jour/mois/annee
    $pub_date = date_parse_from_format('j M Y H:i', $pub_date_to);
    if ($pub_date['error_count'] == 0)
        $pub_date_to = mktime($pub_date['hour'], $pub_date['minute'], 59, $pub_date['month'], $pub_date['day'], $pub_date['year']);
    else
        $pub_date_to = mktime(23, 59, 59, $mois, $pub_date_to, $annee);
}

$pub_date_from = intval($pub_date_from);
$pub_date_to = intval($pub_date_to);

//Si le choix de l'ordre n'est pas définis on met celui par defaut
if (!isset($pub_order_by)) $pub_order_by = "attack_date";
else $pub_order_by = $db->sql_escape_string($pub_order_by);

if (!isset($pub_sens)) $pub_sens = "DESC";
elseif ($pub_sens == 2) $pub_sens = "DESC";
elseif ($pub_sens == 1) $pub_sens = "ASC";

//Requete pour afficher la liste des attaques
$query = "SELECT attack_coord, attack_date, attack_metal, attack_cristal, attack_deut, attack_pertes, attack_id FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_id . " AND attack_date BETWEEN " . $pub_date_from . " and " . $pub_date_to;

$order_by = " ORDER BY ";
if ($pub_order_by != 'attack_coord')
    $order_by .= $pub_order_by . " " . $pub_sens;
else {
    // On va trier par les valeurs des coordonnées
    // Galaxie
    $order_by .= "CAST(SUBSTRING_INDEX(attack_coord, ':', 1)AS UNSIGNED INTEGER) " . $pub_sens . ",";
    // Système
    $order_by .= "CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(attack_coord, ':', 2), ':', -1)AS UNSIGNED INTEGER) " . $pub_sens . ",";
    // Planète
    $order_by .= "CAST(SUBSTRING_INDEX(attack_coord, ':', -1)AS UNSIGNED INTEGER) " . $pub_sens;
}
$query .= $order_by;

$result = $db->sql_query($query);

//On recupère le nombre d'attaques
$nb_attack = $db->sql_numrows($result);

//Cacul pour obtenir les gains
$query = "SELECT SUM(attack_metal), SUM(attack_cristal), SUM(attack_deut), SUM(attack_pertes) FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_id . " AND attack_date BETWEEN " . $pub_date_from . " and " . $pub_date_to . " GROUP BY attack_user_id";

//echo $query;

$resultgains = $db->sql_query($query);

//On récupère la date au bon format
$pub_date_from = date('d M Y', $pub_date_from);
$pub_date_to = date('d M Y', $pub_date_to);


//Création du field pour choisir l'affichage (attaque du jour, de la semaine ou du mois
echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Paramètres d'affichage des attaques ";
echo help("attaques_changer_affichage");
echo "</font></b></legend>";

echo "Afficher les attaques : ";
echo "<form action='index.php?action=attaques&page=attaques' method='post' name='date'>";
echo "du : <input type='text' name='date_from' id='date_from' size='15' value='$pub_date_from' /> ";
echo "au : ";
echo "<input type='text' name='date_to' id='date_to' size='15' value='$pub_date_to' />";
echo "<br>";
?>
<a href="#haut" onclick="setDateFrom('<?php echo $date; ?>'); setDateTo('<?php echo $date; ?>'); valid();">du jour</a> |
<a href="#haut" onclick="setDateFrom('<?php echo $yesterday; ?>'); setDateTo('<?php echo $yesterday; ?>'); valid();">de
    la veille</a> |
<a href="#haut" onclick="setDateFrom('<?php echo $septjours; ?>'); setDateTo('<?php echo $date; ?>'); valid();">des 7
    derniers jours</a> |
<a href="#haut" onclick="setDateFrom('01'); setDateTo('<?php echo $date; ?>'); valid();">du mois</a>
<br />
<select name="user_id">
    <?php foreach ($users as $id => $username) {
        echo "<option value='$id'";
        if ($id == $user_id)
            echo " SELECTED=SELECTED";
        echo ">$username</option>";
    }
    ?>
</select>
<?php


echo "<br><br>";
echo "<input type='submit' value='Afficher' name='B1'></form>";
echo "</fieldset>";
echo "<br><br>";

//Création du field pour voir les gains des attaques
echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Résultats des attaques du " . $pub_date_from . " au " . $pub_date_to . " de " . $users[$user_id];
echo help("attaques_resultats");
echo "</font></b></legend>";

//Résultat requete
list($attack_metal, $attack_cristal, $attack_deut, $attack_pertes) = $db->sql_fetch_row($resultgains);

// Valeurs par défaut
$attack_metal = $attack_metal ?? 0;
$attack_cristal = $attack_cristal ?? 0;
$attack_deut = $attack_deut ?? 0;
$attack_pertes = $attack_pertes ?? 0;

//Calcul des gains totaux
$totalgains = $attack_metal + $attack_cristal + $attack_deut;

//Calcul de la rentabilité
$renta = $totalgains - $attack_pertes;

echo "<table width='100%'><tr align='left'>";


echo "<td width='25%'>" . "<table width='100%'><colgroup><col width='40%'/><col/></colgroup><tbody>" . "<tr>" . "<td style='font-size: 18px;color: white;'><b>M&eacute;tal</b></td>" . "<td class='metal number' style='font-size: 18px;'>" . number_format($attack_metal, 0, ',', ' ') . "</td>" . "</tr><tr>" . "<td style='font-size: 18px;color: white;'><b>Cristal</b></td>" . "<td class='cristal number' style='font-size: 18px;'>" . number_format($attack_cristal, 0, ',', ' ') . "</td>" . "</tr><tr>" . "<td style='font-size: 18px;color: white;'><b>Deut&eacute;rium</b></td>" . "<td class='deuterium number' style='font-size: 18px;'>" . number_format($attack_deut, 0, ',', ' ') . "</td>" . "</tr><tr>" . "<td style='font-size: 18px;color: white;'><b>Gains</b></td>" . "<td class='number' style='font-size: 18px;color: white;'>" . number_format($totalgains, 0, ',', ' ') . "</td>" . "</tr><tr>" . "<td style='font-size: 18px;color: white;'><b>Pertes</b></td>" . "<td class='perte number' style='font-size: 18px;'>" . number_format($attack_pertes, 0, ',', ' ') . "</td>" . "</tr><tr>" . "<td style='font-size: 18px;color: white;'><b>Rentabilit&eacute;</b></td>" . "<td class='renta number' style='font-size: 18px;'>" . number_format($renta, 0, ',', ' ') . "</td>" . "</tr><tbody></table></td>";

// Afficher l'image du graphique
echo "<td width='75%' align='center'>";

if ((!isset($attack_metal)) && (!isset($attack_cristal)) && (!isset($attack_deut)) && (!isset($attack_pertes))) {
    echo "Pas de graphique disponible";
} else {
    /** GRAPHIQUE **/
    echo "<div id='graphique' style='height: 350px; width: 800px; margin: 0pt auto; clear: both;'></div>";
    /** GRAPHIQUE **/

    echo create_pie_numbers($attack_metal . "_x_" . $attack_cristal . "_x_" . $attack_deut . "_x_" . $attack_pertes, "Métal_x_Cristal_x_Deutérium_x_Pertes", "Gains des Attaques", "graphique");
}
echo "</td></tr>";


echo "</table>";
echo "</p></fieldset><br><br>";

//Création du field pour voir la liste des attaques
echo "<fieldset><legend><b><span style=\"color: #0080FF; \">Liste des attaques du " . $pub_date_from . " au " . $pub_date_to . " ";
echo " : " . $nb_attack . " attaque(s) ";
echo help("attaques_liste_attaques");
echo "</span></b></legend>";

//Debut du lien pour le changement de l'ordre d'affichage
$link = "index.php?action=attaques&date_from=" . $pub_date_from . "&date_to=" . $pub_date_to . " &user_id=" . $user_id;

//Tableau donnant la liste des attaques
echo "<table width='100%'>";
echo "<tr>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_coord&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Coordonnées</b> <a href='" . $link . "&order_by=attack_coord&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_date&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Date de l'Attaque</b> <a href='" . $link . "&order_by=attack_date&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_metal&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Métal Gagné</b> <a href='" . $link . "&order_by=attack_metal&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_cristal&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Cristal Gagné</b> <a href='" . $link . "&order_by=attack_cristal&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_deut&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Deut&eacute;rium Gagné</b> <a href='" . $link . "&order_by=attack_deut&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><a href='" . $link . "&order_by=attack_pertes&sens=1'><img src='" . $prefixe . "images/asc.png'></a> <b>Pertes Attaquant</b> <a href='" . $link . "&order_by=attack_pertes&sens=2'><img src='" . $prefixe . "images/desc.png'></a></td>";
echo "<td class=" . 'c' . " align=" . 'center' . "><b><span style=\"color: #FF0000; \">Supprimer</span></b></td>";

echo "</tr>";
echo "<tr>";

while (list($attack_coord, $attack_date, $attack_metal, $attack_cristal, $attack_deut, $attack_pertes, $attack_id) = $db->sql_fetch_row($result)) {
    $attack_date = date('d M Y H:i:s', $attack_date);
    $attack_metal = number_format($attack_metal, 0, ',', ' ');
    $attack_cristal = number_format($attack_cristal, 0, ',', ' ');
    $attack_deut = number_format($attack_deut, 0, ',', ' ');
    $attack_pertes = number_format($attack_pertes, 0, ',', ' ');
    $coord = explode(":", $attack_coord);
    echo "<th align='center'>";
    if (!$masquer_coord)
        echo "<a href='index.php?action=galaxy&galaxy=" . $coord[0] . "&system=" . $coord[1] . "'>" . $attack_coord;
    echo "</th>";
    echo "<th align='center'>" . $attack_date . "</th>";
    echo "<th align='center'>" . $attack_metal . "</th>";
    echo "<th align='center'>" . $attack_cristal . "</th>";
    echo "<th align='center'>" . $attack_deut . "</th>";
    echo "<th align='center'>" . $attack_pertes . "</th>";
    echo "<th align='center' valign='middle'>";

    if ($estUtilisateurCourant) {
        echo "<form action='index.php?action=attaques&page=attaques' method='post'><input type='hidden' name='date_from' value='$pub_date_from'><input type='hidden' name='date_to' value='$pub_date_to'><input type='hidden' name='attack_id' value='$attack_id'><input type='submit'  value='Supprimer' name='B1' style='color: #FF0000'></form>";
    }
    echo "</th>";
    echo "</tr>";
    echo "<tr>";
}
echo "</tr>";
echo "</table>";
echo "</fieldset>";

if ($config['histo'] == 1) {

    /**** DEBUT HISTO ******/
    $mois = date("m");
    $annee = date("Y");

    $query = "SELECT DAY(FROM_UNIXTIME(attack_date)) AS day, SUM(attack_metal) AS metal, SUM(attack_cristal) AS cristal, SUM(attack_deut) AS deut FROM " . TABLE_ATTAQUES_ATTAQUES . " WHERE attack_user_id=" . $user_id . " and MONTH(FROM_UNIXTIME(attack_date))=" . $mois . " and YEAR(FROM_UNIXTIME(attack_date))=" . $annee . " GROUP BY day";

    // requète SQL pour récupérer le total par ressource par jour
    $result = $db->sql_query($query);

    // Initialisation des variables et tableau

    $barre = array();
    $maxy = 0;
    // Lecture de la base de données et stockage des valeurs dans le tableau
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

    $i = 0;
    $categories = "";
    $metal = "";
    $cristal = "";
    $deuterium = "";
    for ($n = 1; $n < 32; $n++) {
        if (!isset($barre[$n][0])) {
            $barre[$n][0] = 0;
        }
        if (!isset($barre[$n][1])) {
            $barre[$n][1] = 0;
        }
        if (!isset($barre[$n][2])) {
            $barre[$n][2] = 0;
        }

        if ($n == 1) {
            $categories .= "'" . $n . "'";
            $metal .= $barre[$n][0];
            $cristal .= $barre[$n][1];
            $deuterium .= $barre[$n][2];
        } else {
            $categories .= ",'" . $n . "'";
            $metal .= "," . $barre[$n][0];
            $cristal .= "," . $barre[$n][1];
            $deuterium .= "," . $barre[$n][2];
        }
    }

    $series = "{name: 'Métal', data: [" . $metal . "] }, " . "{name: 'Cristal', data: [" . $cristal . "] }, " . "{name: 'Deutérium', data: [" . $deuterium . "] }";

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
}
echo "<br>";
echo "<br>";

?>
