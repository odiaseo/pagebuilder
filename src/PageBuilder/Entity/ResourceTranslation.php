<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="Resource_Translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="resource_lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class ResourceTranslation extends AbstractTranslation
{
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, options={"default"=""})
     */
    protected $content;

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
