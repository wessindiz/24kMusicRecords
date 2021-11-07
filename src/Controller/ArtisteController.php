<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArtisteController extends AbstractController
{
    /**
     * @Route("/artiste", name="artiste")
     */
    public function index(Request $request,ArtisteRepository $artisteRepository, PaginatorInterface $paginator): Response
    {
        //Recherche des artistes par leur nom (searchbar)
        $repo = $this->getDoctrine()->getRepository(Artiste::class);
        $infoSearch = $request->query->get('searchbar');
        $artistes = $artisteRepository->findAllArtisteByName($infoSearch);

        //pagination
        $card = $paginator->paginate(
            $artistes, $request->query->getInt('page', 1), 3
        );

        return $this->render('artiste/index.html.twig', [
            'controller_name' => 'ArtisteController', 'artistes' => $artistes,'artistes' => $card, 'noneArt' => empty($artistes),
        ]);
    }


    /**
     * @Route("/artiste/new", name="artiste_new")
     */
    public function addArtist()
    {
        $em = $this->getDoctrine()->getManager();

        $artiste = new Artiste();
        $artiste->setNom("Wess");
        $artiste->setRole("artiste");
        $artiste->setAge("25 ans");
        $artiste->setDescription("Wess est un artiste venant de Saint-Denis (93). Ses influences sont le Rap, le R&nb, et les musiques caribéennes. Ses flows voguent entre le Rap et le chant.");
        $artiste->setPhoto("../photos/wess.jpeg");
        $artiste->setUrlinsta("https://www.instagram.com/wessindiz/");
        $artiste->setUrlyoutube("https://www.youtube.com/channel/UClvMufg6jreNMO6917");

        $em->persist($artiste);
        $em->flush();

        return $this->redirectToRoute("artiste");
    }


    /**
     * @Route("/artiste/show/{id}", name="show")
     */
    public function showArtiste($id)
    {
        $em = $this->getDoctrine()->getManager();
        $artiste = $em->getRepository(Artiste::class)->find($id);

        return $this->render('artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }


     /**
     * @Route("/artiste/edit/{id}", name="edit")
     */
    public function editArtist($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $artiste = $em->getRepository(Artiste::class)->find($id);
        $formedit = $this->createForm(ArtisteType::class, $artiste);
        $oldPhoto = $artiste->getPhoto();

        if (!$artiste) {
            throw $this->createNotFoundException(
                "Aucun artiste trouvé avec cet id!"
            );
        }
        
        $formedit->handleRequest($request);

        if ($formedit->isSubmitted() && $formedit->isValid()) {
            $fileDelete = new Filesystem;
            $file = $formedit->get('photo')->getData();

            if ($file != null) {
                $fileName = time() . "." . $file->guessExtension();

              $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );

                if (file_exists('photos/' . $oldPhoto)) {
                    $fileDelete->remove('photos/' . $oldPhoto);
                }


                $artiste->setPhoto($fileName);
            } else {
                $artiste->setPhoto($oldPhoto);
            }

            $em->flush();

            return $this->redirectToRoute('artiste');
        }

        return $this->render('artiste/edit.html.twig', ["formedit" => $formedit->createView(), "artiste" => $artiste]);
    }

    
    /**
     * @Route("/artiste/delete/{id}", name="delete")
     */
    public function deleteArtist($id)
    {

        $em = $this->getDoctrine()->getManager();
        $artiste = $em->getRepository(Artiste::class)->find($id);

        if (!$artiste) {
            throw $this->createNotFoundException(
                "il n'y a rien à supprimer!"
            );
        }
        $em->remove($artiste);
        $em->flush();

        return $this->redirectToRoute("artiste");
    }

    /**
     * @Route("/artiste/add", name="add_artiste")
     */
    public function newArtist(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $art = new Artiste();
        $form_add = $this->createForm(ArtisteType::class, $art);
        $form_add->handleRequest($request);

        if ($form_add->isSubmitted() && $form_add->isValid()) {

            $imageDestination = $this->getParameter('images_directory');
            $file = $form_add->get('photo')->getData();
            $fileName = "";
            if ($file) {
                $fileName = time() . '.' . $file->guessExtension();
                $file->move(
                    $imageDestination,
                    $fileName
                );
            }
            $art->setPhoto($fileName);
            $em->persist($art);
            $em->flush();

            return $this->redirectToRoute("artiste");
        }

        return $this->render("artiste/add.html.twig", [
            "form" => $form_add->createView(),
        ]);
    }
}
