<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, User::class);
        $this->manager = $manager;
    }

    /**
     * @param User $user
     * @return User
     */
    public function saveUser(User $user): User
    {
        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

    public function loadUserByUsername(string $username)
    {
        $q = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf('Unable to find active admin identified by %s', $username);
            throw new UsernameNotFoundException($message, 0, $e);
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            $message = sprintf('Unsupported class type : %s', $class);
            throw new UnsupportedUserException($message);
        }

        return $this->find($user->getId());
    }

    public function supportsClass(string $class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

}
