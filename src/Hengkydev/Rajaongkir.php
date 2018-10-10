<?php

namespace Hengkydev;

use \Curl\Curl;
use Exception;

/**
 * rajaongkir base class
 * this library based on api documentation
 * https://rajaongkir.com/dokumentasi/
 */

class Rajaongkir extends Environment{

    private $waybill; // variable setup
    private $currency; // currency setup
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * setup some credentials for Rajaongkir API
     * view credentials at
     * https://rajaongkir.com/dokumentasi/
     * 
     * @param array $conf
     * @return Rajaongkir
     */
    public static function setup( array $conf ){
        $my         = new Self;
        $my->config($conf);
        return $my;
    }

    /**
     * calculate cost from Rajaongkir API
     * @param array $conf
     * @return Self
     */
    public static function cost( array $conf ){
        $my         = new Self;

        foreach($my->ava_field_cost as $value){
           if( !array_key_exists( $value,$conf) )
                throw new \Exception('Error: required field ( '.implode(',',$my->ava_field_cost).')'); 
        }

        $conf['originType']      = ( !isset($conf['originType']) ) ? 'city' : $conf['originType'];

        if( $my->type == "pro" ){
            $conf['destinationType'] = ( !isset($conf['destinationType']) ) ? 'subdistrict' : $conf['destinationType'];
        }else{
            $conf['destinationType'] = ( !isset($conf['destinationType']) ) ? 'city' : $conf['destinationType'];
        }

        $my->on_request   = "cost";
        $my->url          = $my->getUrl()->cost;
        $my->method       = "post";
        $my->field        = $conf;

        return $my;
    }

    /**
     * handle waybill
     * @param string $reciept_code
     * @param string $courier
     * @return self
     */
    
     public static function waybill( string $reciept_code, string $courier ){

        $my             = new Self;
        $my->url        = $my->getUrl()->waybill;
        $my->method     = "post";
        $my->field      = [
                            "waybill"   => $reciept_code,
                            "courier"   => $courier
                          ];
        
       return $my;
     }

    /**
     * handle field
     * @param array|int $field
     * @return array $this->field
     */
    public function handleField( $field ){
        return $this->field = (is_array($field)) ? $field : [ "id" => $field ];
    }

     /**
     * get province from rajaongkir API
     * @param array|int $field
     * @return Self
     */
    public static function province( $field = [] ){
        $me               = new Self;
        $me->url          = $me->getUrl()->province;
        $me->field        = $me->handleField( $field );
        return $me;
    }
    
    /**
     * get cities from rajaongkir API
     * @param array $field
     * @return Self
     */
    public static function cities( $field = [] ){
        $me               = new Self;
        $me->url          = $me->getUrl()->cities;
        $me->field        = $me->handleField( $field );
        return $me;
    }

    /**
     * get subdistrict from rajaongkir API
     * @param array $field|require
     * @return Self
     */
    public static function subdistrict( array $field ){
        $me               = new Self;
        $me->url          = $me->getUrl()->subdistrict;
        $me->field        = $me->handleField( $field );
        return $me;
    }

    /**
     * get handle to get query from API rajaongkir
     * @return \Curl\Curl
     */

    public function get(){

        $curl = new Curl;

        $curl->setOpt(CURLOPT_RETURNTRANSFER , true);
        $curl->setOpt(CURLOPT_ENCODING , '');
        $curl->setOpt(CURLOPT_MAXREDIRS , 10);
        $curl->setTimeout($this->timeout);
        $curl->setOpt(CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        $curl->setHeader('key', $this->key);

        if($this->method=="get"){
            $curl->get($this->url,$this->field);
        }else{
            $curl->post($this->url,$this->field);
        }
        

        if ($curl->error) {
            throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);    
        }

        if($this->on_request=="cost")
            $curl->response->rajaongkir->results = $curl->response->rajaongkir->results[0];
            return $curl->response->rajaongkir;

        return $curl->response->rajaongkir->results;
    }
}