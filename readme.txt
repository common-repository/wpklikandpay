=== wpklikandpay ===
Contributors: Eoxia
Tags: payment, klikandpay, klik and pay, klik, pay, klik&pay
Requires at least: 3.0.4
Tested up to: 3.5
Stable tag: 1.2
Donate link: http://www.eoxia.com/

Permet d'ajouter le mode de paiement Klik&Pay sur votre site wordpress.

== Description ==

Vous pourrez g&eacute;rer gr&acirc;ce &agrave; ce plugin une liste d'offres que vous associerez &agrave; des formulaires pour permettre &agrave; vos clients de r&eacute;gler vos prestations et/ou services directement depuis votre site wordpress. Vous aurez acc&eacute;s &agrave; la liste des paiements effectu&eacute;s depuis la partie admin de votre site.

== Installation ==

L'installation de ce plugin est identique &agrave; tous les plugins de wordpress. Vous aurez n&eacute;anmoins besoin de cr&eacute;er un compte sur le site de Klik&Pay pour pouvoir recevoir des paiements.

== Frequently Asked Questions ==

= Faut-il absolument &ecirc;tre inscrit chez Klik&Pay pour utiliser le plugin =

Vous pourrez tester le plugin sans cr&eacute;er de compte chez Klik&Pay. N&eacute;anmoins vous devrez absolument vous inscrire pour recevoir des paiements

= Les clients qui effectue un paiment &agrave; partir du plugin auront-ils un compte sur mon site =

La cr&eacute;ation du compte utilisateur pour chaque client effectuant un paiement en utilisant le plugin n'est pas effective. Elle est pr&eacute;vue dans la liste des am&eacute;liorations pour le moment. Vous aurez n&eacute;anmoins acc&eacute;s aus informations que le client remplira avant d'&ecirc;tre redirig&eacute; vers le site de paiement

== Screenshots ==


== Changelog ==

= Version 1.2 =

Corrections

* Param&egrave;tre manquant pour la fonction wpdb->prepare() depuis la version 3.5 de wordpress ce param&egrave; est obligatoire

= Version 1.1 =

* Ajout d'une information concernant la dur&eacute;e d'une abonnement. La valeur est informative uniquement et permet d'avoir un &eacute;tat sur une commande pour la dur&eacute;e


= Version 1.0 =

* Interface de gestion d'offres (une offre d&eacute;signe un paiment avec un intitul&eacute;)
* G&eacute;rer plusieurs offres par formulaire
* Choisir l'intitul&eacute; d'une offre pour chaque formulaire
* Choisir les champs obligatoires pour le client final dans chaque formulaire (liste des champs utilisateurs pris dans la table des commandes)
* Choisir l'intitul&eacute; du bouton de validation du formulaire de paiement pour chaque formulaire
* Ajouter des formulaires avec une offre par formulaire
* Gestion des paiements uniques ou des paiements avec abonnements
* Passer une commande depuis un formulaire du c&ocirc;t&eacute; "frontend"
* D&eacute;finir un pr&eacute;fixe pour la r&eacute;f&eacute;rence des commandes de chaque offre
* Visualiser le d&eacute;tail d'un paiment
* Gestion des droits utilisateurs (administrateur &agrave; tous les droits, &eacute;diteur &agrave; les droits de consulations uniquement)
* Gestion des url de retour apr&eacute;s un paiement
* Gestion du mode "test" ou du mode "production"

== Upgrade Notice ==

* La mise &agrave; jour se fait simplement comme les autres extensions de wordpress. Lors de la mise &agrave; jour, si des modifications ont &eacute;t&eacute; effectu&eacute;es dans la base de donn&eacute;es ou dans la structure, le plugin fera le transfert vers la nouvelle structure

== Am&eacute;liorations Futures ==

= Non planifi&eacute;es =

* Ajouter les diff&eacute;rents tris pour les interfaces
* G&eacute;rer (ajout/suppression) la liste des champs que l'utilisateur devra remplir pour acc&eacute;der &agrave; la page de paiement
* Cr&eacute;er le compte de l'utilisateur pour lui donner acc&eacute;s &agrave; ses commandes (possibilit&eacute; de les importer avec l'adresse email obligatoire)
* Cr&eacute;er l'interface de visualisation des commandes pour les utilisateurs
* Ranger les offres dans l'ordre que l'on veut dans chaque formulaire

== Contactez l'auteur ==

dev@eoxia.com