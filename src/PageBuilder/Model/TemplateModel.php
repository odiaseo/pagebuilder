<?php
namespace PageBuilder\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use PageBuilder\Entity\Join\TemplateSection;

class TemplateModel extends BaseModel
{
    public function getActiveSections($id)
    {
        $qb = $this->_em->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from('PageBuilder\Entity\Join\TemplateSection', 'e')
            ->where('e.templateId = :id')
            ->andWhere('e.isActive = 1')
            ->setParameter(':id', $id)
            ->getQuery();

        $result = $query->execute(array(), AbstractQuery::HYDRATE_OBJECT);

        return $result;

    }

    /**
     * @param $id
     * @param $sections
     *
     * @return mixed
     */
    public function updateTemplateSections($id, $sections)
    {
        /** @var $entity \PageBuilder\Entity\Template */
        $entity = $this->findObject($id);

        /** @var $ts \PageBuilder\Entity\Join\TemplateSection */
        foreach ($entity->getTemplateSections() as $ts) {
            $ts->setIsActive(0);
        }
        if ($ts = $entity->getTemplateSections()) {
            $entity->getTemplateSections()->clear();
        } else {
            $entity->setTemplateSections(new ArrayCollection());

        }

        foreach ($sections as $order => $v) {
            if ($entry = $this->_em->getRepository('PageBuilder\Entity\Join\TemplateSection')
                ->findOneBy(array('templateId' => $id, 'sectionId' => $v))
            ) {
                $entry->setSortOrder($order);
                $entry->setIsActive(1);
            } elseif ($section = $this->_em->getRepository('PageBuilder\Entity\Section')->find($v)) {
                $entry = new TemplateSection();
                $entry->setSectionId($section);
                $entry->setTemplateId($entity);
                $entry->setSortOrder($order);
                $entry->setIsActive(1);
            }
            $entity->getTemplateSections()->add($entry);
        }

        return $this->save($entity);
    }
}