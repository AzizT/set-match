<?php

namespace App\Controller;

use Exception;

use App\Entity\User;

use App\Entity\Planning;

use App\Entity\Langue;

use App\Entity\Specialite;

use App\Form\AddUserFormType;

use App\Form\UpdateUserFormType;

use App\Form\PlanningType;

use App\Repository\PlanningRepository;

use Symfony\Component\HttpFoundation\Response;

use App\Form\LangueType;

use App\Form\SpecialiteType;

use Symfony\Component\HttpFoundation\Request;

// necessaire pour générer du twig via render dans DefaultController
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// je declare le prefixe admin pour toutes les routes que je vais créer dans ce Controller
 /**
  * @Route("/admin", name="admin_")
  */
class AdminController extends AbstractController{

    // *********************************début de gestion des users**********************************

   /**
     * @Route("/liste_users", name="liste_users")
     */
  public function listeUsers(){
    $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/liste_users.html.twig', [
            'users' => $users
            ]);
  }

   /**
     * @Route("/user/form/add", name="user_add")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){

      $user = new User;

      $form = $this->createForm(AddUserFormType::class, $user);

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

              $em->persist($user);
              $em->flush();

              // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
      $this->addFlash('user_add','L\' inscrit "'.$user->getUsername().'" a bien été ajoutée');

              return $this->redirectToRoute('admin_liste_users');
          }
      }

      return $this->render('admin/form-user.html.twig', [
          'addUserForm' => $form->createView()
      ]);
      
  }

  /**
     * @Route("/user/form/edit/{id}", name="user_edit")
     */
    public function editUser(Request $request, $id){
      $user = $this->getDoctrine()->getRepository(User::class)->find($id);

      $form = $this->createForm(UpdateUserFormType::class, $user);

      if($request->isMethod('POST')){
          $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()){
              
              if ($user->getCategory() == 'coach'){
                $user->setRoles(['ROLE_COACH']);
              }
              else{
                $user->setRoles(['ROLE_PLAYER']);
              }

              $em = $this->getDoctrine()->getManager();
              
              $em->flush();

              // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
      $this->addFlash('user_edit','L\' inscrit "'.$user->getUsername().'" a bien été modifié');

              return $this->redirectToRoute('admin_liste_users',[
                  'id' => $id
              ]);
          }
      }

      return $this->render('admin/edit-user.html.twig', [
          'updateUserForm' => $form->createView()
      ]);
  }

  /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function deleteUser($id){

      $user = $this->getDoctrine()->getRepository(User::class)->find($id);

      // si l' ID n' existe pas, on crée une exception qui stoppe le script
       if(!$user){
           throw new Exception('Ce user n\' existe pas');
       }

      // on fait appel au manager dès qu' il y a ajout,modif ou suppresssion. Il n' intervient pas pour appeler la bdd, mais pour tout le reste
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($user);
      // flush, comme pour l' ajout, sauf que pas besoin de persist
      $entityManager->flush();

      // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
      $this->addFlash('user_remove','L\' inscrit "'.$user->getUsername().'" a bien été supprimé');
      
      return $this->redirectToRoute('admin_liste_users');
  }

    // --------------------------------------fin de gestion des users-------------------------------------------

    // --------------------------------------début de gestion des langues-------------------------------------------

    /**
   * @Route("/liste_langues", name="liste_langues")
   */
  public function listeLangues(){
    $langues = $this->getDoctrine()->getRepository(Langue::class)->findAll();

    return $this->render('admin/liste_langues.html.twig',[
      'langues' => $langues
    ]);
    }

    /**
     * @Route("/langue/form/add", name="langue_add")
     */
    public function addLangue(Request $request){

    $langue = new Langue;

    $form = $this->createForm(LangueType::class, $langue);

    if($request->isMethod('POST')){
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($langue);
            $em->flush();

            // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
            $this->addFlash('langue_add','La langue "'.$langue->getNom().'" a bien été ajoutée');

            return $this->redirectToRoute('admin_liste_langues');
        }
    }

    return $this->render('admin/form-langue.html.twig', [
        'formulaire' => $form->createView()
    ]);

    }

    /**
     * @Route("/langue/form/edit/{id}", name="langue_edit")
    */
    public function editLangue(Request $request, $id){
        $langue = $this->getDoctrine()->getRepository(Langue::class)->find($id);

        $form = $this->createForm(LangueType::class, $langue);

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();

                $em->flush();

                // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
                $this->addFlash('langue_edit','La langue "'.$langue->getNom().'" a bien été modifiée');

                return $this->redirectToRoute('admin_liste_langues');
            }
        }

        return $this->render('admin/form-langue.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/langue/delete/{id}", name="langue_delete")
     */
    public function deleteLangue($id){

        $langue = $this->getDoctrine()->getRepository(Langue::class)->find($id);

        if(!$langue){
            throw new Exception('Cette langue n\' existe pas');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($langue);
        $entityManager->flush();

        // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        $this->addFlash('langue_remove','La langue "'.$langue->getNom().'" a bien été supprimée');

        return $this->redirectToRoute('admin_liste_langues');
    }

    // --------------------------------------fin de gestion des langues-------------------------------------------

    // --------------------------------------début de gestion des spécialités-------------------------------------------

    /**
     * @Route("/liste_specialites", name="liste_specialites")
     */
    public function listeSpecialites(){
        $specialites = $this->getDoctrine()->getRepository(Specialite::class)->findAll();

        return $this->render('admin/liste_specialites.html.twig',[
        'specialites' => $specialites
        ]);
    }

    /**
     * @Route("/specialite/form/add", name="specialite_add")
     */
    public function addSpecialite(Request $request){

    $specialite = new Specialite;

    $form = $this->createForm(SpecialiteType::class, $specialite);

    if($request->isMethod('POST')){
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($specialite);
            $em->flush();

            // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        $this->addFlash('specialite_add','La specialité "'.$specialite->getTitre().'" a bien été ajoutée');

            return $this->redirectToRoute('admin_liste_specialites');
        }
    }

    return $this->render('admin/form-specialite.html.twig', [
        'formulaire' => $form->createView()
    ]);

    }

    /**
     * @Route("/specialite/form/edit/{id}", name="specialite_edit")
    */
    public function editSpecialite(Request $request, $id){
        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->find($id);

        $form = $this->createForm(SpecialiteType::class, $specialite);

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();

                $em->flush();

                // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        $this->addFlash('specialite_edit','La specialité "'.$specialite->getTitre().'" a bien été modifiée');

                return $this->redirectToRoute('admin_liste_specialites');
            }
        }

        return $this->render('admin/form-specialite.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/specialite/delete/{id}", name="specialite_delete")
     */
    public function deleteSpecialite($id){

        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->find($id);

        if(!$specialite){
            throw new Exception('Cette spécialité n\' existe pas');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($specialite);
        $entityManager->flush();

        // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        $this->addFlash('specialite_remove','La specialité "'.$specialite->getTitre().'" a bien été supprimée');

        return $this->redirectToRoute('admin_liste_specialites');
    }

    // --------------------------------------fin de gestion des spécialités-------------------------------------------

    // --------------------------------------début de gestion des plannings + lieux-----------------------------------

    /**
     * @Route("/liste_plannings", name="liste_plannings", methods={"GET"})
     */
    public function index(PlanningRepository $planningRepository): Response
    {
        return $this->render('admin/liste_plannings.html.twig', [
            'plannings' => $planningRepository->findAll(),
        ]);
    }

    /**
     * @Route("/planning/form/add", name="planning_add", methods={"GET","POST"})
     */
    public function addPlanning(Request $request): Response
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

            return $this->redirectToRoute('admin_liste_plannings');
        }

        return $this->render('admin/form-planning.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="planning_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Planning $planning): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_liste_plannings');
        }

        return $this->render('admin/edit-planning.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/planning/delete/{id}", name="planning_delete")
     */
    public function deletePlanning($id){

        $planning = $this->getDoctrine()->getRepository(Planning::class)->find($id);
  
        // si l' ID n' existe pas, on crée une exception qui stoppe le script
         if(!$planning){
             throw new Exception('Ce user n\' existe pas');
         }
  
        // on fait appel au manager dès qu' il y a ajout,modif ou suppresssion. Il n' intervient pas pour appeler la bdd, mais pour tout le reste
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($planning);
        // flush, comme pour l' ajout, sauf que pas besoin de persist
        $entityManager->flush();
  
        // ajout d' un message flash (avec concatenation) pour alerter en front. Premier parametre, le nom du message, le second, le message a afficher
        $this->addFlash('planning_remove','Le planning "'.$planning->getId().'" a bien été supprimé');
        
        return $this->redirectToRoute('admin_liste_plannings');
    }

    // --------------------------------------fin de gestion des plannings + lieux-----------------------------------

}