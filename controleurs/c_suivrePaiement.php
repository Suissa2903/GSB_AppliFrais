<?php

/**
 * Controleur Valider Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Tsivya Suisssa
 * @author    Beth Sefer
 */

$mois = getMois(date('d/m/Y'));
$moisPrecedent =  getMoisPrecedent($mois) ;
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) { 
case 'selectionnerFiche':
    $lesVisiteurs= $pdo->getLesVisiteursVa();
    $lesCles1=array_keys($lesVisiteurs);
    $visiteurASelectionner= $lesCles1[0];
    $lesMois= $pdo->getLesMoisVa();
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    $nbTotalFiche =$pdo->calculNbTotalFiche($moisPrecedent) ;    
    $nbFicheVA =$pdo->calculFicheVa($moisPrecedent) ;
  
    $pourcentage = ($nbFicheVA[0][0] /$nbTotalFiche [0][0]) *100 ;
    $pdo->insertBdd($pourcentage) ;
      include 'vues/v_suivrePaiement.php';
    break ; 

 case 'afficheFrais':
       $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
       $lesVisiteurs=$pdo->getLesVisiteursVa();
       $visiteurASelectionner=$idVisiteur;  
       $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on recupere ce qui a ete selectionné ds la liste deroulante de nummois(qui se trouve dans v_listemois).
       $lesMois = $pdo->getLesMoisVa($idVisiteur);
       $moisASelectionner = $leMois;
       $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
       $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
       $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
       $numAnnee = substr($leMois, 0, 4);
       $numMois = substr($leMois, 4, 2);
       $libEtat = $lesInfosFicheFrais['libEtat'];
       $montantValide = $lesInfosFicheFrais['montantValide'];
       $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
       $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
       if(!is_array($lesInfosFicheFrais)){
            //ajouterErreur('Pas de fiche de frais validée pour ce visiteur ce mois');
            //include 'vues/v_erreurs.php';
            include 'vues/v_suivrePaiement.php';
        }
        else{
            include 'vues/v_etatFrais.php';
            include 'vues/v_miseEnPaiement.php';
        }
        break; 
        
         case 'paiement':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $lesVisiteurs=$pdo->getLesVisiteurs();
        $visiteurASelectionner=$idVisiteur;  
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on recupere ce qui a ete selectionné ds la liste deroulante de nummois(qui se trouve dans v_listemois).
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $leMois;
        $etat='RB';
        $pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);
        echo "La fiche a bien été remboursée.";
        break;
}

