<?php

namespace App\Controller;

use App\Entity\Calendar;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
     */
    public function majEvent(?Calendar $calendar, HttpFoundationRequest $request)
    {
        //on reccupere les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->categorie)&& !empty($donnees->categorie) &&
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            //Les données sont complètes
            //On initialise un code
            $code = 200; 

            //On vérifie si l'id existe
            if(!$calendar){
                //on instancie un rendez-vous
                $calendar = new Calendar;

                //On change le code
                $code = 201;
            }
                //On hydrate l'objet avec nos données
                $calendar->setTitle($donnees->title);
                $calendar->setDescription($donnees->description);
                $calendar->setStart(new DateTime($donnees->start));
                if ($donnees->allDay) {
                    $calendar->setEnd(new DateTime ($donnees->start)); 
                }else {
                    $calendar->setEnd(new DateTime ($donnees->end)); 

                }
                $calendar->setBackgroundColor($donnees->backgroundColor);
                $calendar->setBorderColor($donnees->borderColor);
                $calendar->setTextColor($donnees->textColor);

                $em= $this->getDoctrine()->getManager();
                $em->persist($calendar);
                $em->flush();

                //On retourne un code
                return new HttpFoundationResponse('Ok', $code);
            
        }else{
            //Les données sont imcomplètes
            return new HttpFoundationResponse('Donées incomplètes', 404);
        }


        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}