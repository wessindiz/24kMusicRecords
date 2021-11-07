<?php

namespace App\Repository;

use App\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Calendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendar[]    findAll()
 * @method Calendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    // /**
    //  * @return Calendar[] Returns an array of Calendar objects
    //  */
    
    public function findByStart()
    {
       
       return $this->createQueryBuilder('c')->select('c.start')
            //->andWhere('c.start = :start')
            //->andWhere('c.end = :end')
            //->andWhere('c.categorie = :categorie')

          //  ->setParameter('start', $start)
          //  ->setParameter('end', $end)
          //  ->setParameter('categorie', $categorie)

            ->getQuery()

            ->getResult();
    }


    public function findByEnd()
    {
       
       return $this->createQueryBuilder('c')->select('c.end',)
            //->andWhere('c.start = :start')
            //->andWhere('c.end = :end')
            //->andWhere('c.categorie = :categorie')

          //  ->setParameter('start', $start)
          //  ->setParameter('end', $end)
          //  ->setParameter('categorie', $categorie)

            ->getQuery()

            ->getResult();
    }

   /* public function findByCategorie()
    {
       
       return $this->createQueryBuilder('c')->select('categorie')
            //->andWhere('c.start = :start')
            //->andWhere('c.end = :end')
            //->andWhere('c.categorie = :categorie')

          //  ->setParameter('start', $start)
          //  ->setParameter('end', $end)
          //  ->setParameter('categorie', $categorie)

            ->getQuery()

            ->getResult('c.categorie');
    }


  // /**
    //  * @return Calendar[] Returns an array of Calendar objects
    // 
    
  //  public function findBy($value)
    {
        
        $db = $this->getEntityManager()->getConnection();
            $req = '
                SELECT start, end, categorie FROM calendar
                ';
    } */
    
    

    /*
    public function findOneBySomeField($value): ?Calendar
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

