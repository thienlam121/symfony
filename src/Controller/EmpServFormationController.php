<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Controller\handleRequest;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\CreationFormationType;
use App\Form\CreationProduitType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EmpServFormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    #[Route('/allFormation', name: 'app_all_formation')]
    public function AllFormations(Request $request,ManagerRegistry $doctrine,SessionInterface $session,$emp=null)
    {

        if($session->get('statutEmploye') == "0")
        {
            $formations = [];
            $formations = $doctrine->getManager()->getRepository(Formation::class)->findAll();
            return $this->render('formation/listeFormation.html.twig', 
            array(
            'formations'=>$formations
            ));
        } else {
            return $this->redirectToRoute('app_login');
        }

    }

    #[Route('/supprFormation/{idFormation}', name: 'app_suppr_formation')]
    public function SupprFormation($idFormation, ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
            $idFormation= $doctrine->getManager()->getRepository(Formation::class)->find($idFormation);
            $nbInscription = $doctrine->getManager()->getRepository(Inscription::class)->count(['formation' => $idFormation]);
            if ($nbInscription > 0) 
            {
                 // Si des formations sont associées au produit, afficher un message d'erreur
                 $this->addFlash('error', 'Impossible de supprimer cette formation car des employés se sont déjà inscrits.');
                 return $this->redirectToRoute('app_all_formation');
            }
            else
            {
                $idFormation= $doctrine->getManager()->getRepository(Formation::class)->find($idFormation);
                $entityManager = $doctrine->getManager();
                $entityManager->remove($idFormation);
                $entityManager->flush();
                $this->addFlash('error3', 'Vous avez suprimmé une formation.');
                return $this->redirectToRoute('app_all_formation');
            }
        }
        else
        {
            return $this->redirectToRoute('app_login');
        } 
    }

    #[Route('/ajoutFormation', name: 'app_ajout_formation')]
    public function Ajoutformation(Request $request, ManagerRegistry $doctrine, SessionInterface $session, $formation = null){
        if($session->get('statutEmploye') == "0")
        {
            if($formation === null){
                $formation = new Formation();
            }
            $form = $this -> createForm(CreationFormationType::class, $formation);
            $form -> handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $doctrine->getManager();
                $em->persist($formation);
                $em->flush();
                $this->addFlash('error2', 'Vous avez créé une nouvelle formation.');
                return $this->redirectToRoute('app_all_formation');
            }  
            return $this->render('formation/creationFormation.html.twig', array('form'=>$form->createView()));
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }


    //affichage des inscriptions//
    #[Route('/inscriptionAdmin', name: 'app_aff_inscription')]
    public function affichageInscription(Request $request,ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
        $inscription = [];
        $inscription = $doctrine->getManager()->getRepository(Inscription::class)->findBy(["statut"=>"En cours"]);
        return $this->render('inscription/listeInscription.html.twig', 
        array(
        'inscription'=>$inscription
        ));
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }       
    }
    
    //validation des inscriptions//
    #[Route('/validationInscriptionAdmin/{idInscription}', name: 'app_validation_inscription')]
    public function validationInscription($idInscription,Request $request,ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
            $inscription = [];
            $inscription = $doctrine->getManager()->getRepository(Inscription::class)->updateInscriptionStatutValide($idInscription);
            $this->addFlash('error2', 'Vous avez accepté une inscription.');
            return $this->render('inscription/listeInscription.html.twig', 
            array(
            'inscription'=>$inscription
            ));
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }  
       
    }

    //refus des inscriptions//
    #[Route('/refusInscriptionAdmin/{idInscription}', name: 'app_refus_inscription')]
    public function refusInscription($idInscription,Request $request,ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
            $entityManager = $doctrine->getManager();
            $inscription = $entityManager->getRepository(Inscription::class)->find($idInscription);
            $inscription->setStatut('Refusée');
            $entityManager->flush();

            $this->addFlash('error', 'Vous avez refusé une inscription.');
            return $this->redirectToRoute('app_aff_inscription'); 
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

    //affichage des produits//
    #[Route('/produitAdmin', name: 'app_aff_produit')]
    public function affichageProduit(Request $request,ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
        $produit = [];
        $produit = $doctrine->getManager()->getRepository(Produit::class)->findAll();
        return $this->render('produit/listeProduit.html.twig', 
        array(
        'produit'=>$produit
        ));
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

    //suppression de produit//
    #[Route('/supprProduit/{idProduit}', name: 'app_suppr_produit')]
    public function SupprProduit($idProduit, ManagerRegistry $doctrine,SessionInterface $session){
        if($session->get('statutEmploye') == "0")
        {
            $idProduit= $doctrine->getManager()->getRepository(Produit::class)->find($idProduit);
            $nbFormations = $doctrine->getManager()->getRepository(Formation::class)->count(['produit' => $idProduit]);

            if ($nbFormations > 0) {
                // Si des formations sont associées au produit, afficher un message d'erreur
                $this->addFlash('error', 'Impossible de supprimer ce produit car des formations y sont associées.');
                return $this->redirectToRoute('app_aff_produit');
            }else{
                $entityManager = $doctrine->getManager();
                $entityManager->remove($idProduit);
                $entityManager->flush();
                return $this->redirectToRoute('app_aff_produit');
            }
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

    //ajout de produit//
    #[Route('/ajoutProduit', name: 'app_ajout_produit')]
    public function AjoutProduit(Request $request, ManagerRegistry $doctrine, SessionInterface $session,$produit = null,){
        if($session->get('statutEmploye') == "0")
        {
            if($produit === null){
                $produit = new Produit();
            }
            $form = $this -> createForm(CreationProduitType::class,$produit);
            $form -> handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $doctrine->getManager();
                $em->persist($produit);
                $em->flush();
                return $this->redirectToRoute('app_aff_produit');
            }  
            return $this->render('produit/creationProduit.html.twig', array('form'=>$form->createView()));
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }
}