<?php

namespace App\Controller;

use App\Entity\Planning;

use App\Form\PlanningType;

use Exception;

use App\Entity\User;

use App\Entity\Lieu;

use App\Entity\Langue;

use App\Entity\Specialite;

use App\Form\UpdateFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;


// necessaire pour générer du twig via render dans DefaultController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// necessaire pour utiliser les annotation @Route qui vont remplacer les routes dans routes.yaml
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



// désormais, j' hérite d' AbstractController pour pouvoir pouvoir envoyer du contenu en twig grace a render
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(){

        $userPlayer = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('category'=>'player'), array('id' => 'desc'));
        $userCoach = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('category'=>'coach'), array('id' => 'desc'));

        return $this->render('public/index.html.twig',[
            'userPlayer' => $userPlayer,
            'userCoach' => $userCoach
        ]);
    }

    // pour afficher les cards de tous les coachs

    /**
     * @Route("/coachs", name="coachs")
     */
    public function coachs(){
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('category' => 'coach'));

        return $this->render('public/coachs.html.twig', [
            'users' => $users
        ]);
    }

    // pour afficher la card de chaque coach

    /**
     * @Route("/coach_profil_public/{id}", name="coach_profil_public")
     */
    public function showCoach($id){
    // public function showCoach($id, $userId){

        // getRepository est une méthode de Doctrine qui va ensuite faire appel a notre class Player (Player::class)
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneBy(["userId" => $userId]);

        // $specialite = $user->getSpecialites();

        // si l' ID n' existe, on crée une exception qui stoppe le script
        if(!$user){
            throw new Exception('Ce coach n\' existe pas');
        }
        
        return $this->render('public/coach_profil_public.html.twig', [
            'user' => $user,
            // 'specialite' => $specialite
        ]);
    }

    // pour afficher les cards de tous les joueurs

    /**
     * @Route("/joueurs", name="joueurs")
     */
    public function joueurs(){

        // pour récuperer toutes les entrées en bdd
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array('category' => 'player'));

        return $this->render('public/joueurs.html.twig', [
            'users' => $users
        ]);
    }

    // pour afficher la card de chaque joueur

    /**
     * @Route("/player_profil_public/{id}", name="player_profil_public")
     */
    public function showPlayer($id){

        // getRepository est une méthode de Doctrine qui va ensuite faire appel a notre class Player (Player::class)
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        // si l' ID n' existe, on crée une exception qui stoppe le script
        if(!$user){
            throw new Exception('Ce joueur n\' existe pas');
        }
        
        return $this->render('public/player_profil_public.html.twig', [
            'user' => $user
        ]);
    }

    // permet a chaque joueur ou coach d' acceder a son profil privé, parametrable

    /**
     * @Route("/profil_prive/{id}", name="profil_prive")
     */
    public function profilPrive($id){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $plannings = $this->getDoctrine()->getRepository(Planning::class)->findAll();

    
        // si l' ID n' existe, on crée une exception qui stoppe le script
        if(!$user){
            throw new Exception('Cet utilisateur n\' existe pas');
        }

        return $this->render('public/profil_prive.html.twig', [
            'user' => $user,
            'plannings' => $plannings
        ]);
    }

    /**
     * @Route("/profil/form/edit/{id}", name="profil_edit")
     */
    public function editUser(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $lieu = $this->getDoctrine()->getRepository(Lieu::class)->findAll();
        $planning = $this->getDoctrine()->getRepository(Planning::class)->findAll();
        $langues = $this->getDoctrine()->getRepository(Langue::class)->findAll();
        $specialites = $this->getDoctrine()->getRepository(Specialite::class)->findAll();
  
        $form = $this->createForm(UpdateFormType::class, $user,['langues' => $langues],['specialites' => $specialites]);
  
        if($request->isMethod('POST')){
            $form->handleRequest($request);
  
            if($form->isSubmitted() && $form->isValid()){

                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('Password')->getData()
                    )
                );

                if ($user->getCategory() == 'coach'){
                    $user->setRoles(['ROLE_COACH']);
                }
                else{
                    $user->setRoles(['ROLE_PLAYER']);
                }

                $em = $this->getDoctrine()->getManager();
                
                $em->flush();

                // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
                $this->addFlash('profil_edit','Votre Profil a bien été modifié');
  
                return $this->redirectToRoute('profil_prive',[
                    'id' => $id
                ]);
            }
        }
  
        return $this->render('public/updateForm.html.twig', [
            'updateForm' => $form->createView(),
            'lieu' => $lieu,
            'planning' => $planning,
            'langue' => $langues
        ]);
    }

    // ----------------------------section pour gérer le planning (ajout et modification)----------------------

    /**
     * @Route("/ajouter_disponibilite", name="ajouter_disponibilite", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planning = new Planning();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if($user){
                $planning->setUser($user);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planning);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('public/addPlanningForm.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    // -----------------------fin de gestion du planning---------------------------

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(){
        // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        // $this->addFlash('register_success','Votre Inscription est réussie ! Vous pouvez naviguer sur Set & Match, ou aller sur votre profil personnel pour le compléter !');

        return $this->render('register.html.twig');
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(){
        return $this->render('login.html.twig');
    }


}