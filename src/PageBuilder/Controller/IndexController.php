<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageBuilder\Controller;

use SynergyCommon\Controller\BaseActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 *
 * @package PageBuilder\Controller
 */
class IndexController extends BaseActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function adminAction()
    {
        $gridList = $listItems = [];

        /** @var $gridService \SynergyDataGrid\Service\GridService */
        $gridService = $this->getServiceLocator()->get('synergy\service\grid');

        $entityCacheFile = $gridService->getEntityCacheFile();
        $entities        = include "$entityCacheFile";
        ksort($entities);

        foreach ($entities as $item => $className) {
            if (strpos($item, '-') === false) {
                $gridList[$item] = $this->getServiceLocator()->get('jqgrid')->setGridIdentity(
                    $className, $item, null, false
                );
                $listItems[]     = $item;
            }
        }

        $return = [
            'entities' => $listItems,
            'grids'    => $gridList,
        ];

        return new ViewModel($return);
    }
}
