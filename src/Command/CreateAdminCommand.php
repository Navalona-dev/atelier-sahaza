<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\ORMException;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    private $adminRepository;
    private $em;
    protected $passwordEncoder;

    /**
     * CreateUserCommand constructor.
     * @param AdminRepository $adminRepository
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param string|null $name
     */
    public function __construct(
        AdminRepository $adminRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder,
        string $name = null
    )
    {
        $this->adminRepository = $adminRepository;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Command to create user')
            ->setHelp('This command allows you to create a user...')
            ->addArgument('lastName', InputArgument::REQUIRED, 'The name of the user.')
            ->addArgument('firstName', InputArgument::REQUIRED, 'The firstname of the user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'User password plain password')
            ->addArgument('role', InputArgument::REQUIRED, 'User role (e.g. ROLE_ADMIN)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nom = $input->getArgument('lastName');
        $prenom = $input->getArgument('firstName');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');
        if ($nom && $prenom && $email && $password && $role && in_array($role, Admin::$ROLES, false)) {
            $adminExist = $this->adminRepository->findOneBy([
                'email' => $email
            ]);
            if (!$adminExist) {
                $admin = new Admin();
                $admin->setFirstName($nom);
                $admin->setLastName($prenom);
                $admin->setEmail($email);
                $roles = ['ROLE_ADMIN'];
                $roles [] = $role;
                $admin->setRoles(array_unique($roles));
                $admin->setPassword($this->passwordEncoder->hashPassword($admin, $password));
                $this->em->persist($admin);
                $this->em->flush();
                $io->success('Admin has been created.');
                return Command::SUCCESS;
            }
        }
        $io->error('Email already used or role invalid.');
        return Command::FAILURE;
    }
}
