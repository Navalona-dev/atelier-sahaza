<?php

namespace App\Controller\Front;

use App\Repository\ContactRepository;
use App\Repository\GalleryRepository;
use App\Repository\ProductRepository;
use App\Repository\QualityRepository;
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
    private $qualityRepository;
    private $productRepository;

    public function __construct(
        SocialLinkRepository $socialLinkRepository,
        ContactRepository $contactRepository,
        HomePageRepository $homePageRepository,
        QualityRepository $qualityRepository,
        ProductRepository $productRepository
    )
    {
        $this->socialLinkRepository = $socialLinkRepository;
        $this->contactRepository = $contactRepository;
        $this->homePageRepository = $homePageRepository;
        $this->qualityRepository = $qualityRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);
        $contact = $this->contactRepository->findOneBy(['isActive' => true]);
        $qualities = $this->qualityRepository->findBy(['isActive' => true]);
        $produits = $this->productRepository->findBy(['isActive' => true]);

        $homePages = $this->homePageRepository->findAll();

        return $this->render('front/home/index.html.twig', [
            'contact' => $contact,
            'socialLinks' => $socialLinks,
            'homePages' => $homePages,
            'qualites' => $qualities,
            'produits' => $produits
        ]);
    }
}
