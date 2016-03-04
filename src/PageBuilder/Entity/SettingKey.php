<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * SettingKey
 *
 * @ORM\Entity
 * @ORM\Table(name="Setting_Key")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class SettingKey extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=120, unique=true)
     */
    private $title;
    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(type="string")
     */
    private $code;
    /**
     * @ORM\Column(type="string", name="default_value", nullable=true)
     */
    private $defaultValue = '';
    /**
     * @ORM\Column(type="string", length=25, name="input_type")
     */
    private $inputType = 'text';
    /**
     * @ORM\Column(type="string", name="data_type", nullable=true)
     */
    private $dataType;
    /**
     * @ORM\Column(type="text", name="help_info")
     */
    private $helpInfo;

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setDataType($dataSource)
    {
        $this->dataType = $dataSource;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setHelpInfo($helpInfo)
    {
        $this->helpInfo = $helpInfo;
    }

    public function getHelpInfo()
    {
        return $this->helpInfo;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setInputType($inputType)
    {
        $this->inputType = $inputType;
    }

    public function getInputType()
    {
        return $this->inputType;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
