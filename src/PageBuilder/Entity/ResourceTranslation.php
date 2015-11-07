<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="Resource_Translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ResourceTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="translations")
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
    public function __construct($locale = null, $field = null, $value = null)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }
}
