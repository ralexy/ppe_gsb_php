<?php
/**
 * Suivi des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Alexy ROUSSEAU <contact@alexy-rousseau.com>
 * @copyright 2017-2019 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <13>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

/**
 * Autoload de Composer
 */
require __DIR__ .'/../../vendor/autoload.php';

/**
 * On récupère les fiches validées
 * Si on en a selectionné une on la place en session
 * Comme ça on peut utiliser simplement cette information
 */

// On créé nos variable de session si elles n'existent pas
if(empty($_SESSION['ficheChoisie'])) {
    $_SESSION['ficheChoisie'] = '';
}

$lesFiches                = $pdo->getListeFicheFraisValidees();
$ficheChoisie             = isset($_POST['lstFiches']) ? filter_input(INPUT_POST, 'lstFiches', FILTER_SANITIZE_STRING) : $_SESSION['ficheChoisie'];
$_SESSION['ficheChoisie'] = isset($ficheChoisie) ? $ficheChoisie : null;

if(strlen($ficheChoisie) > 0) {
    $ficheChoisie = explode('-', $ficheChoisie);  // On explode notre idVisiteur et Mois grâce au tiret mis dans le select (plus puissant et ergonomique qu'un double select)
    $idVisiteur = $ficheChoisie[0];
    $idMois =  $ficheChoisie[1];

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $idMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $idMois);
    $infosFiche = $pdo->getLesInfosFicheFrais($idVisiteur, $idMois);
}
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch ($action) {
case 'miseEnPaiementFiche':
        if(!$ficheChoisie) {
            continue;
        }
        /**
         * Inutile de filtrer les $_POST puisqu'on s'en sert uniquement pour le if / elseif
         * Pas de switch envisageable pour $_POST['paiement'] et $_POST['remboursement']
         * car nos 2 vars $_POST ne portent pas le même nom
         *
         * Message à afficher en fonction du contexte
         */
        if(isset($_POST['paiement'])) {
            switch($infosFiche['idEtat']) {
                case 'RB':
                    $_SESSION['flash'] = 'La fiche de frais est déjà remboursée, elle ne peut donc pas être mise en paiement !';
                break;

                case 'PA':
                    $_SESSION['flash'] = 'La fiche de frais est déjà payée, elle ne peut donc pas être mise en paiement !';
                break;

                // On ne met en paiement que des fiches validées
                case 'VA':
                    $pdo->majEtatFicheFrais($idVisiteur, $idMois, 'PA'); // PA pour mise en paiement
                    $_SESSION['flash'] = 'La fiche de frais a bien été mise en paiement';
                    break;

                default:
                    $_SESSION['flash'] = 'Erreur : La mise en paiement de la fiche est impossible. Vérifier que le workflow de celle-ci a été respecté.';
                break;
            }
        } elseif(isset($_POST['remboursement'])) {
            switch($infosFiche['idEtat']) {
                case 'VA':
                    $_SESSION['flash'] = 'La fiche de frais doit être mise en paiement avant d\'être remboursée !';
                    break;

                case 'RB':
                    $_SESSION['flash'] = 'La fiche de frais est déjà remboursée !';
                    break;

                // On ne rembourse que des fiches mises en paiement
                case 'PA':
                    $pdo->majEtatFicheFrais($idVisiteur, $idMois, 'RB');// RB pour remboursé
                    $_SESSION['flash'] = 'La fiche de frais a bien été classée comme remboursée.';
                    break;

                default:
                    $_SESSION['flash'] = 'Erreur : La remboursement de la fiche est impossible. Vérifier que le workflow de celle-ci a été respecté.';
                    break;
            }
        }
        header('Location: index.php?uc=suivreFrais');
    break;

case'selectionnerMois':
    /**
     * On supprime la fiche stockée en session pour laisser le choix au visiteur
     * si il clique sur le menu et on le redirige pour bien afficher la page
     */
    if($_SESSION['ficheChoisie']) {
        unset($_SESSION['ficheChoisie']);
        header('Location: index.php?uc=suivreFrais&action=selectionnerMois');
    }
break;

case 'export':
    $storagePath = __DIR__. '/../pdf/';
    $filePath    = $storagePath . $idVisiteur. '_'. $idMois. '.pdf';

    /**
     * On génère le fichier que si il n'existe pas (orientation Green IT)
     */
    if(!file_exists($filePath)) {
        /**
         * On nettoie tout l'output avant de générer un PDF
         * On va créer un nouvel ob_start
         * Include le contenu du PDF et le stocker dans une variable
         * Et on va finir par nettoyer à nouveau l'output
         */
        ob_end_clean();

        ob_start();
        include( __DIR__. '/../remboursement.php');
        $pdfContent = ob_get_clean();

        /**
         * Génération et stockage du PDF via la librairie mPDF
         */
        $mpdf = new Mpdf\Mpdf();
        $mpdf->writeHTML($pdfContent);
        $file = $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
    }

    /**
     * On finit enfin par retourner le PDF à télécharger
     */
    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename($filePath));
    readfile($filePath);
    exit;
break;
}
if($lesFiches) {
    require 'vues/comptable/v_suivreFrais.php';
} else {
    require 'vues/comptable/v_suiviFraisVide.php';
}