<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseLicence;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Licence")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Licence
    extends BaseLicence
{

}
