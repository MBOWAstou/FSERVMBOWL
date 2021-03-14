<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Document;
use App\Entity\Genre;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use DateTime;

class GedController extends AbstractController
{
    /**
     * @Route("/uploadGed", name="uploadGed")
     */
    public function uploadGed(Request $request, EntityManagerInterface $manager): Response
	{
		//Requête pour récupérer toute la table genre
		$listeGenre = $manager->getRepository(Genre::class)->findAll();
		return $this->render('ged/uploadGed.html.twig', [
		'controller_name' => "Upload d'un Document",
		'listeGenre' => $listeGenre,
		]);
	}
	
	/**
     * @Route("/insertGed", name="insertGed")
     */
	 public function insertGed(Request $request, EntityManagerInterface $manager): Response
	{

		//création d'un nouveau document
		$Document = new Document();
		//Récupération et transfert du fichier
		$brochureFile = $request->files->get("fichier");
		if ($brochureFile){
		$newFilename = uniqid('', true) . "." . $brochureFile->getClientOriginalExtension();
		//$pathImage = "public/upload/";
		$brochureFile->move($this->getParameter('upload'), $newFilename);
		
		//insertion du document dans la base.
		
		$Document->setActif($request->request->get('choix'));
		$Document->setTypeId($manager->getRepository(Genre::class)->findOneById($request->request->get('genre')));
		$Document->setCreatedAt(new \Datetime);
		$Document->setChemin($newFilename);
		$manager->persist($Document);
		$manager->flush();
	}
			//Requête pour récupérer toute la table genre
	$listeGenre = $manager->getRepository(Genre::class)->findAll();
	return $this->render('ged/uploadGed.html.twig', [
	'controller_name' => "Upload d'un Document",
	'listeGenre' => $listeGenre,
	]);
	}
	/**
     * @Route("/listeDocument", name="listeDocument")
     */
	
				public function listeDocument(Request $request, EntityManagerInterface $manager): Response
				{
				//Requête pour récupérer toute la table genre
				$listeDocument = $manager->getRepository(Document::class)->findAll();
				return $this->render('ged/listeDocument.html.twig', [
				'controller_name' => 'Liste des Documents',
				'listeDocument' => $listeDocument,
				]);
				}
				
			/**
     * @Route("/deleteDocument{id}", name="deleteDocument")
     */
	
	public function deleteDocument(Request $request, EntityManagerInterface $manager, Document $id): Response
		{
		$sess = $request->getSession();
		if($sess->get("idUtilisateur")){

		//suppression physique du document :
		if(unlink("upload/".$id->getChemin())){
		//suppression du lien dans la base de données
		$manager->remove($id);
		$manager->flush();
		}
		return $this->redirectToRoute('listeDocument');
		}else{
		return $this->redirectToRoute('authentification');
		}
		}
		
	
}