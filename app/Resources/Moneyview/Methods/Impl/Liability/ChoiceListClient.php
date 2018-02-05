<?php
/**
 * User: Roeland Werring
 * Date: 10/03/15
 * Time: 15:38
 *
 */

namespace App\Resources\Moneyview\Methods\Impl\Liability;

use App\Resources\Moneyview\Methods\AbstractChoiceListClient;
use Config;

class ChoiceListClient extends AbstractChoiceListClient
{
    protected $moneyviewModuleName = 'AVP';

    public function __construct()
    {
        parent::__construct(self::TASK_PROCESS_ONE);
    }
}
