<?php
/**
 * User: Roeland Werring
 * Date: 19/05/15
 * Time: 13:40
 * 
 */

namespace App\Resources\Combiner;
class SimOnly extends FeedCombinerService {

    protected $methodMapping = [
        'products'     => [
            'class'       => \App\Resources\Combiner\Methods\SimOnlyProducts::class,
            'description' => 'Request list merged list of products'
        ],
        'contract'     => [
            'class'       => \App\Resources\Telecombinatie\Methods\Impl\Contract::class,
            'description' => 'Post a lead to TeleCombinatie'
        ],
    ];
}