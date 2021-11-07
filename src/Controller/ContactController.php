<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();

            $subject = "";
            if ($contactFormData['objet'] == 1) {
                $subject = "Annulation ou modification d\'un rendez-vous";
            }

            elseif ($contactFormData['objet'] == 2) {
                $subject = "Studio musique";
            }
            elseif ($contactFormData['objet'] == 3){
                $subject = "Studio photo";
            }
            elseif ($contactFormData['objet'] == 4){
                $subject = "Renseignements";
            }
            elseif ($contactFormData['objet'] == 5){
                $subject = "Autres...";
            }


            //Création du mail
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('admin@admin.fr')
                ->subject($subject)
                ->text($contactFormData['message']);

            //Envoie du mail    
            $mailer->send($message);

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            //Confirmation et redirection
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
