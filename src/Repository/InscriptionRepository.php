<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }
    public function rechInscriptionsEmploye($nom, $prenom): array
    {
        return $this->createQueryBuilder('i')
        ->join('i.employe','e')
        ->andWhere('e.nom= :val1')
        ->andWhere('e.prenom= :val2')
        ->setParameter('val1', $nom)
        ->setParameter('val2', $prenom)
        ->getQuery()
        ->getResult();
    }

    public function rechInscriptionsStatutNone(): array
    {
        return $this->createQueryBuilder('i')
        ->join('i.employe','e')
        ->andWhere('e.statut = :val1')
        ->setParameter('val1', 0)
        ->getQuery()
        ->getResult();
    }

    // public function InscriptionsStatutValider($id): array
    // {
    //     return $this->createQueryBuilder('i')
    //     $entityManager = $this->getEntityManager();
    //     $queryBuilder
    //     ->join('i.employe','e')
    //     ->andWhere('e.id= :val1')
    //     ->andWhere('e.statut = validé')
    //     ->setParameter('val1', $id)
    //     ->persist();
    //     ->flush();
    // }

    public function updateInscriptionStatutValide($id): void
{
    $entityManager = $this->getEntityManager();
    $queryBuilder = $entityManager->createQueryBuilder();

    $queryBuilder->update('App\Entity\Inscription', 'i')
        ->set('i.statut', ':nouveauStatut')
        ->where('i.id = :id')
        ->setParameter('nouveauStatut', 'Validé')
        ->setParameter('id', $id)
        ->getQuery()
        ->execute();
}



//    /**
//     * @return Inscription[] Returns an array of Inscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Inscription
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
