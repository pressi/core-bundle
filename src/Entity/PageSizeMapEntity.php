<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Entity\WebsiteFontEntity;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="tl_page_size_map")
 */
class PageSizeMapEntity extends AbstractGroupEntity
{
    /**
     * @ORM\OneToMany(targetEntity=WebsiteSizeEntity::class, mappedBy="parent", orphanRemoval=true)
     */
    protected $elements;
}
