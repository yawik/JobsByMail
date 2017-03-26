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
use JobsByMail\Form\SubscribeForm;
use JobsByMail\Service\Subscriber;


class SubscribeController extends AbstractActionController
{
    
    /**
     * @var Subscriber
     */
    private $subscriber;
    
    /**
     * @param Subscriber $subscriber
     */
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }
    
    public function subscribeAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        
        if (!$request->isPost() || !$request->isXmlHttpRequest()) {
            // refuse non-POST or non-XHR requests
            return $this->getResponse()
                ->setStatusCode(Response::STATUS_CODE_404);
        }
        
        /** @var SubscribeForm $form */
        $form = $this->serviceLocator->get('FormElementManager')
            ->get(SubscribeForm::class)
            ->setData($request->getPost()->toArray());
        
        if ($form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            unset($data['email']);
            
            $this->subscriber->subscribe($email, $data);
            
            return new JsonModel([
                'valid' => true,
                'content' => $this->serviceLocator->get('ViewRenderer')
                    ->render((new ViewModel())
                        ->setTemplate('jobs-by-mail/form/subscribe/success')
                        ->setTerminal(true))
            ]);
        }
        
        return new JsonModel([
            'valid' => false,
            'errors' => $form->getMessages()
        ]);
    }
}
