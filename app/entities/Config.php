<?php
namespace Entities;
/**
 * @Entity
 */
class Config
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     * @var int 
     */
    protected $id;
    /**
     * @Column(type="string", length="40")
     * @var string
     */
    protected $cname;
    /**
     * @Column(type="text")
     * @var string
     */
    protected $cvalue;

    public function setName($name)
    {
        $this->cname = $name;
    }

    public function getName()
    {
        return $this->cname;
    }

    public function setValue($value)
    {
        $this->cvalue = $value;
    }

    public function getValue()
    {
        return $this->cvalue;
    }
}