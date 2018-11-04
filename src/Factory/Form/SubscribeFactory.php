<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Form;

use Core\Factory\Form\AbstractCustomizableFieldsetFactory;
use JobsByMail\Form\SubscribeForm;
use Interop\Container\ContainerInterface;

class SubscribeFactory extends AbstractCustomizableFieldsetFactory
{
    const OPTIONS_NAME = 'JobsByMail/SubscribeOptions';

    const CLASS_NAME = SubscribeForm::class;
    
    /**
     * {@inheritDoc}
     * @see \Core\Factory\Form\AbstractCustomizableFieldsetFactory::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $router = $container->get('Router');
        
        $form = parent::__invoke($container, $requestedName, $options);
        $form->clearAttributes();
        $form->setAttribute('action', $router->assemble([], ['name' => 'lang/jobsbymail/subscribe']));
        
        return $form;
    }
}
