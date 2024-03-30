<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Employe;
use App\Entity\Produit;

class EmpLambdaController extends AbstractController
{
    #[Route('/emp/lambda', name: 'app_emp_lambda')]
    public function index(): Response
    {
        return $this->render('emp_lambda/index.html.twig', [
            'controller_name' => 'EmpLambdaController',
        ]);
    }

    #[Route('/allFormationEmploye', name: 'app_all_formation_employe')]
    public function AllFormationsEmploye(ManagerRegistry $doctrine,SessionInterface $session,$emp=null)
    {
        if($session->get('statutEmploye') == "1")
        {
            $idEmploye = $session->get('idEmploye');
            $formations = [];
            $employe = $doctrine->getManager()->getRepository(Employe::class)->find($idEmploye);
            $formations = $doctrine->getManager()->getRepository(Formation::class)->findFormationsNonInscritesPourEmploye($employe);
            return $this->render('formation/listeFormationEmploye.html.twig', 
            array(
            'formations'=>$formations
            ));
        }
        else 
        {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/allInscriptionEmploye', name: 'app_all_inscription_employe')]
    public function AllInscriptionEmploye(ManagerRegistry $doctrine,SessionInterface $session,$emp=null)
    {
        if($session->get('statutEmploye') == "1")
        {
            $idEmploye = $session->get('idEmploye');

            if ($idEmploye) 
            {
                $inscription = [];
                $employe = $doctrine->getManager()->getRepository(Employe::class)->find($idEmploye);
                $inscription = $doctrine->getManager()->getRepository(Inscription::class)->findBy(['employe' => $employe]);
        
                return $this->render('formation/listeInscriptionEmploye.html.twig', [
                    'inscriptions' => $inscription
                ]);
            }
        }
        else 
        {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/inscriptionFormation/{idFormation}', name: 'app_inscription_formation_employe')]
    public function InscriptionFormation($idFormation, ManagerRegistry $doctrine, SessionInterface $session)
    {
        if ($session->get('statutEmploye') == "1") {
            $idEmploye = $session->get('idEmploye');
            
            // Récupérer la formation depuis la base de données
            $formation = $doctrine->getManager()->getRepository(Formation::class)->find($idFormation);
            // Récupérer l'employé depuis la base de données
            $employe = $doctrine->getManager()->getRepository(Employe::class)->find($idEmploye);
    
            // Créer une nouvelle instance d'Inscription
            $inscription = new Inscription();
            $inscription->setFormation($formation);
            $inscription->setEmploye($employe);
            $inscription->setStatut('En cours');
    
            // Récupérer l'EntityManager
            $entityManager = $doctrine->getManager();
    
            // Persist et flush l'inscription dans la base de données
            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('error', 'Vous vous êtes inscrit avec succès.');
            return $this->redirectToRoute('app_all_formation_employe');
        } 
        else 
        {
            return $this->redirectToRoute('app_login');
        }
    }
    
    
}
