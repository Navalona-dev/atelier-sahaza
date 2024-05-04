<?php

namespace App\Controller\Admin;

use App\Service\SendMailService;
use App\Form\ResetPasswordFormType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{

    /**
     * @Route("/admin/mon-compte", name="app_admin_my_account")
     */
    public function myAccount() 
    {
        $user = $this->getUser();

        return $this->render("admin/account/mon_compte.html.twig", [
            'user' => $user
        ]);
    }

     /**
     * @Route("/oubli-pass", name="forgotten_password_admin")
     * 
     */
    public function forgottenPassword(
        Request $request, 
        AdminRepository $adminRepo, 
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $em,
        SendMailService $mail
        ): Response
    {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $messageSuccess = 'Un Email a été envoyé avec succès dans votre boîte de reception, merci de le consulter';
            $messageError = 'Un problème est survenu, votre adresse e-mail n\'existe pas, veuillez réessayer en cliquant ce lien';

            //On va chercher l'utilisateur par son email
            $admin = $adminRepo->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if($admin){
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $admin->setResetTokenPass($token);
                $em->persist($admin);
                $em->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('app_admin_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                $context = compact('url', 'admin');

                $title = 'Réinitialisation de mot de passe sur le site Le Carre VIP';

                // Envoi du mail
                $mail->send(
                    'lnomenjanahary68@gmail.com',
                    $admin->getEmail(),
                    $title,
                    'password_reset',
                    $context
                );

                $this->addFlash(
                    'success', 
                    $messageSuccess
                );
                return $this->redirectToRoute('app_login');
            }
            // $admin est null
            $this->addFlash(
                'danger', 
                $messageError
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/account/reset_password_request.html.twig', 
        [
            'requestPassForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/mot-de-passe-oublie/{token}", name="app_admin_reset_password")
     */
    public function resetPass(
        string $token, 
        Request $request,
        AdminRepository $adminRepo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $encoder): Response
    {

        // On vérifie si on a ce token dans la base
        $admin = $adminRepo->findOneByResetTokenPass($token);
        
        // On vérifie si l'utilisateur existe

        $messageSuccess = 'Mot de passe changé avec succès';
        $messageError = 'Jeton invalide';

        if($admin){
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $admin->setResetTokenPass('');
                
                
            // On enregistre le nouveau mot de passe en le hashant
                $admin->setPassword(
                    $encoder->hashPassword(
                        $admin,
                        $form->get('password')->getData()
                    )
                );
                $em->persist($admin);
                $em->flush();

                $this->addFlash('success', $messageSuccess);
                return $this->redirectToRoute('app_login');
            }

            return $this->render('admin/account/reset_password.html.twig', [
                'passForm' => $form->createView(),
            ]);
        }
        
        // Si le token est invalide on redirige vers le login
        $this->addFlash('danger', $messageError);
        return $this->redirectToRoute('app_login');
    }
}
