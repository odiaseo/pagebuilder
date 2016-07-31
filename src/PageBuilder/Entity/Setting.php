<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\BaseEntity as CommonEntity;

/**
 * Setting
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Setting extends CommonEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var \PageBuilder\Entity\SettingKey
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\SettingKey", fetch="LAZY")
     * @ORM\JoinColumn(name="setting_key_id", referencedColumnName="id", nullable=false)
     */
    protected $settingKey;
    /**
     * @ORM\Column(type="string", name="setting_value", length=512)
     */
    protected $settingValue = '';
    /**
     * @var Site
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $dataSource;

    public function setDataSource($site)
    {
        $this->dataSource = $site;
    }

    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSettingValue($value)
    {
        $this->settingValue = $value;
    }

    public function getSettingValue()
    {
        return $this->settingValue;
    }

    /**
     * @param $settingKey
     */
    public function setSettingKey($settingKey)
    {
        $this->settingKey = $settingKey;
    }

    /**
     * @return \PageBuilder\Entity\SettingKey
     */
    public function getSettingKey()
    {
        return $this->settingKey;
    }

}
