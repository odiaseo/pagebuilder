<?php
namespace PageBuilder\Entity\Join;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PageBuilder\Entity\Site;
use PageBuilder\Entity\Template;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Page Template Join table
 *
 * @ORM\Entity
 * @ORM\Table(name="Page_Template",
 * uniqueConstraints={@ORM\UniqueConstraint(name="page_template_idx",
 * columns={"page_id", "template_id", "is_active", "site_id"})})
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class PageTemplate extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template", inversedBy="templatePages")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     */
    protected $template;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", inversedBy="pageTemplates")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     */
    protected $page;

    /**
     * @var Site
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $site;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = 0;

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    public function setId($itemId)
    {
        $this->id = $itemId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setPage($pageId)
    {
        $this->page = $pageId;
    }

    /**
     * @return \PageBuilder\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed Template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
