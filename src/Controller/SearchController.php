<?php

namespace App\Controller;

use App\Form\SearchType;

use App\Form\SearchDateType;

use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;


// necessaire pour générer du twig via render dans DefaultController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// necessaire pour utiliser les annotation @Route qui vont remplacer les routes dans routes.yaml
use Symfony\Component\Routing\Annotation\Route;

// désormais, j' hérite d' AbstractController pour pouvoir pouvoir envoyer du contenu en twig grace a render
class SearchController extends AbstractController
{
    
    //------------------------ début de search bar simple https://bibliocode.fr/biblio/10--------------------------

    /**
     * @Route("/recherche", name="search")
     */
    public function recherche(Request $request, UserRepository $repo, PaginatorInterface $paginator) {

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        
        $searchDateForm = $this->createForm(SearchDateType::class);
        $searchDateForm->handleRequest($request);
        
        $donnees = $repo->findAll();
 
        // Gestion Formulaire Recherche par mot clé
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $moteur = $searchForm->get('searchEngine')->getData();

            $donnees = $repo->search($moteur);

            if ($donnees == null) {
                $this->addFlash('erreurUsername', 'Aucun Utilisateur n\'a été trouvé, essayez en un autre.');
           
            }
        }
        // Gestion Formulaire Recherche par mot date
        else if ($searchDateForm->isSubmitted() && $searchDateForm->isValid()) {

            $moteurDate = $searchDateForm->get('searchDateEngine')->getData();

            $donnees = $repo->searchDate($moteurDate);

            if ($donnees == null) {
                $this->addFlash('erreurUserDate', 'Aucun Utilisateur n\'a été trouvé, essayez en un autre.');
           
            }
        }


        // Paginate the results of the query
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('public/search.html.twig',[
            'users' => $users,
            'searchForm' => $searchForm->createView(),
            'searchDateForm' => $searchDateForm->createView()
        ]);
    }

    // ------------------------------------fin de la search bar simple-------------------------

}