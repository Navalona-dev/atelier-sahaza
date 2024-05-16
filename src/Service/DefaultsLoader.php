<?php

namespace App\Service;

use DateTime;
use App\Entity\Type;
use App\Entity\Contact;
use App\Entity\Gallery;
use App\Entity\Product;
use App\Entity\Quality;
use App\Entity\Category;
use App\Entity\HomePage;
use App\Entity\SocialLink;
use App\Entity\HomePageBlock;
use Symfony\Component\Yaml\Yaml;
use App\Repository\TypeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Filesystem\Filesystem;


class DefaultsLoader
{

    private $em;
    private $typeRepository;
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->em = $em;
        $this->typeRepository = $typeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    private function maybeCreate($class, $criteria, ?string $repositoryMethodName = 'findOneBy'): array
    {
        $entity = $this->em->getRepository($class)->{$repositoryMethodName}($criteria);
        $isNew = is_null($entity);
        if ($isNew) {
            $entity = new $class;
        }
        return [$isNew, $entity];
    }

    public function loadDb()
    {
        $this->contacts();
        $this->socialLinks();
        $this->homePages();
        $this->qualites();
        $this->categories();
        $this->types();
        $this->produits();
        
    }


    public function contacts() {
        $contacts = Yaml::parseFile('defaults/data/contact.yaml');

        foreach ($contacts as $name => $content) {
            list($isNewContact, $contact) = $this->maybeCreate(Contact::class, ['name' => $name]);
            if($isNewContact){
                $date = new \DateTime();
                $contact->setName($content['name']);
                $contact->setIsActive(true);
                if($content['contact']) $contact->setContact($content['contact']);
                if($content['email']) $contact->setEmail($content['email']);
                if($content['adresse']) $contact->setAdresse($content['adresse']);
                if($content['contact']) $contact->setContact($content['contact']);
                $contact->setCreatedAt($date);
                $this->em->persist($contact);
                $this->em->flush();

            }
            
        }
    }

    public function socialLinks() {
        $socialLinks = Yaml::parseFile('defaults/data/socialLink.yaml');

        foreach ($socialLinks as $name => $content) {
            list($isNewSocialLink, $socialLink) = $this->maybeCreate(SocialLink::class, ['name' => $name]);
            if($isNewSocialLink){
                $date = new \DateTime();
                $socialLink->setName($content['name']);
                $socialLink->setLink($content['link']);
                $socialLink->setIcon($content['icon']);
                $socialLink->setIsActive(true);
                $socialLink->setCreatedAt($date);
                $this->em->persist($socialLink);
                $this->em->flush();

            }
            
        }
    }

    public function homePages() {
        $homePages = Yaml::parseFile('defaults/data/homePage.yaml');

        foreach ($homePages as $label => $content) {
            list($isNewHomePage, $homePage) = $this->maybeCreate(HomePage::class, ['label' => $label]);
            if($isNewHomePage){
                $date = new \DateTime();
                $homePage->setLabel($label);
                $homePage->setIsActive(true);
                $homePage->setCreatedAt($date);
                if($content['title']) $homePage->setTitle($content['title']);
                if($content['description']) $homePage->setDescription($content['description']);
                $this->em->persist($homePage);
                $this->em->flush();
            }

            $subBlocks = $content['subBlocks'] ?? [];
            foreach ($subBlocks as $label => $subBlock) {
                list($isNewSubBlock, $newSubBlock) = $this->maybeCreate(HomePageBlock::class, ['homePage' => $homePage, 'label' => $label]);
                if($isNewSubBlock){
                    $date = new \DateTime();

                    $newSubBlock->setHomePage($homePage);
                    $newSubBlock->setLabel($label);
                    $newSubBlock->setTitle($subBlock['title']);
                    $newSubBlock->setDescription($subBlock['description']);
                    $newSubBlock->setIsActive(true);
                    $newSubBlock->setCreatedAt($date);

                    $this->em->persist($newSubBlock);
                    $this->em->flush();
                }
                
            }
        
        }
    }

    public function qualites() {
        $qualites = Yaml::parseFile('defaults/data/quality.yaml');

        foreach ($qualites as $label => $content) {
            list($isNewQuality, $quality) = $this->maybeCreate(Quality::class, ['label' => $label]);
            if($isNewQuality){
                $date = new \DateTime();
                $quality->setLabel($label);
                $quality->setTitle($content['title']);
                $quality->setDescription($content['description']);
                $quality->setIcon($content['icon']);
                $quality->setIsActive(true);
                $quality->setCreatedAt($date);
                $this->em->persist($quality);
                $this->em->flush();

            }
            
        }
    }

    public function categories() {
        $categories = Yaml::parseFile('defaults/data/category.yaml');

        foreach ($categories as $label => $content) {
            list($isNewCategory, $category) = $this->maybeCreate(Category::class, ['label' => $label]);
            if($isNewCategory){
                $date = new \DateTime();
                $category->setLabel($label);
                $category->setName($content['name']);
                $category->setIsActive(true);
                $category->setCreatedAt($date);
                $this->em->persist($category);
                $this->em->flush();

            }
            
        }
    }

    public function types() {
        $types = Yaml::parseFile('defaults/data/type.yaml');

        foreach ($types as $label => $content) {
            list($isNewType, $type) = $this->maybeCreate(Type::class, ['label' => $label]);
            if($isNewType){
                $date = new \DateTime();
                $type->setLabel($label);
                $type->setName($content['name']);
                $type->setIsActive(true);
                $type->setCreatedAt($date);
                $this->em->persist($type);
                $this->em->flush();

            }
            
        }
    }

    public function produits() {
        $produits = Yaml::parseFile('defaults/data/produit.yaml');

        $types = $this->typeRepository->findAll();
        $categories = $this->categoryRepository->findAll();

        foreach ($produits as $label => $content) {
            list($isNewProduit, $produit) = $this->maybeCreate(Product::class, ['label' => $label]);
            if($isNewProduit){
                $date = new \DateTime();
                $produit->setLabel($label);
                $produit->setImage($content['image']);
                $produit->setName($content['name']);
                $produit->setDescription($content['description']);
                $produit->setReference($content['reference']);
                $produit->setIsActive(true);
                $produit->setCreatedAt($date);

               // Choisissez un type parmi les types disponibles
                if (!empty($types)) {
                    $randomType = $types[array_rand($types)];
                    $produit->setType($randomType);
                } else {
                    // Gérer le cas où aucun type n'est trouvé
                }

               // Choisissez un categorie parmi les categories disponibles
               if (!empty($categories)) {
                    $randomCategorie = $categories[array_rand($categories)];
                    $produit->setCategory($randomCategorie);
                } else {
                    // Gérer le cas où aucun categorie n'est trouvé
                }

                $this->em->persist($produit);
                $this->em->flush();

            }
            
        }
    }


    public function copyFiles()
    {
        $fs = new Filesystem();
        $fileDefs = Yaml::parseFile('defaults/files.yaml') ?? [];
        foreach ($fileDefs as $destDir => $fileMappings) {
            foreach ($fileMappings as $dest => $source) {
                $destFile = u('/')->join([u($destDir), $dest]);
                $fs->copy($source, $destFile);
            };
        };
    }
}
