<?php
/**
 * footer.php
 *
 * @package Attaques
 * @author Verité / ericc
 * @link http://www.ogsteam.eu
 * @version 0.8e
 * Affichage de la version du module dans le pied de page
 */

$request = 'SELECT version from ' . TABLE_MOD . ' WHERE action=\'attaques\'';
$result = $db->sql_query($request);
list($version) = $db->sql_fetch_row($result);
echo "<hr width='325px'>";
echo "<div class='attack_box'><div class='attack_box_background'> </div> <div class='attack_box_contents'>";
echo "<p align='center'>Mod de Gestion des Attaques | Version " . $version . " | &copy; 2006-2021</p>";
echo "</div></div>";
