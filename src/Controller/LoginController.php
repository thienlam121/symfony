<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\LoginType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/', name:'app_login')]
    public function login(Request $request, ManagerRegistry $doctrine, SessionInterface $session){
        $session->remove('statutEmploye');
        $session->remove('idEmploye');

        $employeConnexion = [];
        $message = "";
        $employe = new Employe();
        $form = $this->createForm(LoginType::class, $employe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){   
            $emp = $form->getData();
            $login = $emp->getLogin();
            $mdp = $emp->getMdp();
            $mdph = MD5($mdp .'15');
            $employeConnexion = $doctrine->getManager()->getRepository(Employe::class)->findOneBy(['login'=>$login,'mdp'=>$mdph]);      

            if($employeConnexion){
                
                $session->set('idEmploye', $employeConnexion->getId());
                $session->set('statutEmploye', $employeConnexion->getStatut());

                if($employeConnexion->getStatut() == 0){
                    return $this->render('emp_serv_formation/index.html.twig');
                }
                else{
                    return $this->render('emp_lambda/index.html.twig');
                }
            }else{
                $message = "Erreur de login ou de mot de passe";
            }
        }
        return $this->render('login/index.html.twig', array('form'=>$form->createView(),'message'=>$message));
    }

     
}


/*

    --- Cours variable de session ---

    $session permet d'instancier une variable de session
    Elle permet de stocker en mémoire une valeur

    $session comprends deux paramètre :
    - un nom au choix = 'statutEmploye' soit 'idEmploye' etc...
    - une valeur qui va correspond à la valeur que je souhaite stockée en mémoire = employeConnexion->getStatut();

    $session va permettre de pouvoir récupérer dans n'importe quel controller la valeur qui a été initialiser au login
    Donc dans notre cas , la valeur correspond à : $employeConnexion->getStatut();

    La variable de session à des méthodes (fonctions) qui sont :
    ->set(nom, valeur)
    ->get(nom)
    ->remove(nom)
    
*/