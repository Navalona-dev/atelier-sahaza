<?php

namespace App\Controller\Front;

use App\Repository\ContactRepository;
use App\Repository\HomePageRepository;
use App\Repository\SocialLinkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $socialLinkRepository;
    private $contactRepository;
    private $homePageRepository;

    public function __construct(
        SocialLinkRepository $socialLinkRepository,
        ContactRepository $contactRepository,
        HomePageRepository $homePageRepository
    )
    {
        $this->socialLinkRepository = $socialLinkRepository;
        $this->contactRepository = $contactRepository;
        $this->homePageRepository = $homePageRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);
        $contact = $this->contactRepository->findOneBy(['isActive' => true]);

        $homePages = $this->homePageRepository->findAll();

        return $this->render('front/home/index.html.twig', [
            'contact' => $contact,
            'socialLinks' => $socialLinks,
            'homePages' => $homePages
        ]);
    }
}
