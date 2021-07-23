<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Repository\WebsiteSizeRepository;
use Mvo\ContaoGroupWidget\Entity\AbstractGroupElementEntity;


/**
 * @ORM\Entity(repositoryClass=WebsiteSizeRepository::class)
 * @ORM\Table(name="tl_website_size")
 */
class WebsiteSizeEntity extends AbstractGroupElementEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=PageSizeMapEntity::class, inversedBy="sizes")
     * @ORM\JoinColumn(name="parent", nullable=false)
     */
    protected $parent;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $size = '';


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $inHeadline = true;


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $inText = true;


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isDefault = false;



    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }



    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }



    /**
     * @return bool
     */
    public function isInHeadline(): bool
    {
        return $this->inHeadline;
    }



    /**
     * @param bool $inHeadline
     */
    public function setInHeadline(bool $inHeadline): void
    {
        $this->inHeadline = $inHeadline;
    }



    /**
     * @return bool
     */
    public function isInText(): bool
    {
        return $this->inText;
    }



    /**
     * @param bool $inText
     */
    public function setInText(bool $inText): void
    {
        $this->inText = $inText;
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
