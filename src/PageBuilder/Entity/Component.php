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
        protected $cssClass = '';
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


    }