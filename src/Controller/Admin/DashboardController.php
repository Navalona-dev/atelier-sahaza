<?php

namespace App\Controller\Admin;

use App\Repository\ContactRepository;
use App\Repository\SocialLinkRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private $contactRepo;
    private $socialLinkRepo;

    public function __construct(
        ContactRepository $contactRepo,
        SocialLinkRepository $socialLinkRepo
    )
    {
        $this->contactRepo = $contactRepo;
        $this->socialLinkRepo = $socialLinkRepo;
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

        $listes = [];

        if($menu == "contact") {
            $listes = $this->contactRepo->findAll();
        } elseif($menu == "social") {
            $listes = $this->socialLinkRepo->findAll();
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
}
