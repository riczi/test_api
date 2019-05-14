<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    /** @var ObjectManager */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * CreateUserCommand constructor.
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Creates a new user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('plainPassword', InputArgument::REQUIRED)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('plainPassword');

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $this->manager->persist($user);
        $this->manager->flush();

        $output->writeln("User created.");
    }
}
