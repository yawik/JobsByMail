<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Options;

use Core\Options\FieldsetCustomizationOptions;

class SubscribeOptions extends FieldsetCustomizationOptions
{
    /**
     * Fields can be disabled.
     *
     * @var array
     */
   protected $fields=[
       'q' => [
            'enabled' => true
        ],
        'l' => [
            'enabled' => true
        ],
        'd' => [
            'enabled' => true
        ]
    ];
}