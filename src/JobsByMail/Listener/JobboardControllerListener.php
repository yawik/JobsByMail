<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use JobsByMail\Form\SubscribeForm;

class JobboardControllerListener implements ListenerAggregateInterface
{
	use ListenerAggregateTrait;

	/**
	 * @see \Zend\EventManager\ListenerAggregateInterface::attach()
	 */
	public function attach(EventManagerInterface $events, $priority = 1)
	{
		$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'addForm']);
	}

	/**
	 * @param MvcEvent $event
	 */
	public function addForm(MvcEvent $event)
	{
		$viewModel = $event->getResult();

		if (!$viewModel instanceof ViewModel)
		{
			return;
		}
		
		$serviceManager = $event->getApplication()->getServiceManager();
		$viewModel->setVariable('jobsByMailForm', $serviceManager->get('FormElementManager')->get(SubscribeForm::class));
	}
}