## Version 0.8j

* Compatibilité Xtense > 2.0b7

## Version 0.8i

* Correction du footer. Le numéro de version est pris dans la database
* Modification de la gestion des couleurs dans les graphes statistiques
* Réecriture de l'import pour gameOgame et Xtense1

## Version 0.8h

* Bug correction. Nom des tables en dur dans certainnes requètes SQL - Merci bozzo

## Version 0.8e

* petites corrections des histogrammes 3D
* Ajout d'une page 'Statistiques' avec les graphes sur 7 jours et mensuel de tout les joueurs
* Page Admin: Possibilité de supprimer les anciennes archives
* Page Admin: Détection et suppression des rapports orphelins (joueurs supprimés ou inactifs)
* Connection avec Xtense2: Rapports de combats et de recyclages remontés automatiquement
* Page Admin: Détection de Xtense2 et connection
* A l'installation la présence de la table mod_config est détecté et si non présente celle-ci est créé

## Version 0.8d

* Correction du script de génération des histogrammes 3D pour support PHP4 (free.fr)

## Version 0.8c

* Modification suite à réapparition des coordonnées dans les rapports de recyclages

## Version 0.8a

* Modification de la barre de menu
* Ajout d'une page 'Admin'
* Ajout d'un 'layer' pour améliorer la lisibilité du mod sur les fonds clairs
* Possibilité de désactiver le 'layer' dans la page 'Admin', et de modifier son pourcentage d'opacité
* Possibilité de modifier les couleurs utilisés dans les bbcodes avec sélecteur de couleur en javascript
* Ajout d'un 'historique mensuel' en barre histogramme 3D dans les pages 'Attaques','Recyclages' et 'Bilan'
* Possibilité de désactiver l'affichage de 'l'historique mensuel' dans la page 'Admin' (le graphe met plus de 3s à s'afficher)
* Les pages 'Admin' et 'Changelog' n'apparaissent que pour les administrateurs
* Préparation au support multi-langue
* Prise en compte des attaques subies (dont vous êtes le défenseur)
* Possibilité de désactiver la prise en compte des attaques subies dans la page 'Admin'
* Page Archive: affichage des mois archivés, clickable

## Version 0.7a

* Modification du RegEx pour import des rapports de combats
* Correction de la quasi totalité des erreurs de type Notice
* Insertion des données du RC reçues par la barre Xtense dans le module gameOgame si celui ci est actif
* Prise en compte de la version 0.6 dans la mise à jour

## Version 0.5h

* Correction d'erreurs dans la page de changelog et numéro de version en pied de page

## Version 0.5g

* Compatibilité avec Ogame version 0.78.
* Prise en compte des [] autour des coordonnées Attaquant/Défenseurs
* Mise à 1:1:1 des coordonées de recyclage en attendant quelles réapparaissent dans les rapports

## Version 0.5f

*Compatibilité avec Ogame au niveau des . dans les attaques

Merci à oXid_Fox et à Santory2 pour avoir effectué les modifs nécéssaires

## Version 0.5e

* Compatibilité avec Ogame version 0.76.

## Version 0.5d

*Correction des bugs de formulaire.

## Version 0.5c

* Compatibilité avec la barre Xtense pour l'envoie des RC.
* Onglets du menu en liens.

## Version 0.5b

*Correction des erreurs de la 0.5

## Version 0.5

* Correction du bug de sauvegarde des resultats
* Compatibilité avec la barre de Naqdazar.

## Version 0.4b

* Correction des bugs découvert dans la 0.4.
* Ajout d'un espace bilan.
* lorsque l'on clique sur un lien pour changer la date, les données sont rechargées automatiquement. Plus besoin de cliquer en plus sur le bouton afficher.

## Version 0.4

* Impossibilité d'enregistrer deux fois la même attaque, ou le même recyclage.
* Contrôle si la version acuelle est à jour
* Plus grande liberté au niveau du choix des dates d'affichage.
* Possibilité de récupérer les résultats et la liste des attaques en BBCode.
* Séparation des attaques et des recyclages
* Test de la présence ou non des tables dans les fichiers install et uninstall
* Ajout de l'aide via les tooltips.

## Version 0.3

* Prise en compte des recyclages.
* Ajout de graphiques sur la page attaques du mois.
* Pour la mise à jour et la suppression, le mod est appelé par son paramètre GET. Il est donc possible de changer le nom du mod sans problème
* Amélioration du code.

## Version 0.2b

* Prise en compte des prefixes des tables.
* Correction de bugs mineurs.
* Sécurisation du mod.

## Version 0.2

* Prise en compte des pertes attaquant.
* Les gains des attaques des mois précédent sont sauvegardés.
* Les chiffres sont affichés avec un séparateur de milliers.
* Demande une confirmation avant de supprimer une attaque.
* Amélioration du code.

## Version 0.1b

* Correction d'un bug au niveau des formulaires.
* Correction du code.

## Version 0.1

* Sortie du mod gestion des attaques.

Merci à calidian pour les tests qu'il a effectué
