<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageBuilder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $_entities
        = array(
            'page',
            'section',
            'template',
            'component',
            'theme',
            'site'
        );

    public function indexAction()
    {
        $gridList = array();

        /** @var $grid \SynergyDataGrid\Grid\GridType\BaseGrid */
        $grid = $this->getServiceLocator()->get('jqgrid');

        foreach ($this->_entities as $item) {
            $className = $return[$item . 'Grid'] = $grid->getClassnameFromEntityKey($item);
            $gridList[$item] = $this->getServiceLocator()->get('jqgrid')->setGridIdentity(
                $className, $item, null, false
            );
        }

        $return = array(
            'entities' => $this->_entities,
            'grids'    => $gridList
        );

        return new ViewModel($return);
    }
}
