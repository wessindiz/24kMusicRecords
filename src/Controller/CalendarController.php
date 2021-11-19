<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Categorie;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Internal\PushedResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{

    /**
     * @Route("/new", name="calendar_new", methods={"GET","POST"})
     */
    public function new(Request $request, CalendarRepository $calendarRepo): Response
    {
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
               // 'title' => $event->getTitle(),
               // 'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }
        
        $data = json_encode($rdvs);

        return $this->renderForm('calendar/new.html.twig', [
            'calendar' => $calendar,
            'categories' => $categories,
            'form' => $form,
            'data'=>$data,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="calendar_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Calendar $calendar): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/delete", name="calendar_delete", methods={"PUT"})
     */
    public function delete(Request $request, ?Calendar $calendar): Response
    {
        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
