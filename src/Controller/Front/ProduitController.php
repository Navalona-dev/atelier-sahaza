<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\HomePageRepository;
use App\Repository\SocialLinkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    private $productRepo;
    private $homePageRepository;
    private $socialLinkRepository;
    private $categoryRepo;
    private $paginator;


    public function __construct (
        ProductRepository $productRepo,
        HomePageRepository $homePageRepository,
        SocialLinkRepository $socialLinkRepository,
        CategoryRepository $categoryRepo,
        PaginatorInterface $paginator

    )
    {
        $this->productRepo = $productRepo;
        $this->homePageRepository = $homePageRepository;
        $this->socialLinkRepository = $socialLinkRepository;
        $this->categoryRepo = $categoryRepo;
        $this->paginator = $paginator;

    }
    /**
     * @Route("/produit/metallique", name="app_front_produit_metallique")
     */
    public function produitMetallique(Request $request): Response
    {
        $homePages = $this->homePageRepository->findAll();

        $products = $this->productRepo->findProductMetallique();

        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);

        $categories = $this->categoryRepo->findBy(
            ['isActive' => true],
            ['name' => 'ASC']
        );

        $produits = $this->paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2
        );
        

        return $this->render('front/product/produit_metallique.html.twig', [
            'products' => $produits,
            'homePages' => $homePages,
            'socialLinks' => $socialLinks,
            'categories' => $categories

        ]);
    }

    /**
     * @Route("/produit/allimunium", name="app_front_produit_allimunium")
     */
    public function produitAllimunium(Request $request): Response
    {
        $homePages = $this->homePageRepository->findAll();

        $products = $this->productRepo->findProductAllimunium();

        $socialLinks = $this->socialLinkRepository->findBy(['isActive' => true]);

        $categories = $this->categoryRepo->findBy(
            ['isActive' => true],
            ['name' => 'ASC']
        );

        $produits = $this->paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('front/product/produit_allimunium.html.twig', [
            'products' => $produits,
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
