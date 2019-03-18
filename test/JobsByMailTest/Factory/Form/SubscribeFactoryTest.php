<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Factory\Form;

use Interop\Container\ContainerInterface;
use JobsByMail\Factory\Form\SubscribeFactory;
use JobsByMail\Form\SubscribeForm;
use Zend\Router\RouteInterface;
use JobsByMail\Options\SubscribeOptions;

/**
 * @coversDefaultClass \JobsByMail\Factory\Form\SubscribeFactory
 */
class SubscribeFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $action = '/action/url';
        
        $router = $this->getMockBuilder(RouteInterface::class)
            ->getMock();
        $router->expects($this->once())
            ->method('assemble')
            ->with($this->identicalTo([]), $this->identicalTo(['name' => 'lang/jobsbymail/subscribe']))
            ->willReturn($action);
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->method('get')
            ->will($this->returnValueMap([
                ['Router', $router],
                [SubscribeFactory::OPTIONS_NAME, new SubscribeOptions()]
            ]));
        
        
        $formFactory = new SubscribeFactory();
        $form = $formFactory->__invoke($container, 'JobsByMail\Form\SubscribeForm');
        $this->assertInstanceOf(SubscribeForm::class, $form);
        $this->assertSame($action, $form->getAttribute('action'));
    }
}
