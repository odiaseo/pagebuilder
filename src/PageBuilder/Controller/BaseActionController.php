<?php

namespace PageBuilder\Controller;

use Doctrine\ORM\EntityManager;
use PageBuilder\PageAwareInterface;
use PageBuilder\PageMetaData;
use SynergyCommon\SiteAwareInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class BaseController
 *
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 * @method \Zend\Http\PhpEnvironment\Request   getRequest()
 * @method translate()
 * @method translator()
 * @package Application\Controller
 */
class BaseActionController
	extends AbstractActionController
	implements SiteAwareInterface, PageAwareInterface {
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $_em = null;

	/** @var \PageBuilder\Entity\Site */
	protected $_site;

	/** @var  PageMetaData */
	protected $_pageMetadata;

	/** @var \Zend\Log\Logger */
	protected $_log;

	/**
	 * @param PageMetaData $meta
	 *
	 * @return mixed
	 */
	public function setPageMetadata( PageMetaData $meta ) {
		$this->_pageMetadata = $meta;
	}

	/**
	 * @return PageMetaData
	 */
	public function getPageMeta() {
		return $this->_pageMetadata;
	}

	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager( EntityManager $em ) {
		$this->_em = $em;
	}

	/**
	 * @return array|EntityManager|null|object
	 */
	public function getEntityManager() {
		if ( null === $this->_em ) {
			// doctrine.entitymanager.orm_default
			$this->_em = $this->getServiceLocator()->get( 'doctrine.entitymanager.orm_default' );
		}

		return $this->_em;
	}

	public function setSite( $site = null ) {
		$this->_site = $site;

		return $this;
	}


	/**
	 * @return \PageBuilder\Entity\Site
	 */
	public function getSite() {
		return $this->_site;
	}

	/**
	 * @param \Zend\Log\Logger $log
	 *
	 * @return $this
	 */
	public function setLog( $log ) {
		$this->_log = $log;

		return $this;
	}

	/**
	 * @return \Zend\Log\Logger
	 */
	public function getLog() {
		return $this->_log;
	}

}
