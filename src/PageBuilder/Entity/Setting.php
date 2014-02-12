<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 *
 */
class Setting
    extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var \PageBuilder\Entity\SettingKey
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\SettingKey")
     * @ORM\JoinColumn(name="setting_key_id", referencedColumnName="id", nullable=false)
     */
    protected $settingKey;
    /**
     * @ORM\Column(type="text", name="setting_value")
     */
    protected $value = '';

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $settingKey
     */
    public function setSettingKey($settingKey)
    {
        $this->settingKey = $settingKey;
    }

    /**
     * @return \SynergyCommon\Admin\Entity\SettingKey
     */
    public function getSettingKey()
    {
        return $this->settingKey;
    }


}