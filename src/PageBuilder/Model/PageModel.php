<?php
namespace PageBuilder\Model;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\QueryException;
use SynergyCommon\Model\NestedSetRepository;

/**
 * Class PageModel
 *
 * @method NestedSetRepository getRepository()
 * @package PageBuilder\Model
 */
class PageModel
    extends BaseModel
{
    /**
     * Returns the navigation menus
     *
     * @param int $rootLevel
     *
     * @return mixed
     */
    public function getTreeStructure($rootLevel = 1)
    {
        return $this->getRepository()->getTreeStructure($rootLevel);
    }

    public function getRootMenu()
    {
        return $this->getRepository()->getRootMenu();
    }

    public function getRootMenuById($pageId)
    {
        return $this->getRepository()->getRootMenuById($pageId);
    }

    public function nestify($arrs, $depthKey = 'level')
    {
        return $this->getRepository()->nestify($arrs, $depthKey);
    }

    public function getNavigationContainer($menus, $routeMatch)
    {
        return $this->getRepository()->getNavigationContainer($menus, $routeMatch);
    }

    public function toHierarchy($collection, $childKey = 'pages')
    {
        $trees    = $this->getRepository()->toHierarchy($collection, $childKey, array($this, 'buildPage'));
        $entities = $this->buildEntityTree();

        if ($entities) {
            array_push($trees, $entities);
        }

        return $trees;
    }

    protected function buildEntityTree()
    {
        return array();

    }

    public static function buildPage($node, $hasIdentity = false)
    {
        $params = array();
        if ($node['parameters']) {
            parse_str($node['parameters'], $params);
        }

        if (in_array($node['slug'], array('login', 'logout'))) {
            if ($node['slug'] == 'login') {
                $node['isVisible'] = !$hasIdentity;
            } elseif ($node['slug'] == 'logout') {
                $node['isVisible'] = $hasIdentity;
            }
        }

        $menu = array(
            'id'        => $node['id'],
            'title'     => $node['title'],
            'label'     => $node['title'],
            'route'     => $node['routeName'],
            'resource'  => self::getResourceString($node['routeName'], $node['slug']),
            'privilege' => $node['slug'],
            'visible'   => $node['isVisible'],
            'level'     => $node['level'],
            'icon'      => $node['iconClassName'],
            'params'    => $params
        );


        if (!empty($node['uri'])) {
            $menu['uri']    = $node['uri'];
            $menu['target'] = '_blank';
            unset($menu['route']);
        }

        return $menu;
    }

    /**
     * @param $slug
     *
     * @return array
     */
    public function getBreadcrumbPath($slug)
    {
        return $this->getRepository()->getBreadcrumbPath($slug);
    }


    public function getDetails($pageId)
    {
        $page = $this->getRepository()->find($pageId);

        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;

    }

    public function updateTemplateId($pageId, $templateId)
    {
        $page             = $this->getRepository()->find($pageId);
        $page->templateId = $templateId;
        $res              = $this->save($page);

        return $res;
    }

    public function getNavigation()
    {
        $navigation = $this->_sm->get('viewhelpermanager')->get('navigation');
        $menu       = $navigation('menu');

        return $menu;
    }

    public function listRootMenusByTitle()
    {
        $list  = array();
        $roots = $this->getRoots();
        foreach ($roots as $item) {
            $list[$item['id']] = array(
                'title'       => $item['title'],
                'description' => $item['description']
            );
        }

        return $list;
    }

    public function getRoots($mode = AbstractQuery::HYDRATE_ARRAY)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where($qb->expr()->eq('e.level', ':level'))
            ->setParameter('level', 0)
            ->getQuery();

        $result = $query->execute(null, $mode);


        return $result;
    }


    public function getPageDetails($pageId)
    {
        $page = $this->getRepository()->find($pageId);

        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;

    }

    public function getActivePages()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where('e.isVisible = 1')
            ->andWhere('e.parent NOT IN (12, 15)')
            ->andWhere('e.level > 0')
            ->getQuery();

        $result = $query->execute();


        return $result;
    }


    public static function getResourceString($routeName, $uniqueId)
    {
        return 'mvc:' . strtolower($routeName) . '.' . strtolower($uniqueId);
    }

    public function getPages()
    {
        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo  = $this->getRepository(); //  $em->getRepository($navService->getEntity());
        $menus = $repo->getNodesHierarchy();

        return $this->toHierarchy($menus);
    }

}