<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\View\Helper;

use Zend\View\Helper\AbstractHelper;
use JobsByMail\Form\SubscribeForm;
use Core\Controller\Plugin\PaginationParams;
use Auth\AuthenticationService;

/**
 * Renders subscription form
 */
class SubscriptionForm extends AbstractHelper
{

    /**
     *
     * @var SubscribeForm
     */
    private $form;

    /**
     *
     * @var PaginationParams
     */
    private $paginationParams;

    /**
     *
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     *
     * @param SubscribeForm $form
     * @param PaginationParams $paginationParams
     * @param AuthenticationService $authenticationService
     */
    public function __construct(SubscribeForm $form, PaginationParams $paginationParams, AuthenticationService $authenticationService)
    {
        $this->form = $form;
        $this->paginationParams = $paginationParams;
        $this->authenticationService = $authenticationService;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $data = $this->paginationParams->__invoke('Jobs_Board', [
            'q',
            'l',
            'd' => 10
        ])->toArray();
        
        if ($this->authenticationService->hasIdentity()) {
            $data['email'] = $this->authenticationService->getUser()->getEmail();
        }
        
        $this->form->setData($data);
        
        return $this->getView()->form($this->form);
    }
}
