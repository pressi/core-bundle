<?php

namespace IIDO\CoreBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use IIDO\CoreBundle\Repository\CompanyRepository;


/**
 * ORM\Entity(repositoryClass=CompanyRepository::class)
 * ORM\Table(name="tl_iido_company")
 *
 * @deprecated
 */
class CompanyEntity
{
    /**
     * ORM\Id()
     * ORM\GeneratedValue()
     * ORM\Column(type="integer", length=10, options={"unsigned": true})
     */
    private $id;


    /**
     * ORM\Column(type="integer", length=10, options={"unsigned": true, "default": 0})
     */
    private $tstamp = 0;


    /**
     * ORM\Column(type="integer", length=10, options={"unsigned": true, "default": 0})
     */
    private $page = 0;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }



    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }



    /**
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }



    /**
     * @return int
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }



    /**
     * @param int $tstamp
     *
     * @return self
     */
    public function setTstamp(int $tstamp): self
    {
        $this->tstamp = $tstamp;
        return $this;
    }
}
