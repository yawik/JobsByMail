<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Form;

use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use JobsByMail\Form\SubscribeForm;
use Laminas\Form\Element\Select;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @coversDefaultClass \JobsByMail\Form\SubscribeForm
 */
class SubscribeFormTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::init()
     */
    public function testInit()
    {
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has', 'build'])
            ->getMock();
        
        $formElementManager = new FormElementManager($container);
        $formElementManager->setService('LocationSelect', new Select());
        
        $form = new SubscribeForm();
        $form->getFormFactory()->setFormElementManager($formElementManager);
        $form->init();
        
        $this->assertTrue($form->has('q'));
        $this->assertTrue($form->has('l'));
        $this->assertTrue($form->has('d'));
        $this->assertTrue($form->has('email'));
    }
}
