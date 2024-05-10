<?php

namespace App\Controller\Admin;

use App\Repository\TypeRepository;
use App\Repository\ContactRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\SocialLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private $contactRepo;
    private $socialLinkRepo;
    private $productRepository;
    private $categoryRepository;
    private $typeRepository;

    public function __construct(
        ContactRepository $contactRepo,
        SocialLinkRepository $socialLinkRepo,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        TypeRepository $typeRepository
    )
    {
        $this->contactRepo = $contactRepo;
        $this->socialLinkRepo = $socialLinkRepo;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function index()
    {
        return $this->render('admin/dashboard/index.html.twig', [

        ]);
    }

    /**
     * @Route("admin/liste", name="app_admin_liste")
     */
    public function loadMenuContent(Request $request)
    {
        $menu = $request->get('menu');

        $request->getSession()->set('menu', $menu);

        $listes = [];

        if($menu == "contact") {
            $listes = $this->contactRepo->findAll();
        } elseif($menu == "social") {
            $listes = $this->socialLinkRepo->findAll();
        } elseif ($menu == "produit") {
            $listes = $this->productRepository->findAll();
        } elseif ($menu == "categorie") {
            $listes = $this->categoryRepository->findAll();
        } elseif ($menu == "type") {
            $listes = $this->typeRepository->findAll();
        }

        if(!$menu) {
            return $this->render('admin/dashboard/index.html.twig');
        } else {

            $content = $this->renderView('admin/table/index.html.twig', [
                'menu' => $menu,
                'listes' => $listes
            ]);

            return new JsonResponse($content);

        }

    }

    /**
     * @Route("/admin/liste/update-is-active/{id}", name="app_is_active")
     */
    public function updateIsActive(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id');
        $menu = $request->getSession()->get('menu');

        switch ($menu) {
            case 'contact':
                $entity = $this->contactRepo->findOneById($id);
                break;
            case 'social':
                $entity = $this->socialLinkRepo->findOneById($id);
                break;
            case 'produit':
                $entity = $this->productRepository->findOneById($id);
                break;
            case 'categorie':
                $entity = $this->categoryRepository->findOneById($id);
                break;
            case 'type':
                $entity = $this->typeRepository->findOneById($id);
                break;
            default:
                throw $this->createNotFoundException('Menu inconnu');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Aucune entité trouvée pour l\'identifiant. '.$id);
        }

        $entity->setIsActive(!$entity->isIsActive());
        $em->persist($entity);
        $em->flush();

        // Renvoyer une réponse JSON avec l'état mis à jour
        return new JsonResponse(['isActive' => $entity->isIsActive()]);
    }

    
}
