<?php
namespace PageBuilder\Entity\Join;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;


/**
 * Page Themes Join table
 *
 * @ORM\Entity
 * @ORM\Table(name="Page_Theme", uniqueConstraints={@ORM\UniqueConstraint(name="page_theme_idx", columns={"page_id", "theme_id"})})
 *
 */
class PageTheme
	extends AbstractEntity {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	/**
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Theme", inversedBy="pageThemes")
	 * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
	 */
	protected $themeId;
	/**
	 * @ORM\Column(type="boolean", name="is_active")
	 */
	protected $isActive = 0;
	/**
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", inversedBy="pageThemes")
	 * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
	 */
	protected $pageId;
	/**
	 * @ORM\Column(type="json_array", nullable=true)
	 */
	protected $layout;

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setIsActive( $isActive ) {
		$this->isActive = $isActive;
	}

	public function getIsActive() {
		return $this->isActive;
	}

	public function setLayout( $layout ) {
		$this->layout = $layout;
	}

	public function getLayout() {
		return $this->layout;
	}

	public function setPageId( $pageId ) {
		$this->pageId = $pageId;
	}

	/**
	 * @return \PageBuilder\Entity\Page
	 */
	public function getPageId() {
		return $this->pageId;
	}

	public function setThemeId( $themeId ) {
		$this->themeId = $themeId;
	}

	/**
	 * @return \PageBuilder\Entity\Theme mixed
	 */
	public function getThemeId() {
		return $this->themeId;
	}

}
