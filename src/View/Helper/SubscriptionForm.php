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

use Laminas\View\Helper\AbstractHelper;
use JobsByMail\Form\SubscribeForm;
use Core\Controller\Plugin\PaginationParams;
use Auth\AuthenticationService;
use Laminas\Paginator\Paginator;
use Solr\FacetsProviderInterface;
use Laminas\Form\Fieldset;
use Core\Form\View\Helper\Form as FormHelper;

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
     * @param Paginator $jobs
     * @return string
     */
    public function render(Paginator $jobs)
    {
        $facets = null;
        $data = $this->paginationParams->__invoke('Jobs_Board', [
            'q',
            'l',
            'd' => 10
        ])->toArray();
        
        if ($this->authenticationService->hasIdentity()) {
            $data['email'] = $this->authenticationService->getUser()->getEmail();
        }
        
        $this->form->setData($data);
        
        if ($jobs instanceof FacetsProviderInterface) {
            $facets = $jobs->getFacets();
            
            foreach ($facets->getActiveValues() as $name => $values) {
                $fieldset = new Fieldset($name);
                
                foreach ($values as $value) {
                    $fieldset->add([
                        'type' => 'hidden',
                        'name' => $value
                    ]);
                }
                
                $this->form->add($fieldset);
            }
        }
        
        return $this->getView()->form($this->form, FormHelper::LAYOUT_HORIZONTAL, ['facets' => $facets]);
    }
}
