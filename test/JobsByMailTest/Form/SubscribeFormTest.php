<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Form;

use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use JobsByMail\Form\SubscribeForm;
use Zend\Form\Element\Select;

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
        $formElementManager = new FormElementManagerV2Polyfill();
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
