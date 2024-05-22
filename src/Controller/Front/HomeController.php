<?php

namespace App\Controller\Front;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ContactRepository;
use App\Repository\GalleryRepository;
use App\Repository\ProductRepository;
use App\Repository\QualityRepository;
use App\Repository\HomePageRepository;
use App\Repository\MessageRepository;
use App\Repository\SocialLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class HomeController extends AbstractController
{
    private $socialLinkRepository;
    private $contactRepository;
    private $homePageRepository;
    private $qualityRepository;
    private $productRepository;
    private $em;

    public function __construct(
        SocialLinkRepository $socialLinkRepository,
        ContactRepository $contactRepository,
        HomePageRepository $homePageRepository,
        QualityRepository $qualityRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    )
    {
        $this->socialLinkRepository = $socialLinkRepository;
        $this->contactRepository = $contactRepository;
        $this->homePageRepository = $homePageRepository;
        $this->qualityRepository = $qualityRepository;
        $this->productRepository = $productRepository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);
        $contacts = $this->contactRepository->findBy(['isActive' => true]);
        $qualities = $this->qualityRepository->findBy(['isActive' => true]);
        $produits = $this->productRepository->findBy(['isActive' => true]);

        $homePages = $this->homePageRepository->findAll();

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message); 

        return $this->render('front/home/index.html.twig', [
            'contacts' => $contacts,
            'socialLinks' => $socialLinks,
            'homePages' => $homePages,
            'qualites' => $qualities,
            'produits' => $produits,
            'formMessage' => $form->createView()
        ]);
    }

    /**
     * @Route("/send/message", name="send_message", methods={"POST"} )
     */
    public function sendMessage(Request $request, MessageRepository $messageRepository, Environment $environment): Response
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $message->setCreatedAt($date);
            
            $this->em->persist($message);
            $this->em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'success', 'fullGlobalRecapHtml' => $environment->load('front/home/index.html.twig')->renderBlock('messageNotificationContainer', [
                    'messageNotification' => "Message envoyé avec succès",
                ]), Response::HTTP_OK]);
            }

            $this->addFlash('success', 'Message envoyé avec succès');
            return $this->redirectToRoute('app_admin_liste');
        }
    }
}
