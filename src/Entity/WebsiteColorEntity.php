<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Repository\WebsiteColorRepository;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupElementEntity;
use IIDO\CoreBundle\Entity\PageColorMapEntity;


/**
 * @ORM\Entity(repositoryClass=WebsiteColorRepository::class)
 * @ORM\Table(name="tl_website_color")
 */
class WebsiteColorEntity extends AbstractGroupElementEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=PageColorMapEntity::class, inversedBy="colors")
     * @ORM\JoinColumn(name="parent", nullable=false)
     */
    protected $parent;


    /**
     * @ORM\Column(type="string", length=64)
     */
    private string $color = '';


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $label = '';


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $className = '';



    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }



    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }



    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }



    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }



    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }



    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }
}
