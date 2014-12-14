<?php
namespace PageBuilder\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use PageBuilder\Entity\Join\TemplateSection;
use PageBuilder\Entity\Template;

class TemplateModel extends BaseModel
{
    public function getActiveSections($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
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
            if ($entry = $this->getEntityManager()->getRepository('PageBuilder\Entity\Join\TemplateSection')
                ->findOneBy(array('templateId' => $id, 'sectionId' => $v))
            ) {
                $entry->setSortOrder($order);
                $entry->setIsActive(1);
            } elseif ($section = $this->getEntityManager()->getRepository('PageBuilder\Entity\Section')->find($v)) {
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

    public function listTemplates()
    {

        $list = array();
        $qb   = $this->getEntityManager()->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e.id, e.title, e.description, e.layout')
            ->from($this->_entity, 'e')
            ->getQuery();

        $result = $query->execute(array(), AbstractQuery::HYDRATE_ARRAY);

        if ($result) {
            foreach ($result as $item) {
                $list[$item['id']] = array(
                    'title'       => $item['title'],
                    'description' => $item['description'],
                    'layout'      => $item['layout']
                );
            }

        }

        return $list;

    }

    public function updateTemplate($id, $layout, $title = '')
    {
        $template = $this->getEntityManager()
            ->getRepository($this->getEntity())->find($id);

        if (!$template) {
            $template = new Template();
            $template->setTitle($title);
        }

        $template->setLayout($layout);

        return $this->save($template);
    }

}