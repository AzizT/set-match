<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

// fonction pour la barre de recherche https://bibliocode.fr/biblio/10
    public function search($searchEngine) {
        
        return $this->createQueryBuilder('User')
            ->andWhere('User.prenom LIKE :searchEngine')
            ->orWhere('User.nom LIKE :searchEngine')
            ->orWhere('User.category LIKE :searchEngine')
            ->orWhere('User.budget LIKE :searchEngine')
            ->leftJoin('User.langues', 'l')
            ->orWhere('l.nom LIKE :searchLangueEngine')
            ->leftJoin('User.specialites', 's')
            ->orWhere('s.titre LIKE :searchSpecialiteEngine')
            ->setParameter('searchEngine', '%'.$searchEngine.'%')
            ->setParameter('searchLangueEngine', $searchEngine )
            ->setParameter('searchSpecialiteEngine', $searchEngine )
            ->getQuery()
            ->execute();
    }

    public function searchDate($searchDateEngine) {
        
        return $this->createQueryBuilder('User')
            ->leftJoin('User.plannings', 'p')
            ->orWhere('p.dateDebut <= :searchDateEngine ')
            ->orWhere('p.dateFin >= :searchDateEngine ')
            ->setParameter('searchDateEngine',$searchDateEngine)
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
