<?php

namespace Hengkydev;
use Exception;

class Environment {

    /**
     * define name for variable environment
     * @var
     */
    const API_KEY           = "RAJAONGKIR_API_KEY";
    const API_TYPE          = "RAJAONGKIR_API_TYPE";

    /**
     * declare some environment variable
     * @var 
     */
    public $url;            // url end point API based on rajaongkir url
    public $type;           // type key API, pro, basic or starter
    public $key;            // API key
    public $attributes; 
    public $field           = [];
    public $ava_field_cost  = [ "origin", "destination", "weight", "courier"];
    public $timeout         = 30;
    public $method          = "get";
    public $on_request      = null;

    private $ava_type       = ["starter","basic","pro"];

    // constructor
    public function __construct()
    {
      if( getEnv("RAJAONGKIR_API_KEY") ){

        $conf           = [
            "RAJAONGKIR_API_KEY"    => getenv("RAJAONGKIR_API_KEY"),
            "RAJAONGKIR_API_TYPE"   => getenv("RAJAONGKIR_API_TYPE")
        ];

        $this->config($conf);
      }
      
    }

    public function config(array $env){
        // define key
        if( !isset( $env[Self::API_KEY] ) )
            throw new \Exception("please fill 'RAJAONGKIR_API_KEY' ");

        $this->key      = $env[Self::API_KEY];
        
        // define type 
        $type   = strtolower(@$env[Self::API_TYPE]);
        $this->type     = ( !in_array($type,$this->ava_type) ) ? "starter" : $type;
        
        // define url
        $this->url      = ( $this->type =="basic" || $this->type=="starter" ) ? 
                            "https://api.rajaongkir.com/{$this->type}" :
                            "https://pro.rajaongkir.com/api";
    }

    /**
     * helper to handle uri parse for url
     * @param string $url 
     * @return string
     */
    public function url( string $url = "" ){
        if(!$url) return $this->url;

        return $this->url."/".trim($url,"/");
    }

    /**
     * get all ur for environment api key
     * @return object
     */
    public function getUrl(){

        return json_decode(json_encode([
            "province"                  => $this->url('province'),
            "cities"                    => $this->url('city'),
            "subdistrict"               => $this->url('subdistrict'),
            "international"             => [
                "origin"                => $this->url("v2/internationalOrigin"),
                "destination"           => $this->url("v2/internationalDestination"),
                "cost"                  => $this->url("v2/internationalCost")
            ],
            "cost"                      => $this->url("cost"),
            "currency"                  => $this->url("currency"),
            "waybill"                   => $this->url("waybill")
        ]));

    }
}