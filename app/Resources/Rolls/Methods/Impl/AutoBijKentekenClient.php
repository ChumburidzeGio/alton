<?php
/**
 * @deprecated
 *
 * (C) 2010 Vergelijken.net
 * User: RuleKinG
 * Date: 17-aug-2010
 * Time: 0:19:25
 */

namespace App\Resources\Rolls\Methods\Impl;

use App\Interfaces\ResourceInterface;
use App\Resources\Rolls\Methods\RollsAbstractSoapRequest;
use Config;


class AutoBijKentekenClient extends RollsAbstractSoapRequest
{

    private $extended = false;
    private $licenseplate = false;
    protected $arguments = [
        ResourceInterface::LICENSEPLATE => [
            'rules'     => self::VALIDATION_REQUIRED_LICENSEPLATE,
            'example'  => '35-jdr-8',
            'filter' => 'filterAlfaNumber'
        ],
        ResourceInterface::EXTENDED => [
            'rules'     => self::VALIDATION_BOOLEAN,
            'default' => 0,
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->init( Config::get( 'resource_rolls.functions.kenteken_auto_function' ) );

    }

    public function setParams( Array $params )
    {
        $this->setMerkenlijst( 0 );
        $this->setModellenlijst( 'False' );
        $this->setKenteken( $params[ResourceInterface::LICENSEPLATE] );
        $this->licenseplate = $params[ResourceInterface::LICENSEPLATE] ;
        if ($params[ResourceInterface::EXTENDED]) {
            $this->extended = $params[ResourceInterface::EXTENDED];
        }
    }


    /**
     * @param string $optionlList
     *
     * @return array
     */
    protected function getLincensePlateResult($optionlList = 'car_option_list')
    {

        $optionlListMapping = [
            'Koetswerk'          => ResourceInterface::COACHWORK_TYPE_ID,
            'Transmissie'        => ResourceInterface::TRANSMISSION_ID,
            'Beveiligingsklasse' => ResourceInterface::SECURITY_CLASS_ID,
        ];

        $result = parent::getResult();
        if( ! isset($result->Merk) || ! isset($result->Merk->Id)){
            $this->setErrorString('invalid licenseplate');
        }
        $return                                       = [];
        $return[ResourceInterface::BRAND_ID]          = $result->Merk->Id . "";
        $return[ResourceInterface::BRAND_NAME]        = $result->Merk->Naam . "";
        $return[ResourceInterface::MODEL_ID]          = $result->Model->Id . "";
        $return[ResourceInterface::MODEL_NAME]        = $result->Model->Naam . "";
        $return[ResourceInterface::CONSTRUCTION_DATE] = $result->Bouwjaar . '-' . str_pad($result->Bouwmaand, 2, "0", STR_PAD_LEFT) . '-01';

        $listRes                                      = $this->internalRequest('carinsurance', 'list', [ResourceInterface::OPTIONLIST => $optionlList]);
        $types                                        = $this->extractResult('Typen', 'Type');
        foreach($types as $type){
            $retType = [];
            foreach($type as $key => $value){
                if($key == 'Kentekenbrandstofid'){
                    $retType[$key] = $this->fuelTypeMapping[$value . ""];
                    continue;
                }
                if($key == 'Vermogen'){
                    $retType[$key] = round($value * 1.359623);
                    continue;
                }

                if( ! isset($optionlListMapping[$key], $listRes[$optionlListMapping[$key]], $listRes[$optionlListMapping[$key]][$value . ""])){
                    $retType[$key] = $value . "";
                    continue;
                }
                $retType[$key] = $listRes[$optionlListMapping[$key]][$value . ""]['name'];
            }
            $retType['Label']                   = "{$retType['Naam']} ({$retType['Aantaldeuren']} deurs, {$retType['Vermogen']} PK)";
            $retType['Keynaam']                 = $retType['Id'];
            $return[ResourceInterface::TYPES][] = $retType;
        }
        return $return;
    }

    public function getResult()
    {

        $result =  $this->getLincensePlateResult('car_option_list');

//        if ($this->extended) {
//            $extResult = $this->internalRequest($this->getRequestType(), 'licenseplatephoto', ['licenseplate' => $this->licenseplate]);
//            $result = array_merge($result, $extResult);
//        }
        $result['advise'] = $this->getDekkingAdvies($result['construction_date']);
        return $result;
    }

    public function executeFunction()
    {
        $xml =  parent::executeFunction(); // TODO: Change the autogenerated stub
        if($this->isError()) {
            $this->addErrorMessage(ResourceInterface::LICENSEPLATE,'resource.rolls.error.licenseplate','Ongeldig kenteken!','input');
            return;
        }
        return $xml;
    }


    /**
     * Auto generated functions from XML file 1.0
     *(C) 2010 Vergelijken.net
     */

    public function setKenteken( $kenteken )
    {
        $this->xml->Functie->Parameters->Kenteken = preg_replace( '/[^a-zA-Z0-9]/', '', $kenteken );
    }

    public function setMerkenlijst( $merkenlijst )
    {
        $this->xml->Functie->Parameters->Merkenlijst = $merkenlijst;
    }

    public function setModellenlijst( $modellenlijst )
    {
        $this->xml->Functie->Parameters->Modellenlijst = $modellenlijst;
    }

}

 
