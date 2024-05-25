<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\HomePageRepository;
use App\Repository\SocialLinkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    private $productRepo;
    private $homePageRepository;
    private $socialLinkRepository;
    private $categoryRepo;

    public function __construct (
        ProductRepository $productRepo,
        HomePageRepository $homePageRepository,
        SocialLinkRepository $socialLinkRepository,
        CategoryRepository $categoryRepo

    )
    {
        $this->productRepo = $productRepo;
        $this->homePageRepository = $homePageRepository;
        $this->socialLinkRepository = $socialLinkRepository;
        $this->categoryRepo = $categoryRepo;

    }
    /**
     * @Route("/produit/metallique", name="app_front_produit_metallique")
     */
    public function produitMetallique(): Response
    {
        $homePages = $this->homePageRepository->findAll();

        $products = $this->productRepo->findProductMetallique();

        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);

        $categories = $this->categoryRepo->findBy(
            ['isActive' => true],
            ['name' => 'ASC']
        );

        return $this->render('front/product/produit_metallique.html.twig', [
            'products' => $products,
            'homePages' => $homePages,
            'socialLinks' => $socialLinks,
            'categories' => $categories

        ]);
    }

    /**
     * @Route("/produit/allimunium", name="app_front_produit_allimunium")
     */
    public function produitAllimunium(): Response
    {
        $homePages = $this->homePageRepository->findAll();

        $products = $this->productRepo->findProductAllimunium();

        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);

        $categories = $this->categoryRepo->findBy(
            ['isActive' => true],
            ['name' => 'ASC']
        );

        return $this->render('front/product/produit_allimunium.html.twig', [
            'products' => $products,
            'homePages' => $homePages,
            'socialLinks' => $socialLinks,
            'categories' => $categories

        ]);
    }

    /**
     * @Route("/produit/detail/{id}", name="app_front_produit")
     */

     public function produitDetail($id): Response
     {
        $homePages = $this->homePageRepository->findAll();

        $product = $this->productRepo->find($id);

        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);

         return $this->render('front/product/produit_detail.html.twig', [
             'produit' => $product,
             'homePages' => $homePages,
             'socialLinks' => $socialLinks,
             
         ]);
     }
}
