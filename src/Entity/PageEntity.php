<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Entity\WebsiteColorEntity;


/**
 * ORM\Entity()
 * ORM\Table(name="tl_page")
 *
 * @deprecated
 */
class PageEntity
{
    /**
     * ORM\Column(name="id", type="integer", options={"unsigned": true})
     * ORM\Id
     * ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;



    /**
     * ORM\OneToMany(targetEntity=WebsiteColorEntity, mappedBy="parent", orphanRemoval=true)
     */
    private Collection $colors;



    public function __construct()
    {
        $this->colors = new ArrayCollection();
    }



    /**
     * @return Collection<int, WebsiteColorEntity>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }



    public function addColor( WebsiteColorEntity $color ): self
    {
        if( !$this->colors->contains( $color ) )
        {
            $this->colors[] = $color;
            $color->setParent( $this );
        }

        return $this;
    }



    public function removeColor( WebsiteColorEntity $color ): self
    {
        if( $this->colors->removeElement( $color ) )
        {
            if( $color->getParent() === $this )
            {
                $color->setParent(null);
            }
        }

        return $this;
    }
}
