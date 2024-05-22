<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Form\MessageType;
use App\Form\TypeFormType;
use App\Repository\CategoryRepository;
use App\Repository\MessageRepository;
use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                $produit = new Product();
                 $form = $this->createForm(ProductType::class, $produit);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $date = new \DateTime();
                    $produit->setCreatedAt($date)
                            ->setIsActive(1);
                    
                    $this->em->persist($produit);
                    $this->em->flush();

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
                    }

                    $this->addFlash('success', 'Produit ajouté avec succès');
                    return $this->redirectToRoute('app_admin_liste');
                }
                break;

            case 'type':
                $type = new Type();
                 $form = $this->createForm(TypeFormType::class, $type);
                
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    
                    $date = new \DateTime();
                    $type->setCreatedAt($date)
                            ->setIsActive(1);
                    
                    $this->em->persist($type);
                    $this->em->flush();

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
                    }

                    $this->addFlash('success', 'Type de produit ajouté avec succès');
                    return $this->redirectToRoute('app_admin_liste');
                }
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
    public function delete($id, Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository, MessageRepository $messageRepository, TypeRepository $typeRepository): Response
    {
        $menu = $request->get('menu');
        $entity = null;
        switch ($menu) {
            case 'categorie':
                $entity = $categoryRepository->find($id);
                break;
            case 'produit':
                $entity = $productRepository->find($id);
                break;
            case 'message':
                $entity = $messageRepository->find($id);
                break;
            case 'type':
                $entity = $typeRepository->find($id);
                break;
            default:
                # code...
                break;
        }
        

        if ($entity) {
            $this->em->remove($entity);
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
    public function update($id, Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository, MessageRepository $messageRepository): Response
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

                    $this->addFlash('success', 'Categorie modifié avec succès');
                    return $this->redirectToRoute('app_admin_liste');
                }
                return $this->render('admin/liste/modal_update.html.twig', [
                    'form' => $form->createView(),
                    'id' => $request->get('id'),
                    'menu' => $menu
                ]);
                break;
            case 'produit':
                $produit = $productRepository->find($id);
                $form = $this->createForm(ProductType::class, $produit);
               $form->handleRequest($request);

               if ($form->isSubmitted() && $form->isValid()) {
                //dd($request);
                   $date = new \DateTime();
                   $produit->setCreatedAt($date)
                           ->setIsActive(1);
                   
                   $this->em->persist($produit);
                   $this->em->flush();

                   if ($request->isXmlHttpRequest()) {
                       return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
                   }

                   $this->addFlash('success', 'Produit modifié avec succès');
                   return $this->redirectToRoute('app_admin_liste');
               }
               return $this->render('admin/liste/modal_update_produit.html.twig', [
                'form' => $form->createView(),
                'id' => $request->get('id'),
                'menu' => $menu
            ]);
               break;
            case 'message':
            $message = $messageRepository->find($id);
    
            $form = $this->createForm(MessageType::class, $message);
            
            $form->handleRequest($request);

            /*if ($form->isSubmitted() && $form->isValid()) {
                $date = new \DateTime();
                $category->setCreatedAt($date)
                        ->setIsActive(1);
                
                $this->em->persist($category);
                $this->em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
                }

                $this->addFlash('success', 'Categorie modifié avec succès');
                return $this->redirectToRoute('app_admin_liste');
            }*/
            return $this->render('admin/liste/modal_message.html.twig', [
                'form' => $form->createView(),
                'id' => $request->get('id'),
                'menu' => $menu
            ]);
            break;
            default:
                # code...
                break;
        }
    }

}
