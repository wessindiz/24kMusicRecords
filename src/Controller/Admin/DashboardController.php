<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Artiste;
use App\Entity\Calendar;
use App\Entity\Categorie;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Artiste::class);
        $artiste = $repo->findAll();
        $nbArt = count($artiste);
   
        $repo1 = $this->getDoctrine()->getRepository(User::class);
        $repoUser= $repo1->findAll();
        $nbUser= count($repoUser);
        
        return $this->render('bundles/EasyAdminBundle/welcome.html.twig',[
            'artistes' => $nbArt,
            'Users' => $nbUser,
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('24kMusicRecords');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fas fa-chart-line');
        yield MenuItem::linkToCrud('Artistes', 'fas fa-music', Artiste::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToRoute('Calendrier', 'far fa-calendar-alt', 'rdv', ['routeParamName' => 'routeParamValue']);
        yield MenuItem::linkToRoute('Statistiques', 'far fa-calendar-alt', 'stats', ['routeParamName' => 'routeParamValue']);
        yield MenuItem::linkToRoute('Retour sur le site', 'fas fa-home', 'home', ['routeParamName' => 'routeParamValue']);
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-door-open');
    }


    /**
    * @Route("/stats", name="stats")
    */
    public function statistique(){
        return $this->render('bundles/EasyAdminBundle/stats.html.twig');
    }



    /**
    * @Route("/rdv", name="rdv")
    */
    public function rdv(Request $request, CalendarRepository $calendarRepo){   

        $categorierepo = $this->getDoctrine()->getRepository(Categorie::class);

        $calendar = new Calendar();
        $categories = $categorierepo->findAll();

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieChoisie = $form->get('categorie')->getData();
            if ($categorieChoisie->getNom() == "Musique") {
                $calendar->setBackgroundColor('#158968'); //VERT 
                $calendar->setBorderColor('#000000'); //NOIR 
                $calendar->setTextColor('#ffffff'); //NOIR
            } else {
                # la catégorie choisie est donc Photo
                $calendar->setBackgroundColor('#cb2a2a'); //ROUGE
                $calendar->setBorderColor('#000000'); //NOIR
                $calendar->setTextColor('#ffffff'); //NOIR
            };


            $hStarts  = $calendarRepo->findByStart();

            $hEnds  = $calendarRepo->findByEnd();

            $newStart = $calendar->getStart();

            $newEnd = $calendar->getEnd();

            $newCategorie = $calendar->getCategorie();

            $toutRDV = $calendarRepo->findAll();

            $horaires = [];


            for ($i = 0; $i < count($hStarts); $i++) {
                $horaires[$i]['start' . $i] = $hStarts[$i];
                $horaires[$i]['end' . $i] = $hEnds[$i];
            }


            $PeutPrendreRdv = true;


            for ($j = 0; $j < count($horaires); $j++) {
                if ($toutRDV[$j]->getCategorie()->getId() == $newCategorie->getId()) {
                    if ($horaires[$j]['start' . $j]["start"] <= $newStart &&  $horaires[$j]['end' . $j]["end"] > $newStart) {
                        $PeutPrendreRdv = false;
                        $this->addFlash('danger', 'Ce créneau horaire est déjà pris, veuillez regarder les disponibilités sur le calendrier !');
                        break;
                    } elseif ($newEnd > $horaires[$j]['start' . $j]["start"] && $newEnd <= $horaires[$j]['end' . $j]["end"]) {
                        $PeutPrendreRdv = false;
                        $this->addFlash('danger', 'Ce créneau horaire est déjà pris, veuillez regarder les disponibilités sur le calendrier !');
                        break;
                    } elseif ($newStart <= $horaires[$j]['start' . $j]["start"] && $newEnd >= $horaires[$j]['end' . $j]["end"]) {
                        $PeutPrendreRdv = false;
                        $this->addFlash('danger', 'Ce créneau horaire est déjà pris, veuillez regarder les disponibilités sur le calendrier !');
                        break;
                    } elseif ($newStart >= $horaires[$j]['start' . $j]["start"] && $newEnd <= $horaires[$j]['end' . $j]["end"]) {
                        $PeutPrendreRdv = false;
                        $this->addFlash('danger', 'Ce créneau horaire est déjà pris, veuillez regarder les disponibilités sur le calendrier !');
                        break;
                    };
                };
            };


            if ($PeutPrendreRdv) {
                $entityManager = $this->getDoctrine()->getManager(); 
                $entityManager->persist($calendar);
                $entityManager->flush();
                $this->addFlash('success', 'Votre rendez-vous a bien été enregistré, pour tout changements, veuillez vous diriger vers la page "contact" !');
            }
            
        }    

        $events = $calendarRepo->findAll();

        $rdvs = [];
        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }
        
        $data = json_encode($rdvs);

        return $this->renderForm('bundles/EasyAdminBundle/rdv.html.twig', [
            'calendar' => $calendar,
            'categories' => $categories,
            'form' => $form,
            'data'=>$data,
        ]);
        
    }

    /**
     * @Route("/allrdv", name="allrdv", methods={"GET"})
     */
     public function AllRdv(CalendarRepository $calendarRepository): Response
     {
         return $this->render('bundles/EasyAdminBundle/allrdv.html.twig', [
             'calendars' => $calendarRepository->findAll(),
         ]);
     }
    
    
     /**
     * @Route("/{id}/editrdv", name="editrdv", methods={"GET","POST"})
     */
    public function editRdv(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('allrdv', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('bundles/EasyAdminBundle/editrdv.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'calendars' => $calendarRepository->findAll(),
        ]);
    }
 
}


