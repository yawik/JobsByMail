<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Form;

use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Jobs\Form\JobboardSearch;

class SubscribeForm extends JobboardSearch implements ViewPartialProviderInterface
{
    
    use ViewPartialProviderTrait;

    /**
     * Default view partial.
     *
     * @var string
     */
    private $defaultPartial = 'jobs-by-mail/form/subscribe/form';

    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        parent::init();
        $this->setAttribute('id', 'jobsbymail-subscribe-form')
            ->setAttribute('method', 'POST');
        
        $this->add([
            'type' => 'email',
            'name' => 'email',
            'options' => [
                'label' => /*@translate*/ 'Email'
            ],
            'attributes' => [
                'required' => true,
                'placeholder' => /*@translate*/ 'Enter your email address'
            ]
        ]);
        
        $this->getInputFilter()->add([
            'name' => 'l',
            'required' => false
        ]);
    }
}