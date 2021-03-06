<?php

namespace PageBuilder\Controller;

use Doctrine\ORM\EntityManager;
use PageBuilder\PageAwareInterface;
use PageBuilder\PageMetaData;
use SynergyCommon\Controller\BaseActionController as CommonActionController;
use SynergyCommon\SiteAwareInterface;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;

/**
 * Class BaseController
 *
 * @method Response getResponse()
 * @method Request   getRequest()
 * @method translate($text)
 * @method translator($text)
 * @package Application\Controller
 */
class BaseActionController extends CommonActionController implements SiteAwareInterface, PageAwareInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /** @var \PageBuilder\Entity\Site */
    protected $_site;

    /** @var  PageMetaData */
    protected $_pageMetadata;

    /** @var \Laminas\Log\Logger */
    protected $_log;

    /**
     * @param PageMetaData $meta
     *
     * @return mixed
     */
    public function setPageMetadata(PageMetaData $meta)
    {
        $this->_pageMetadata = $meta;
    }

    /**
     * @return PageMetaData
     */
    public function getPageMeta()
    {
        return $this->_pageMetadata;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return array|EntityManager|null|object
     */
    public function getEntityManager()
    {
        if (null === $this->_em) {
            // doctrine.entitymanager.orm_default
            $this->_em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->_em;
    }

    public function setSite($site = null)
    {
        $this->_site = $site;

        return $this;
    }

    /**
     * @return \PageBuilder\Entity\Site
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * @param \Laminas\Log\Logger $log
     *
     * @return $this
     */
    public function setLog($log)
    {
        $this->_log = $log;

        return $this;
    }

    /**
     * @return \Laminas\Log\Logger
     */
    public function getLog()
    {
        return $this->_log;
    }
}
