<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Entity\WebsiteColorEntity;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="tl_page_color_map")
 */
class PageColorMapEntity extends AbstractGroupEntity
{
    /**
     * @ORM\OneToMany(targetEntity=WebsiteColorEntity::class, mappedBy="parent", orphanRemoval=true)
     */
    protected $elements;
}
