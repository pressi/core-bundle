<?php


namespace IIDO\CoreBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;


class ThemeDesignerRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThemeDesignerEntity::class);
    }



//    /**
//     * @return ThemeDesignerEntity[]
//     */
//    public function findThemeDesignerFromPage(int $pageId): array
//    {
//        $entityManager = $this->getEntityManager();
//
//        $query = $entityManager->createQuery(
//            'SELECT p
//            FROM IIDO\CoreBundle\Entity\ThemeDesignerEntity p
//            WHERE p.page = :pageId'
//        )->setParameter('pageId', $pageId);
//
//        // returns an array of Product objects
//        return $query->getResult();
//    }
}
