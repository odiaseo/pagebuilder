<?php
namespace PageBuilder\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessenger;
use Zend\View\Helper\AbstractHelper;

class FlashMessages extends AbstractHelper {
	/**
	 * @var FlashMessenger
	 */
	protected $flashMessenger;

	/**
	 * @param FlashMessenger $flashMessenger
	 */
	public function setFlashMessenger( FlashMessenger $flashMessenger ) {
		$this->flashMessenger = $flashMessenger;
	}

	public function __invoke( $includeCurrentMessages = false ) {
		$messages = array(
			FlashMessenger::NAMESPACE_ERROR   => array(),
			FlashMessenger::NAMESPACE_SUCCESS => array(),
			FlashMessenger::NAMESPACE_INFO    => array(),
			FlashMessenger::NAMESPACE_DEFAULT => array()
		);

		foreach ( $messages as $ns => &$m ) {
			$m = $this->flashMessenger->getMessagesFromNamespace( $ns );
			if ( $includeCurrentMessages ) {
				$m = array_merge( $m, $this->flashMessenger->getCurrentMessagesFromNamespace( $ns ) );
				$this->flashMessenger->clearCurrentMessagesFromNamespace( $ns );
			}
		}

		return $messages;
	}
}
