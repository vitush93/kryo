<?php

namespace App\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Setting
 * @package App\Model
 *
 * @Entity()
 * @Table(name="settings")
 */
class Setting
{
    /**
     * @Id()
     * @GeneratedValue()
     * @Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     *
     * @var string
     */
    private $setting_key;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    private $setting_value;

    /**
     * Setting constructor.
     * @param string $key
     * @param string $value
     */
    function __construct($key, $value)
    {
        $this->setting_key = $key;
        $this->setting_value = $value;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSettingKey()
    {
        return $this->setting_key;
    }

    /**
     * @return string
     */
    public function getSettingValue()
    {
        return $this->setting_value;
    }

    /**
     * @param string $setting_value
     */
    public function setSettingValue($setting_value)
    {
        $this->setting_value = $setting_value;
    }
}