<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Component
 *
 * @ORM\Entity
 * @ORM\Table(name="Component")
 * @Gedmo\TranslationEntity(class="PageBuilder\Entity\ComponentTranslation")
 */
class Component extends AbstractEntity {
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
	 * @Gedmo\Translatable
	 * @ORM\Column(type="text", nullable=false)
	 */
	protected $content;
	/**
	 * @Gedmo\Slug(fields={"title"})
	 * @ORM\Column(type="string", name="cssid")
	 */
	protected $cssId;
	/**
	 * @ORM\Column(type="string", nullable=true, name="css_class")
	 */
	protected $cssClass;
	/**
	 * @ORM\OneToMany(
	 *   targetEntity="ComponentTranslation",
	 *   mappedBy="object",
	 *   cascade={"persist", "remove"}
	 * )
	 */
	protected $translations;

	public function __construct() {
		$this->translations = new ArrayCollection();
	}

	public function setContent( $content ) {
		$this->content = $content;
	}

	public function getContent() {
		return $this->content;
	}

	public function setCssClass( $cssClass ) {
		$this->cssClass = $cssClass;
	}

	public function getCssClass() {
		return $this->cssClass;
	}

	public function setCssId( $cssId ) {
		$this->cssId = $cssId;
	}

	public function getCssId() {
		return $this->cssId;
	}

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function addTranslation( ComponentTranslation $t ) {
		if ( ! $this->translations->contains( $t ) ) {
			$this->translations[] = $t;
			$t->setObject( $this );
		}
	}
}
