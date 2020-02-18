<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\View\Helper;

use JobsByMail\View\Helper\SubscriptionForm as SubscriptionFormHelper;
use JobsByMail\Form\SubscribeForm;
use Core\Controller\Plugin\PaginationParams;
use Auth\AuthenticationService;
use Laminas\Paginator\Paginator;
use Solr\Paginator\Paginator as PaginatorFacetsProvider;
use Solr\Facets;
use Laminas\Stdlib\Parameters;
use Laminas\View\Renderer\RendererInterface;
use Core\Form\View\Helper\Form as FormHelper;
use Auth\Entity\User;
use Laminas\Form\Fieldset;

/**
 * @coversDefaultClass \JobsByMail\View\Helper\SubscriptionForm
 */
class SubscriptionFormTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SubscriptionFormHelper
     */
    private $formHelper;
    
    /**
     * @var SubscribeForm
     */
    private $form;

    /**
     * @var PaginationParams
     */
    private $paginationParams;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var RendererInterface
     */
    private $view;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->form = $this->getMockBuilder(SubscribeForm::class)
            ->getMock();
        
        $this->paginationParams = $this->getMockBuilder(PaginationParams::class)
            ->getMock();
        
        $this->authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->view = $this->getMockBuilder(RendererInterface::class)
            ->setMethods(['getEngine', 'setResolver', 'render', 'form'])
            ->getMock();
        
        $this->formHelper = new SubscriptionFormHelper($this->form, $this->paginationParams, $this->authenticationService);
        $this->formHelper->setView($this->view);
    }
    
    /**
     * @covers ::__construct()
     * @covers ::render()
     */
    public function testRender()
    {
        $result = 'form markup';
        $data = [
            'q' => 'search',
            'l' => 'location',
            'd' => 50
        ];
        $parameters = new Parameters($data);
        
        $this->view->expects($this->once())
            ->method('form')
            ->with($this->identicalTo($this->form), $this->identicalTo(FormHelper::LAYOUT_HORIZONTAL), $this->identicalTo(['facets' => null]))
            ->willReturn($result);
            
        $this->paginationParams->expects($this->once())
            ->method('__invoke')
            ->with('Jobs_Board', [
                'q',
                'l',
                'd' => 10
            ])
            ->willReturn($parameters);
            
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($data));
        
        $jobs = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->assertSame($result, $this->formHelper->render($jobs));
    }
    
    /**
     * @covers ::render()
     */
    public function testRenderWithLoggedUser()
    {
        $result = 'form markup';
        $data = [
            'q' => 'search',
            'l' => 'location',
            'd' => 50
        ];
        $email = 'user@domain.tld';
        $user = new User();
        $user->setEmail($email);
        $parameters = new Parameters($data);
        
        $this->authenticationService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        
        $this->authenticationService->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $this->view->expects($this->once())
            ->method('form')
            ->with($this->identicalTo($this->form), $this->identicalTo(FormHelper::LAYOUT_HORIZONTAL), $this->identicalTo(['facets' => null]))
            ->willReturn($result);
            
        $this->paginationParams->expects($this->once())
            ->method('__invoke')
            ->with('Jobs_Board', [
                'q',
                'l',
                'd' => 10
            ])
            ->willReturn($parameters);
            
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo(array_merge($data, [
                'email' => $email
            ])));
        
        $jobs = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->assertSame($result, $this->formHelper->render($jobs));
    }
    
    /**
     * @covers ::render()
     */
    public function testRenderWithFacetsProvider()
    {
        if (!class_exists(PaginatorFacetsProvider::class)) {
            $this->markTestSkipped('Solr module is not available');
        }
        
        $result = 'form markup';
        $data = [
            'q' => 'search',
            'l' => 'location',
            'd' => 50
        ];
        $parameters = new Parameters($data);
        
        $facets = $this->getMockBuilder(Facets::class)
            ->getMock();
        $facets->expects($this->once())
            ->method('getActiveValues')
            ->willReturn([
                'groupOne' => [
                    'FirstOne',
                    'SecondOne',
                    'ThirdOne'
                ],
                'groupTwo' => [
                    'FirstTwo',
                    'SecondTwo',
                    'ThirdTwo'
                ]
            ]);
            
        $this->view->expects($this->once())
            ->method('form')
            ->with($this->identicalTo($this->form), $this->identicalTo(FormHelper::LAYOUT_HORIZONTAL), $this->identicalTo(['facets' => $facets]))
            ->willReturn($result);
            
        $this->paginationParams->expects($this->once())
            ->method('__invoke')
            ->with('Jobs_Board', [
                'q',
                'l',
                'd' => 10
            ])
            ->willReturn($parameters);
            
        $this->form->expects($this->once())
            ->method('setData')
            ->with($this->identicalTo($data));
        $this->form->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [$this->callback(function ($fieldset) {
                    if (!$fieldset instanceof Fieldset) {
                        return false;
                    }
                    
                    if ($fieldset->getName() !== 'groupOne') {
                        return false;
                    }
                    
                    foreach (['FirstOne', 'SecondOne', 'ThirdOne'] as $name) {
                        if (!$fieldset->has($name)) {
                            return false;
                        }
                        if ($fieldset->get($name)->getAttribute('type') !== 'hidden') {
                            return false;
                        }
                    }
                    
                    return true;
                })],
                [$this->callback(function ($fieldset) {
                    if (!$fieldset instanceof Fieldset) {
                        return false;
                    }
                    
                    if ($fieldset->getName() !== 'groupTwo') {
                        return false;
                    }
                    
                    foreach (['FirstTwo', 'SecondTwo', 'ThirdTwo'] as $name) {
                        if (!$fieldset->has($name)) {
                            return false;
                        }
                        if ($fieldset->get($name)->getAttribute('type') !== 'hidden') {
                            return false;
                        }
                    }
                    
                    return true;
                })]
            );
        
        $jobs = $this->getMockBuilder(PaginatorFacetsProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $jobs->expects($this->once())
            ->method('getFacets')
            ->willReturn($facets);
        
        $this->assertSame($result, $this->formHelper->render($jobs));
    }
}