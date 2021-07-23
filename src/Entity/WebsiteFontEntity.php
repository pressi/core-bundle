<?php

namespace IIDO\CoreBundle\Entity;


use Contao\StringUtil;
use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Repository\WebsiteFontRepository;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupElementEntity;
use IIDO\CoreBundle\Entity\PageFontMapEntity;


/**
 * @ORM\Entity(repositoryClass=WebsiteFontRepository::class)
 * @ORM\Table(name="tl_website_font")
 */
class WebsiteFontEntity extends AbstractGroupElementEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=PageFontMapEntity::class, inversedBy="fonts")
     * @ORM\JoinColumn(name="parent", nullable=false)
     */
    protected $parent;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $label = '';


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isDefault = false;



    /**
     * @return string
     */
    public function getLabel(): string
    {
        $this->label = str_replace(['&#40;', '&#41;'], ['(', ')'], $this->label);
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
    public function getName(): string
    {
        $this->name = str_replace(['&#40;', '&#41;'], ['(', ')'], $this->name);
        return $this->name;
    }



    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }



    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }



    /**
     * @param bool $isDefault
     */
    public function setDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
