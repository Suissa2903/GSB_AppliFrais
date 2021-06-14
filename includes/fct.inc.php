<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author   Tsivya Suissa
 * @author    Beth Sefer
 */

/**
 * Teste si un quelconque visiteur est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idUtilisateur']);//isset: question: Est qu il y a un IdVisiteur dans la SuperGlobable ?(vrai ou faux?)
}

/**
 * Enregistre dans une variable session les infos d'un visiteur
 * 
 * @param type $idUtilisateur
 * @param type $nom
 * @param type $prenom
 * @param type $statut
 * 
 * @return null
 */
function connecter($idUtilisateur, $nom, $prenom,$statut)
{
    $_SESSION['idUtilisateur'] = $idUtilisateur;//on insere les valeurs dans la superGlobale SESSION
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['statut'] = $statut;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate)
{
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * 
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);//explode:chaque fois qu'il y a un "/", il sépare les variables
    unset($jour);//retire la variable
    if (strlen($mois) == 1) { //verifie le nombre de caractere dans le mois
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                    "date d'enregistrement du frais dépassé, plus de 1 an"
                );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}
/**
 * retourne le statut=visiteur si il est connecté
 * @return type
 */
function estVisiteurConnecte()
{
   if (estConnecte()){
       return ($_SESSION['statut']== 'visiteur');
   }  
}
/**
 * retourne le statut=visiteur si il est connecté
 * @return type
 */
function estComptableConnecte()
{
   if (estConnecte()){
       return ($_SESSION['statut']== 'comptable');
   }  
   
}
/**
 * recupere le mois suivant
 * @param type $mois
 * @return type
 */
function getMoisSuivant($mois){
   $numAnnee = substr($mois, 0, 4);// permet de recuperer les 4 premiers caracteres
   $numMois = substr($mois, 4, 2);// permet de recuperer a partir du 4eme caractere, les 2 premiers
   if($numMois=='12'){
        $numMois='01';
        $numAnnee++;
       
   }else{
        $numMois ++;
   }
   if (strlen($numMois) == 1) { //verifie le nombre de caractere dans le mois
        $numMois = '0' . $numMois;
   }
   return $numAnnee.$numMois;
   
}
/**
 * recupere le mois precedent
 * @param type $mois
 * @return type
 */
function getMoisPrecedent($mois){
   $numAnnee = substr($mois, 0, 4);// permet de recuperer les 4 premiers caracteres
   $numMois = substr($mois, 4, 2);// permet de recuperer a partir du 4eme caractere, les 2 premiers
   if($numMois=='01'){
        $numMois='12';
        $numAnnee--;
       
   }else{
        $numMois --;
   }
   if (strlen($numMois) == 1) { //verifie le nombre de caractere dans le mois
        $numMois = '0' . $numMois;
   }
   return $numAnnee.$numMois;
   
}
function getLesMois($mois){  
    $lesMois=array();
    for ($k=0; $k<12; $k++){
        $mois=getMoisPrecedent($mois); 
        $numAnnee = substr($mois, 0, 4);// permet de recuperer les 4 premiers caracteres
        $numMois = substr($mois, 4, 2);
        
        if (strlen($numMois) == 1) { //verifie le nombre de caractere dans le mois
            $numMois = '0' . $numMois;
        }
        $lesMois[] = array(
                'mois' => $numAnnee.$numMois,
                'numAnnee' => $numAnnee,
                'numMois'=> $numMois
            );  
    }
    return($lesMois);
    
}
function zero(){
    
}
