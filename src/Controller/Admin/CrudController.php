<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/data/new", name="app_admin_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $menu = $request->get('menu');
       
        switch ($menu) {
            case 'categorie':
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
                break;
            case 'produit':
                break;
            default:
                # code...
                break;
        }
        

        return $this->render('admin/liste/new.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    /**
     * @Route("/data/delete/{id}", name="app_admin_delete", methods={"POST"})
     */
    public function delete($id, Request $request, CategoryRepository $categoryRepository): Response
    {
        
        $category = $categoryRepository->find($id);

        if ($category) {
            $this->em->remove($category);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
            }
        }
        return new Response("ok");
    }

    /**
     * @Route("/data/update/{id}", name="app_admin_update", methods={"POST"})
     */
    public function update($id, Request $request, CategoryRepository $categoryRepository): Response
    {
        $menu = $request->get('menu');

        switch ($menu) {
            case 'categorie':
                $category = $categoryRepository->find($id);
        
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

                break;
            case 'produit':
                break;
            default:
                # code...
                break;
        }
        
        return $this->render('admin/liste/modal_update.html.twig', [
            'form' => $form->createView(),
            'id' => $request->get('id'),
            'menu' => $menu
        ]);
    }

}
