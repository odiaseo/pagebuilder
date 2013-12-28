<?php
    namespace PageBuilder\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use PageBuilder\BaseEntity;

    /**
     * Component
     *
     * @ORM\Entity
     * @ORM\Table(name="Component")
     *
     */
    class Component extends BaseEntity

    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer");
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        /**
         * @ORM\Column(type="string")
         */
        protected $title;
        /**
         * @ORM\Column(type="string", nullable=true)
         */
        protected $description ;
        /**
         * @ORM\Column(type="text", nullable=false)
         */
        protected $content;
        /**
         * @Gedmo\Slug(fields={"title"})
         * @ORM\Column(type="string")
         */
        protected $cssId;
        /**
         * @ORM\Column(type="string", nullable=true)
         */
        protected $cssClass;
        /**
         * @ORM\ManyToOne(targetEntity="Site")
         * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
         */
        protected $siteId;
        /**
         * @var \datetime createdAt
         *
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(type="datetime", name="created_at")
         */
        protected $createdAt;
        /**
         * @var \datetime updatedAt
         *
         * @Gedmo\Timestampable(on="update")
         * @ORM\Column(type="datetime", name="updated_at")
         */
        protected $updatedAt;

        public function setContent($content)
        {
            $this->content = $content;
        }

        public function getContent()
        {
            return $this->content;
        }

        /**
         * @param \datetime $createdAt
         */
        public function setCreatedAt($createdAt)
        {
            $this->createdAt = $createdAt;
        }

        /**
         * @return \datetime
         */
        public function getCreatedAt()
        {
            return $this->createdAt;
        }

        public function setCssClass($cssClass)
        {
            $this->cssClass = $cssClass;
        }

        public function getCssClass()
        {
            return $this->cssClass;
        }

        public function setCssId($cssId)
        {
            $this->cssId = $cssId;
        }

        public function getCssId()
        {
            return $this->cssId;
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

        public function setSiteId($siteId)
        {
            $this->siteId = $siteId;
        }

        public function getSiteId()
        {
            return $this->siteId;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        /**
         * @param \datetime $updatedAt
         */
        public function setUpdatedAt($updatedAt)
        {
            $this->updatedAt = $updatedAt;
        }

        /**
         * @return \datetime
         */
        public function getUpdatedAt()
        {
            return $this->updatedAt;
        }


    }