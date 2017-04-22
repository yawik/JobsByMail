<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Controller;

use JobsByMail\Controller\SubscribeController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Mailer;
use JobsByMail\Entity\SearchProfile;
use JobsByMail\Form\SubscribeForm;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Interop\Container\ContainerInterface;
use Zend\View\Renderer\RendererInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @coversDefaultClass \JobsByMail\Controller\SubscribeController
 */
class SubscribeControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SubscribeController
     */
    private $controller;
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var Subscriber
     */
    private $subscriber;
    
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
     * @var SubscribeForm
     */
    private $form;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $objectManager = $this->getMockBuilder(ObjectManager::class)
            ->getMock();
        
        $this->searchProfileRepository = $this->getMockBuilder(SearchProfileRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchProfileRepository->method('getDocumentManager')
            ->willReturn($objectManager);
        
        $this->subscriber = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->form = $this->getMockBuilder(SubscribeForm::class)
            ->getMock();
        
        $this->formElementManager = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $this->formElementManager->method('get')
            ->will($this->returnValueMap([
                [SubscribeForm::class, $this->form]
            ]));
        
        $this->viewRenderer = $this->getMockBuilder(RendererInterface::class)
            ->getMock();
        
        $this->controller = $this->getMockBuilder(SubscribeController::class)
            ->setConstructorArgs([
                $this->searchProfileRepository,
                $this->subscriber,
                $this->mailer,
                $this->formElementManager,
                $this->viewRenderer
            ])
            ->setMethods(['params'])
            ->getMock();
    }
    
    /**
     * @covers ::__construct()
     * @covers ::indexAction()
     * @dataProvider dataIndexInvalidRequest
     * @param string $method
     * @param bool $isXhr
     */
    public function testIndexInvalidRequest($method, $isXhr)
    {
        /** @var Request $request */
        $request = $this->controller->getRequest();
        $request->setMethod($method);
        /** @var Response $response */
        $response = $this->controller->getResponse();
        
        if ($isXhr) {
            $request->getHeaders()
                ->addHeaderLine('X-Requested-With: XMLHttpRequest');
        }
        
        $this->form->expects($this->never())
            ->method('setData');
        
        $this->assertEquals($isXhr, $request->isXmlHttpRequest());
        $this->assertSame($response, $this->controller->indexAction());
        $this->assertSame(Response::STATUS_CODE_404, $response->getStatusCode());
    }
    
    /**
     * @covers ::indexAction()
     */
    public function testIndexInvalidData()
    {
        $data = [
            'q' => 'searchPhrase',
            'email' => 'invalidEmail'
        ];
        $errors = [
            'error message'
        ];
        
        /** @var Request $request */
        $request = $this->controller->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($data));
        $request->getHeaders()
            ->addHeaderLine('X-Requested-With: XMLHttpRequest');
        /** @var Response $response */
        $response = $this->controller->getResponse();
        
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($data))
            ->willReturnSelf();
        $this->form->expects($this->once())
            ->method('getMessages')
            ->willReturn($errors);
        
        $this->subscriber->expects($this->never())
            ->method('subscribe');
        
        $jsonModel = $this->controller->indexAction();
        $this->assertSame(Response::STATUS_CODE_200, $response->getStatusCode());
        $this->assertInstanceOf(JsonModel::class, $jsonModel);
        
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('valid', $variables);
        $this->assertFalse($variables['valid']);
        $this->assertArrayHasKey('errors', $variables);
        $this->assertSame($errors, $variables['errors']);
    }
    
    /**
     * @covers ::indexAction()
     */
    public function testIndexValidData()
    {
        $data = [
            'q' => 'searchPhrase',
            'l' => 'someLocation',
            'd' => 20,
            'email' => 'user@domain.tld'
        ];
        $subscribeData = $data;
        unset($subscribeData['email']);
        $language = 'es';
        $searchProfile = new SearchProfile();
        $sent = true;
        $content = '<markup>';
        
        /** @var Request $request */
        $request = $this->controller->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($data));
        $request->getHeaders()
            ->addHeaderLine('X-Requested-With: XMLHttpRequest');
        /** @var Response $response */
        $response = $this->controller->getResponse();
        
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($data))
            ->willReturnSelf();
        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $this->form->expects($this->never())
            ->method('getMessages');
        
        $this->controller->method('params')
            ->willReturn($language);
        
        $this->subscriber->expects($this->once())
            ->method('subscribe')
            ->with($this->identicalTo($data['email']),
                $this->identicalTo($subscribeData),
                $this->identicalTo($language))
            ->willReturn($searchProfile);
        
        $this->mailer->expects($this->once())
            ->method('sendConfirmation')
            ->with($this->identicalTo($searchProfile))
            ->willReturn($sent);
        
        $this->viewRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function ($viewModel) use ($sent, $searchProfile) {
                if (!$viewModel instanceof ViewModel) {
                    return false;
                }
                
                if ($viewModel->getVariable('sent') !== $sent) {
                    return false;
                }
                
                if ($viewModel->getVariable('searchProfile') !== $searchProfile) {
                    return false;
                }
                
                if (!$viewModel->terminate()) {
                    return false;
                }
                
                return true;
            }))
            ->willReturn($content);
        
        $jsonModel = $this->controller->indexAction();
        $this->assertSame(Response::STATUS_CODE_200, $response->getStatusCode());
        $this->assertInstanceOf(JsonModel::class, $jsonModel);
        
        $variables = $jsonModel->getVariables();
        $this->assertArrayHasKey('valid', $variables);
        $this->assertTrue($variables['valid']);
        $this->assertArrayHasKey('content', $variables);
        $this->assertSame($content, $variables['content']);
    }
    
    /**
     * @return array
     */
    public function dataIndexInvalidRequest()
    {
        return [
            [Request::METHOD_GET, false],
            [Request::METHOD_POST, false],
            [Request::METHOD_GET, true]
        ];
    }
}
