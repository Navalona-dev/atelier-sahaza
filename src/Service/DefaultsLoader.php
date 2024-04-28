<?php

namespace App\Service;

use DateTime;
use App\Entity\Contact;
use App\Entity\HomePage;
use App\Entity\SocialLink;
use App\Entity\HomePageBlock;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Filesystem\Filesystem;


class DefaultsLoader
{

    private $em;


    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
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
            list($isNewContact, $socialLink) = $this->maybeCreate(SocialLink::class, ['name' => $name]);
            if($isNewContact){
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
