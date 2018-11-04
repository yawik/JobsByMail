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
use Zend\View\Model\JsonModel;
use Interop\Container\ContainerInterface;
use Zend\View\Renderer\RendererInterface;
use JobsByMail\Form\SubscribeForm;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Mailer;

class SubscribeController extends AbstractActionController
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
     * @var Mailer
     */
    private $mailer;
    
    /**
     * @var ContainerInterface
     */
    private $formElementManager;
    
    /**
     * @var RendererInterface
     */
    private $viewRenderer;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param Subscriber $subscriber
     * @param Mailer $mailer
     * @param ContainerInterface $formElementManager
     * @param RendererInterface $viewRenderer
     */
    public function __construct(
        SearchProfileRepository $searchProfileRepository,
        Subscriber $subscriber,
        Mailer $mailer,
        ContainerInterface $formElementManager,
        RendererInterface $viewRenderer)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->subscriber = $subscriber;
        $this->mailer = $mailer;
        $this->formElementManager = $formElementManager;
        $this->viewRenderer = $viewRenderer;
    }
    
    public function indexAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        
        if (!$request->isPost() || !$request->isXmlHttpRequest()) {
            // refuse non-POST or non-XHR requests
            return $this->getResponse()
                ->setStatusCode(Response::STATUS_CODE_404);
        }
        
        $data = $request->getPost()->toArray();
        
        /** @var SubscribeForm $form */
        $form = $this->formElementManager->get(SubscribeForm::class)
            ->setData($data);
        
        if ($form->isValid()) {
            $email = $data['email'];
            unset($data['email']);
            
            $searchProfile = $this->subscriber->subscribe($email, $data, $this->params('lang'));
            $this->searchProfileRepository->getDocumentManager()->flush();
            
            $sent = $this->mailer->sendConfirmation($searchProfile);
            
            return new JsonModel([
                'valid' => true,
                'content' => $this->viewRenderer->render((new ViewModel())
                    ->setVariable('sent', $sent)
                    ->setVariable('searchProfile', $searchProfile)
                    ->setTemplate('jobs-by-mail/form/subscribe/result')
                    ->setTerminal(true))
            ]);
        }
        
        return new JsonModel([
            'valid' => false,
            'errors' => $form->getMessages()
        ]);
    }
}
