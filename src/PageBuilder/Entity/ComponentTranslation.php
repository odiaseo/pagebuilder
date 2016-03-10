<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="Component_Translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="component_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class ComponentTranslation extends AbstractTranslation
{
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="Component", inversedBy="translations")
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
