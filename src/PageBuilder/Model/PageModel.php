<?php
namespace PageBuilder\Model;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;
use Zend\Navigation\Navigation;

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
        $entityManager = $this->getEntityManager();
        $query         = $entityManager
            ->createQueryBuilder()
            ->select('e')
            ->from($this->_entity, 'e')
            ->where('e.level > :level')
            ->setParameter(':level', $rootLevel - 1)
            ->orderBy('e.root, e.lft', 'ASC')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getRootMenu()
    {
        $qb = $this->_em->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where($qb->expr()->eq('e.level', ':level'))
            ->setMaxResults(1)
            ->setParameter('level', 0)
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getRootMenuById($pageId)
    {
        $qb = $this->_em->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where($qb->expr()->eq('e.id', ':id'))
            ->setMaxResults(1)
            ->setParameter('id', $pageId)
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function nestify($arrs, $depth_key = 'level')
    {
        $nested = array();
        $depths = array();

        foreach ($arrs as $key => $arr) {
            if ($arr[$depth_key] == 0) {
                $nested[$key]                 = $arr;
                $depths[$arr[$depth_key] + 1] = $key;
            } else {
                $parent =& $nested;
                for ($i = 1; $i <= ($arr[$depth_key]); $i++) {
                    $parent =& $parent[$depths[$i]];
                }

                $parent[$key]                 = $arr;
                $depths[$arr[$depth_key] + 1] = $key;
            }
        }

        return $nested;
    }

    public function getNavigationContainer($menus, $routeMatch)
    {
        $nestedMenus = $this->toHierarchy($menus, $routeMatch);

        return new Navigation($nestedMenus);
    }

    public function toHierarchy($collection, $childKey = 'pages')
    {
        // Trees mapped

        $trees = array();
        //$l = 0;
        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();
            foreach ($collection as $node) {
                $item            = $this->_buildPage($node);
                $item[$childKey] = array();
                // Number of stack items
                $l = count($stack);
                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) {
                    array_pop($stack);
                    $l--;
                }
                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i         = count($trees);
                    $trees[$i] = $item;
                    $stack[]   = & $trees[$i];
                } else {
                    // Add node to parent
                    $i                            = count($stack[$l - 1][$childKey]);
                    $stack[$l - 1][$childKey][$i] = $item;
                    $stack[]                      = & $stack[$l - 1][$childKey][$i];
                }
            }
        }

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

    protected function _buildPage($node, $hasIdentity = false)
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
        try {
            $path = array();
            $qb   = $this->_em->createQueryBuilder();
            $query
                  = $qb->select('e')
                ->from($this->_entity, 'e')
                ->where('e.slug = :slug')
                ->setParameter(':slug', $slug)
                ->setMaxResults(1)
                ->getQuery();

            $menu = $query->execute(array(), AbstractQuery::HYDRATE_OBJECT);

            if ($menu) {
                /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
                $repo = $this->_em->getRepository($this->_entity);
                $path = $repo->getPath($menu[0]);
            }
        } catch (NonUniqueResultException $ex) {

        } catch (QueryException $ex) {

        }

        return $path;
    }


    public function getDetails($pageId)
    {
        $page = $this->_em->getRepository($this->_entity)->find($pageId);

        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;

    }

    public function updateTemplateId($pageId, $templateId)
    {
        $page             = $this->_em->getRepository($this->_entity)->find($pageId);
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
        $qb = $this->_em->createQueryBuilder();

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
        $page = $this->_em->getRepository($this->_entity)->find($pageId);

        $details           = $page->toArray();
        $details['layout'] = is_object($page->template) ? $page->template->layout : '';

        return $details;

    }

    public function getActivePages()
    {
        $qb = $this->_em->createQueryBuilder();

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