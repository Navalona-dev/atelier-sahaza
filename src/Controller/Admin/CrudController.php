<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CrudController extends AbstractController
{

    private $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

   /**
     * @Route("/admin/nouveau", name="app_admin_new")
     */
    public function new(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $category->setCreatedAt($date)
                    ->setIsActive(1);

            $this->em->persist($category);
            $this->em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
            }

            $this->addFlash('success', 'Categorie ajouté avec succès');
            return $this->redirectToRoute('app_admin_liste');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => 'error', 'message' => 'Form is invalid'], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('admin/liste/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
