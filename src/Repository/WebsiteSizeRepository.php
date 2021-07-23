<?php


namespace IIDO\CoreBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use IIDO\CoreBundle\Entity\PageSizeMapEntity;
use IIDO\CoreBundle\Entity\WebsiteSizeEntity;


class WebsiteSizeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteSizeEntity::class);
    }



    public function findByPage(int $pageId, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('f')
            ->from(WebsiteSizeEntity::class, 'f')
            ->join(PageSizeMapEntity::class, 'fm')
            ->where('fm.sourceId = :pageId')
            ->andWhere('fm.sourceTable = :sourceTable')
            ->andWhere('f.parent = fm.id')
            ->setParameters( ['pageId' => $pageId, 'sourceTable' => 'tl_page'] );

        return $query->getQuery()->getResult();
    }
}
