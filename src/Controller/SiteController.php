<?php

namespace App\Controller;

// necessaire pour générer du twig via render dans DefaultController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// necessaire pour utiliser les annotation @Route qui vont remplacer les routes dans routes.yaml
use Symfony\Component\Routing\Annotation\Route;

// désormais, j' hérite d' AbstractController pour pouvoir pouvoir envoyer du contenu en twig grace a render
class SiteController extends AbstractController
{
    /**
     * @Route("/confidentialite", name="confidentialite")
     */
    public function confidentialite(){
        return $this->render('public/confidentialite.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function mentionsLegales(){
        return $this->render('public/mentionsLegales.html.twig');
    }

    /**
     * @Route("/conditions-utilisation", name="conditions_utilisation")
     */
    public function conditionsUtilisation(){
        return $this->render('public/conditionsUtilisation.html.twig');
    }

}