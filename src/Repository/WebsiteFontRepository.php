<?php


namespace IIDO\CoreBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use IIDO\CoreBundle\Entity\PageFontMapEntity;
use IIDO\CoreBundle\Entity\WebsiteFontEntity;


class WebsiteFontRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteFontEntity::class);
    }



    public function findByPage(int $pageId, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $entityManager = $this->getEntityManager();
//        $query = $entityManager->createQuery('SELECT fontMap FROM IIDO\CoreBundle\Entity\PageFontMapEntity fontMap WHERE fontMap.sourceId = :pageId AND fontMap.sourceTable = :sourceTable')
//            ->setParameters(['pageId' => $pageId, 'sourceTable' => 'tl_page']);

//        $result = $query->getArrayResult();

//        $criteria = ['parent' => $result[0]['id']];
//        return parent::findBy($criteria, $orderBy, $limit, $offset);

        $query = $entityManager->createQueryBuilder()
            ->select('f')
            ->from(WebsiteFontEntity::class, 'f')
            ->join(PageFontMapEntity::class, 'fm')
            ->where('fm.sourceId = :pageId')
            ->andWhere('fm.sourceTable = :sourceTable')
            ->andWhere('f.parent = fm.id')
            ->setParameters( ['pageId' => $pageId, 'sourceTable' => 'tl_page'] );

//        $query = $entityManager->createQuery('
//        SELECT
//            f
//        FROM
//            IIDO\CoreBundle\Entity\WebsiteFontEntity f
//        INNER JOIN
//            IIDO\CoreBundle\Entity\PageFontMapEntity fm
//        WITH
//            fm.sourceId = :pageId
//            AND
//            fm.sourceTable = :sourceTable
//        WHERE
//            f.parent = fm.id')
//        ->setParameters(['pageId' => $pageId, 'sourceTable' => 'tl_page']);

//        $result = $query->getArrayResult();
//        $result = ;

//        echo "<pre>"; print_r( $result ); exit;
        return $query->getQuery()->getResult();
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
