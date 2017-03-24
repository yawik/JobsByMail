<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Form;

use Zend\Form\Form;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;

class SubscribeForm extends Form implements ViewPartialProviderInterface
{
    
    use ViewPartialProviderTrait;

    /**
     * Default view partial.
     *
     * @var string
     */
    private $defaultPartial = 'jobs-by-mail/form/subscribe';

    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->add([
            'type' => 'email',
            'name' => 'email',
            'options' => [
                'label' => /*@translate*/ 'Email'
            ],
            'attributes' => [
                'required' => true
            ]
        ]);
    }
}