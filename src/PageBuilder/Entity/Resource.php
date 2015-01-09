<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity as CommonEntity;

/**
 * Resource
 *
 * @ORM\Entity
 * @ORM\Table(name="Resource")
 *
 */
class Resource extends CommonEntity {
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

	public function setDataSource( $dataSource ) {
		$this->dataSource = $dataSource;
	}

	/**
	 * @return Site
	 */
	public function getDataSource() {
		return $this->dataSource;
	}


	public function __toString() {
		$type = $this->getResourceType()->getSlug();

		switch ( $type ) {
			case 'video':
				$html = sprintf( "<iframe src='%s' frameborder='0' allowfullscreen class='resource-video'></iframe>",
					$this->getResourceUrl() );
				break;
			case 'link':
				$html = sprintf( '<a href="%s" target="_blank" rel="nofollow" class="resource-link">Click Here</a>',
					$this->getResourceUrl() );
				break;
			default:
				$html = '';
		}

		return $html;
	}

	public function setIsGeneric( $isGeneric ) {
		$this->isGeneric = $isGeneric;
	}

	public function getIsGeneric() {
		return $this->isGeneric;
	}

	public function setDescription( $description ) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}


	public function setResourceType( $resourceType ) {
		$this->resourceType = $resourceType;
	}

	/**
	 * @return ResourceType
	 */
	public function getResourceType() {
		return $this->resourceType;
	}

	public function setResourceUrl( $resourceUrl ) {
		$this->resourceUrl = $resourceUrl;
	}

	public function getResourceUrl() {
		return $this->resourceUrl;
	}


	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}
}
