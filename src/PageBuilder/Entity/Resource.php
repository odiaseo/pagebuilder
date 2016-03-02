<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity as CommonEntity;

/**
 * Resource
 *
 * @ORM\Entity
 * @ORM\Table(name="Resource")
 * @Gedmo\TranslationEntity(class="PageBuilder\Entity\ResourceTranslation")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Resource extends CommonEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string")
     */
    protected $title;
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    protected $description;
    /**
     * @ORM\ManyToOne(targetEntity="ResourceType", cascade="persist")
     * @ORM\JoinColumn(name="resource_type_id", referencedColumnName="id", nullable=false)
     */
    protected $resourceType;
    /**
     * @ORM\Column(type="string")
     */
    protected $resourceUrl = '';
    /**
     * @ORM\Column(type="boolean", name="is_generic", nullable=true)
     */
    protected $isGeneric = 0;
    /**
     * @ORM\ManyToOne(targetEntity="Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $dataSource;
    /**
     * @ORM\OneToMany(
     *   targetEntity="ResourceTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param mixed $translations
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return Site
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    public function __toString()
    {
        $type = $this->getResourceType()->getSlug();

        switch ($type) {
            case 'video':
                $html = sprintf(
                    "<iframe src='%s' frameborder='0' allowfullscreen class='resource-video'></iframe>",
                    $this->getResourceUrl()
                );
                break;
            case 'link':
                $html = sprintf(
                    '<a href="%s" target="_blank" rel="nofollow" class="resource-link">'.$this->getTitle().
                    ' <i class="icon-mail-forward"></i></a>',
                    $this->getResourceUrl()
                );
                break;
            default:
                $html = '';
        }

        return $html;
    }

    public function setIsGeneric($isGeneric)
    {
        $this->isGeneric = $isGeneric;
    }

    public function getIsGeneric()
    {
        return $this->isGeneric;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
    }

    /**
     * @return ResourceType
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;
    }

    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addTranslation(ResourceTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }
}
