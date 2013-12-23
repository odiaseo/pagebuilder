<?php
    namespace PageBuilder\Entity\Join;

    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    use PageBuilder\BaseEntity;

    /**
     * SiteThemes Join table
     *
     * @ORM\Entity
     * @ORM\Table(name="Site_Theme")
     *
     */
    class SiteTheme extends BaseEntity
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer");
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        /**
         * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Theme", inversedBy="siteThemes")
         * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
         */
        protected $themeId;
        /**
         * @ORM\Column(type="boolean", name="is_active")
         */
        protected $isActive = 0;
        /**
         * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", inversedBy="siteThemes")
         * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
         */
        protected $siteId;

    }