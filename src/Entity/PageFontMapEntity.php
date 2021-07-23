<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Entity\WebsiteFontEntity;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="tl_page_font_map")
 */
class PageFontMapEntity extends AbstractGroupEntity
{
    /**
     * @ORM\OneToMany(targetEntity=WebsiteFontEntity::class, mappedBy="parent", orphanRemoval=true)
     */
    protected $elements;
}
