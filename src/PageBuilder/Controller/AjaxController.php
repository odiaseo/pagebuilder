<?php

namespace PageBuilder\Controller;

use Application\Adapter\QueryFilter;
use Application\Entity\Comment;
use Application\Entity\MerchantRating;
use Application\Form\CommentForm;
use Application\Service\MerchantService;
use Application\View\Helper\OfferHelper;
use Solarium\Core\Query\Result\ResultInterface;
use Zend\Filter\HtmlEntities;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Mvc\Exception\RuntimeException;
use Zend\View\Model\JsonModel;
use Application\Util\String;

class AjaxController extends BaseController
{

    public function editableDataSourceAction()
    {
        return new JsonModel();
    }

    public function autoCompleteAction()
    {
        try {
            $return  = $output = array();
            $section = array(
                'no-result' => 'No result found'
            );

            $keyword       = $this->params()->fromPost('term');
            $filter        = new QueryFilter();
            $arrayTerms    = String::prepareSearchString($keyword, $filter);
            $filteredTerms = implode(' ', $arrayTerms);

            /** @var $client \Solarium\Core\Client\Client */
            $client = $this->getServiceLocator()->get('solarium');

            /** @var $parallel \Solarium\Plugin\ParallelExecution\ParallelExecution */
            $parallel = $client->getPlugin('parallelexecution');

            /** @var $query \Solarium\QueryType\Select\Query\Query */
            $query      = $client->createSelect();
            $searchTerm = $query->getHelper()->assemble("title:%p1%^2 %1% %1%~0.3", array($filteredTerms));

            $query->setQuery($searchTerm);
            $query->setFields('id,title,slug,discount,merchant');
            $query->createFilterQuery('expired')->setQuery('-isExpired:true');

            //merchant specific query
            $merchantQuery = $client->createSelect()->setQuery($searchTerm);
            $merchantQuery->setFields('title, slug, logo');
            $merchantQuery->setRows(6);

            //category
            $catQuery = $client->createSelect()->setQuery($searchTerm);
            $catQuery->setFields('title,slug');
            $catQuery->setRows(6);

            $parallel->addQuery('merchants', $merchantQuery, 'merchant');
            $parallel->addQuery('categories', $catQuery, 'category');
            $parallel->addQuery('offers', $query, 'offer');

            $results = $parallel->execute();
            $manager = $this->getServiceLocator()->get('viewhelpermanager');
            /** @var $offerHelper \Application\View\Helper\OfferHelper */
            $offerHelper = $manager->get('offerHelper');
            /** @var $imageHelper \Application\View\Helper\ImageHelper */
            $imageHelper = $manager->get('imageHelper');
            $urlHelper   = $manager->get('url');
            $filter      = new HtmlEntities();


            foreach ($results as $type => $data) {
                $return         = array();
                $section[$type] = $type == 'offers' ? 'Top 10 matching offers' : $type;
                if ($data instanceof  ResultInterface) {
                    foreach ($data as $doc) {
                        $obj              = new \stdClass();
                        $obj->title       = $doc->title;
                        $obj->logo        = '';
                        $obj->description = '';
                        $obj->type        = $type;
                        switch ($type) {
                            case 'categories':
                                $obj->url = $urlHelper(
                                    'affiliate/default',
                                    array('title' => $doc->slug, 'controller' => 'category', 'title' => $doc->slug)
                                );
                                break;
                            case 'merchants':
                                $obj->logo = $imageHelper->merchantLogo($doc->logo);
                                $obj->url  = $urlHelper('merchant/default', array('slug' => $doc->slug));
                                break;
                            case 'offers':
                                $obj->merchant = $doc->merchant;
                                $obj->discount = $doc->discount ? $filter->filter($doc->discount) : '';
                                $obj->outLink  = $offerHelper->outLink(
                                    $doc->id, $doc->slug, null, OfferHelper::PAGE_TYPE_AUTO_COMPLETE
                                );
                                $obj->url      = $urlHelper(
                                    'product_detail',
                                    array('hexId' => $offerHelper->encodeOfferId($doc->id), 'title' => $doc->slug)
                                );
                                break;
                        }
                        $return[] = $obj;
                    }
                }

                $output[] = $return;


            }
        } catch (\Exception $e) {
            $this->getServiceLocator()->get('logger')->logException($e);
        }

        //Amazon product advertising API

        return new JsonModel(
            array(
                 'data'     => $output,
                 'sections' => $section

            )
        );
    }

    public function addRatingAction()
    {
        $error   = true;
        $message = 'Invalid Rating';

        if ($this->getRequest()->isPost()) {
            $ipAddress  = $this->getRequest()->getServer('REMOTE_ADDR');
            $merchantId = $this->params()->fromPost('merchantId');
            $rating     = $this->params()->fromPost('rating');

            $merchantRating = new MerchantRating();
            $merchantRating->setMerchantId($merchantId);
            $merchantRating->setTitle($ipAddress);
            $merchantRating->setRating($rating);

            try {
                $service = $this->getServiceLocator()->get('merchant-ratings_service');

                $merchantRating = $service->save($merchantRating);
                $message        = 'You rated it ' . $merchantRating->getRating();
                $error          = false;
            } catch (RuntimeException $e) {
                $message = 'An Error Occurred';
                $error   = true;
            }
        }

        return new JsonModel(
            array(
                 'error'   => $error,
                 'message' => $message
            )
        );
    }

    public function addCommentAction()
    {
        $error   = true;
        $message = 'Invalid Comment';
        $url     = $this->getRequest()->getServer('HTTP_REFERER');

        if ($this->getRequest()->isPost()) {
            $commentForm = new CommentForm();
            $comment     = new Comment();
            $commentForm->setInputFilter($comment->getInputFilter());

            $commentForm->setData($this->getRequest()->getPost());
            if ($commentForm->isValid()) {
                $data = $commentForm->getData();
                $url  = isset($data['returnTo']) ? $data['returnTo'] : '/';
                $comment->exchangeArray($data);
                $ipAddress = $this->getRequest()->getServer('REMOTE_ADDR');
                $comment->setTitle($ipAddress);

                /** @var $service \Application\Service\MerchantService */
                $service  = $this->getServiceLocator()->get('merchants_service');
                $merchant = $service->find($data['merchantId']);

                try {
                    $merchant->comments->add($comment);
                    $service->save($merchant);

                    $message = 'Thank you for your comment';
                    $error   = false;
                } catch (RuntimeException $e) {
                    $message = 'An Error Occurred';
                    $error   = true;
                }
            } else {
                $message = $commentForm->getMessages();
                $error   = true;

            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel(
                array(
                     'error'   => $error,
                     'message' => $message
                )
            );
        } else {

            $this->flashMessenger()
                ->setNamespace($error ? FlashMessenger::NAMESPACE_ERROR : FlashMessenger::NAMESPACE_SUCCESS)
                ->addMessage($message);

            return $this->redirect()->toUrl($url);
        }
    }
}