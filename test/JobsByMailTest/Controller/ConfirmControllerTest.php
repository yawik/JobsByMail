<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Controller;

use JobsByMail\Controller\ConfirmController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Hash;
use JobsByMail\Entity\SearchProfile;
use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\View\Model\ViewModel;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @coversDefaultClass \JobsByMail\Controller\ConfirmController
 */
class ConfirmControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ConfirmController
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
     * @var Hash
     */
    private $hash;
    
    /**
     * @var Translator
     */
    private $translator;
    
    /**
     * @var array
     */
    private $params = [
        'id' => 'idValue',
        'hash' => 'hashValue'
    ];
    
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
        
        $this->hash = $this->getMockBuilder(Hash::class)
            ->getMock();
        
        $this->translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        
        $this->controller = $this->getMockBuilder(ConfirmController::class)
            ->setConstructorArgs([$this->searchProfileRepository, $this->subscriber, $this->hash, $this->translator])
            ->setMethods(['params'])
            ->getMock();
        $this->controller->method('params')
            ->will($this->returnValueMap($this->convertToValueMap($this->params)));
    }
    
    /**
     * @covers ::__construct()
     * @covers ::indexAction()
     */
    public function testIndexActionProfileNotFound()
    {
        $errorMessage = 'translated error message';
        
        $this->searchProfileRepository->expects($this->once())
            ->method('find')
            ->with($this->params['id']);
        
        $this->translator->expects($this->once())
            ->method('translate')
            ->with('Time to confirm a search profile has expired')
            ->willReturn($errorMessage);
        
        $this->hash->expects($this->never())
            ->method('validate');
        
        $this->subscriber->expects($this->never())
            ->method('confirm');
        
        $viewModel = $this->controller->indexAction();
        $this->assertSame(Response::STATUS_CODE_404, $this->controller->getResponse()->getStatusCode());
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertSame($errorMessage, $viewModel->getVariable('message'));
    }
    
    /**
     * @covers ::indexAction()
     */
    public function testIndexActionInvalidHash()
    {
        $searchProfile = new SearchProfile();
        $errorMessage = 'translated error message';
        
        $this->searchProfileRepository->expects($this->once())
            ->method('find')
            ->with($this->params['id'])
            ->willReturn($searchProfile);
        
        $this->translator->expects($this->once())
            ->method('translate')
            ->with('Invalid URL')
            ->willReturn($errorMessage);
        
        $this->hash->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($searchProfile), $this->identicalTo($this->params['hash']))
            ->willReturn(false);
        
        $this->subscriber->expects($this->never())
            ->method('confirm');
        
        $viewModel = $this->controller->indexAction();
        $this->assertSame(Response::STATUS_CODE_404, $this->controller->getResponse()->getStatusCode());
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertSame($errorMessage, $viewModel->getVariable('message'));
    }
    
    /**
     * @covers ::indexAction()
     */
    public function testIndexActionValidHash()
    {
        $searchProfile = new SearchProfile();
        
        $this->searchProfileRepository->expects($this->once())
            ->method('find')
            ->with($this->params['id'])
            ->willReturn($searchProfile);
        
        $this->translator->expects($this->never())
            ->method('translate');
        
        $this->hash->expects($this->once())
            ->method('validate')
            ->with($this->identicalTo($searchProfile), $this->identicalTo($this->params['hash']))
            ->willReturn(true);
        
        $this->subscriber->expects($this->once())
            ->method('confirm')
            ->with($this->identicalTo($searchProfile));
        
        $viewModel = $this->controller->indexAction();
        $this->assertSame(Response::STATUS_CODE_200, $this->controller->getResponse()->getStatusCode());
        $this->assertInstanceOf(ViewModel::class, $viewModel);
        $this->assertNull($viewModel->getVariable('message'));
        $this->assertSame($searchProfile, $viewModel->getVariable('searchProfile'));
    }

    /**
     * @param array $array
     * @return array
     */
    private function convertToValueMap(array $array)
    {
        $return = [];
        
        foreach ($this->params as $key => $value) {
            $return[] = [$key, $value];
        }
        
        return $return;
    }
}
