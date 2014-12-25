<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="Page_Translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class PageTranslation extends AbstractPersonalTranslation {
	/**
	 * @ORM\ManyToOne(targetEntity="Page", inversedBy="translations")
	 * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $object;

	/**
	 * Convenient constructor
	 *
	 * @param string $locale
	 * @param string $field
	 * @param string $value
	 */
	public function __construct( $locale = null, $field = null, $value = null ) {
		$this->setLocale( $locale );
		$this->setField( $field );
		$this->setContent( $value );
	}
}
