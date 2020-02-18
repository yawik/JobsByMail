<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Hash;

class UnsubscribeController extends AbstractActionController
{
    
    /**
     * @var Subscriber
     */
    private $subscriber;
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var Hash
     */
    private $hash;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param Subscriber $subscriber
     * @param Hash $hash
     */
    public function __construct(SearchProfileRepository $searchProfileRepository, Subscriber $subscriber, Hash $hash)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->subscriber = $subscriber;
        $this->hash = $hash;
    }
    
    public function indexAction()
    {
        $id = $this->params('id');
        $response = $this->getResponse();
        $searchProfile = $this->searchProfileRepository->find($id);
        
        if ($searchProfile) {
            if ($this->hash->validate($searchProfile, $this->params('hash'))) {
                $this->subscriber->unsubscribe($searchProfile);
                $this->searchProfileRepository->getDocumentManager()->flush();
            } else {
                $response->setStatusCode(Response::STATUS_CODE_404);
                return;
            }
        }
        
        return new ViewModel([
            'searchProfile' => $searchProfile
        ]);
    }
}
