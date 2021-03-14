<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Entity\Genre;
use Symfony\Component\HttpFoundation\Session\Session;

class ServeurController extends AbstractController
{
    /**
     * @Route("/serveur", name="serveur")
     */
    public function index(): Response
    {
        return $this->render('serveur/index.html.twig', [
            'controller_name' => 'ServeurController',
        ]);
    }
	
	
	 /**
     * @Route("/creationServeur", name="creationServeur")
     */
    public function creationServeur(Request $request, EntityManagerInterface $manager): Response
    {
		$Serveur = new Utilisateur();
		$Serveur->setNom($request->request->get('nom'));
		$Serveur->setPrenom($request->request->get('prenom'));
		$Serveur->setCode($request->request->get('code'));
		

		$manager->persist($Serveur);
		$manager->flush();

        return $this->render('serveur/index.html.twig', [
            'controller_name' => 'Un utilisateur a ete ajouter',
        ]);
    }
	
	/**
     * @Route("/listeServeur", name="listeServeur")
     */
	
		 public function listeServeur(Request $request, EntityManagerInterface $manager): Response
    {
		//Récupération de tous les utilisateurs
		$listeServeur = $manager->getRepository(Utilisateur::class)->findAll();
        return $this->render('serveur/listeServeur.html.twig', [
            'controller_name' => 'Liste des utilisateurs',
            'listeServeur' => $listeServeur,
        ]);
    }
	
		/**
     * @Route("/updateServeur/{id}", name="updateServeur")
     */
	 public function updateServeur(Request $request, EntityManagerInterface $manager, Utilisateur $id ): Response
			{
			$sess = $request->getSession();
			//Créer des variables de session
			$sess->set("idServeurModif", $id->getId());
			return $this->render('serveur/updateServeur.html.twig', [
			'controller_name' => "Mise à jour d'un Utilisateur",
			'serveur' => $id,
			]);
			}
	 
	/**
     * @Route("/updateServeurBdd", name="updateServeurBdd")
     */
	 
	 public function updateServeurBdd(Request $request, EntityManagerInterface $manager): Response
			{
			$sess = $request->getSession();
			//Créer des variables de session
			$id = $sess->get("idServeurModif");
			$serveur = $manager->getRepository(Utilisateur::class)->findOneById($id);
			if(!empty($request->request->get('nom')))
			$serveur->setNom($request->request->get('nom'));
			if(!empty($request->request->get('prenom')))
			$serveur->setPrenom($request->request->get('prenom'));
			if(!empty($request->request->get('code')))
			$serveur->setCode($request->request->get('code'));
			$manager->persist($serveur);
			$manager->flush();

			return $this->redirectToRoute('listeServeur');
			}
	 
	 
}
