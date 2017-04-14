<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Hash;
use Zend\I18n\Translator\TranslatorInterface as Translator;


class ConfirmController extends AbstractActionController
{
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var Subscriber
     */
    private $subscriber;
    
    /**
     * @var Hash
     */
    private $hash;
    
    /**
     * @var Translator
     */
    private $translator;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param Subscriber $subscriber
     * @param Hash $hash
     * @param Translator $translator
     */
    public function __construct(SearchProfileRepository $searchProfileRepository, Subscriber $subscriber, Hash $hash, Translator $translator)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->subscriber = $subscriber;
        $this->hash = $hash;
        $this->translator = $translator;
    }
    
    public function indexAction()
    {
        $id = $this->params('id');
        $errorMessage = null;
        $searchProfile = $this->searchProfileRepository->find($id);

        if (!$searchProfile) {
            $errorMessage = $this->translator->translate('Time to confirm a search profile has expired');
        }
        
        if (!$errorMessage && !$this->hash->validate($searchProfile, $this->params('hash'))) {
            $errorMessage = $this->translator->translate('Invalid URL');
        }
        
        if ($errorMessage) {
            $this->getResponse()
                ->setStatusCode(Response::STATUS_CODE_404);
            
            return new ViewModel([
                'message' => $errorMessage
            ]);
        }
        
        $this->subscriber->confirm($searchProfile);
        $this->searchProfileRepository->getDocumentManager()->flush();
        
        return new ViewModel([
            'searchProfile' => $searchProfile
        ]);
    }
}
