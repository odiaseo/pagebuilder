<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use SynergyCommon\Model\TranslatableModelTrait;
use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class PageModel
 * @method PageRepository getRepository()
 *
 * @package PageBuilder\Model
 */
class PageModel extends BaseModel
{
    use LocaleAwareTrait;
    use TranslatableModelTrait;

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
        $trees    = $this->getRepository()->toHierarchy($collection, $childKey, [$this, 'buildPage']);
        $entities = $this->buildEntityTree();

        if ($entities) {
            array_push($trees, $entities);
        }

        return $trees;
    }

    protected function buildEntityTree()
    {
        return [];
    }

    public static function buildPage($node, $hasIdentity = false)
    {
        $params = [];
        if ($node['parameters']) {
            parse_str($node['parameters'], $params);
        }

        if (in_array($node['privilege'], ['login', 'logout'])) {
            if ($node['privilege'] == 'login') {
                $node['isVisible'] = !$hasIdentity;
            } elseif ($node['privilege'] == 'logout') {
                $node['isVisible'] = $hasIdentity;
            }
        }

        $menu = [
            'id'        => $node['id'],
            'title'     => $node['title'],
            'label'     => empty($node['label']) ? $node['title'] : $node['label'],
            'route'     => $node['routeName'],
            'resource'  => self::getResourceString($node['routeName'], $node['slug']),
            'privilege' => $node['privilege'],
            'visible'   => $node['isVisible'],
            'level'     => $node['level'],
            'i18n'      => $node['slug'],
            'icon'      => $node['iconClassName'],
            'params'    => $params,
        ];

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
        $page              = $this->findObject($pageId);
        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;
    }

    public function updateTemplateId($pageId, $templateId)
    {
        $page             = $this->findObject($pageId);
        $page->templateId = $templateId;
        $res              = $this->save($page);

        return $res;
    }

    public function getNavigation()
    {
        $navigation = $this->getServiceLocator()->get('ViewHelperManager')->get('navigation');
        $menu       = $navigation('site\pages');

        return $menu;
    }

    public function listRootMenusByTitle()
    {
        $list  = [];
        $roots = $this->getRoots();
        foreach ($roots as $item) {
            $list[$item['id']] = [
                'title'       => $item['title'],
                'description' => $item['description'],
            ];
        }

        return $list;
    }

    public function getRoots($mode = AbstractQuery::HYDRATE_ARRAY)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from($this->getEntity(), 'e')
            ->where($qb->expr()->eq('e.level', ':level'))
            ->setParameter('level', 0)
            ->getQuery();

        $result = $query->execute(null, $mode);

        return $result;
    }

    public function getPageDetails($pageId)
    {
        $page = $this->findObject($pageId);

        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;
    }

    /**
     * @param int $mode
     *
     * @return array
     */
    public function getActivePages($mode = AbstractQuery::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e')
            ->from($this->getEntity(), 'e')
            ->where('e.isVisible = 1')
            ->andWhere('e.parent NOT IN (12, 15)')
            ->andWhere('e.level > 0')
            ->getQuery();

        $result = $query->getResult($mode);

        return $result;
    }

    /**
     * @param     $id
     * @param int $mode
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMainPageById($id, $mode = AbstractQuery::HYDRATE_ARRAY)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e.id, t.layout', 'p.id as parentId', 't.id as templateId')
            ->from($this->getEntity(), 'e')
            ->leftJoin('e.template', 't')
            ->leftJoin('e.parent', 'p')
            ->where('e.id = :id')
            ->setParameters(
                [
                    ':id' => $id,
                ]
            );

        $qb->setEnableHydrationCache($this->enableResultCache);
        $result = $qb->getQuery()->getOneOrNullResult($mode);

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
        $query = $repo->getNodesHierarchyQuery();
        $query = $this->setCacheFlag($query);
        $menus = $query->getArrayResult();

        return $this->toHierarchy($menus);
    }

    /**
     * @param null $rootPage
     * @param int $mode
     * @param null $cacheKey
     *
     * @return mixed
     */
    public function getEntityNavigation($rootPage = null, $mode = AbstractQuery::HYDRATE_ARRAY, $cacheKey = null)
    {
        /** @var $repo  PageRepository */
        $this->setCacheKey($cacheKey);
        $repo  = $this->getRepository();
        $query = $this->addHints($repo->getNodesHierarchyQuery($rootPage));
        $query = $this->setCacheFlag($query);

        return $query->execute([], $mode);
    }
}
