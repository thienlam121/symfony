<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employe;
use App\Entity\Inscription;
use App\Form\EmployeInscriptionType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EmployeRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;


class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function index(): Response
    {
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
        ]);
    }

    #[Route('/rechercheFindBy', name: 'app_recherche_findBy')]
    public function rechercheFindByAction(ManagerRegistry $doctrine)
    {
        // $message = "bonjour";
        // $test = "lam";
        //liste des employés
        $inscriptions = $doctrine->getManager()->getRepository(Inscription::class)->rechInscriptionsEmploye('Lam','Thomas');
       
        // var_dump($inscriptions);
        // exit;
    return $this->render('recherche/index.html.twig', ['inscriptions'=>$inscriptions/*,'message' => $message, 'nom'=>$test*/]);
    }

    #[Route('/rechercheByName', name: 'app_recherche_ByName')]
    public function rechercheParNom(Request $request,ManagerRegistry $doctrine,$emp=null)
    {
        $inscriptions = [];
       
        $form = $this->createForm(EmployeInscriptionType::class);
       
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){   
            $emp = $form->getData();
            $nom = $emp->getNom();
            $prenom = $emp->getPrenom();
            $inscriptions = $doctrine->getManager()->getRepository(Inscription::class)->rechInscriptionsEmploye($nom,$prenom);
            
        }
        
        return $this->render('recherche/rechercheInscriptionByName.html.twig', 
            array('form'=>$form->createView(),
            'inscriptions'=>$inscriptions
            ));
    }
    


}
