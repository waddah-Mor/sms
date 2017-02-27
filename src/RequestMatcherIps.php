<?php

namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class RequestMatcherIps extends RequestMatcher
{

    protected $ips = null;

    protected $allowedIps = array();

    protected $restrictIps = ture;

    /*protected $allowedIps = array('waddah-local' => '192.168.16.133', 
                                    'pndev-local' => '192.168.16.33', 
                                    );*/


    //check for environment variable (restrict ips) and pass them if they exist
    public function __construct() {
        
        $this->ips = getenv('RESTRICT_IPS');

        if ($this->ips) {

            $this->getIpsArray($this->ips);

        }else{

            $this->restrictIps = false;

        }
    }


    //filter and move the restrict ips into array
    protected function getIpsArray($ips){
        
        $ips = explode(',', trim($ips));
        
        foreach ($ips as $ip) {
            
            $this->allowedIps []= $ip;
        }
    }


    //check if client ip match any of the restrict ips and return true (if matched) or false
    public function checkIpsList(Request $request){

    	$isMatched = false;

        //if there's a restricted ips then run them against the client's ip
        if ($this->restrictIps) {

            foreach ($this->allowedIps as $ip) {

                //create new RequestMatcher object with the wanted restrict ip
                $reqMatcher = new RequestMatcher(null, null, null, $ip);

                //return true if the client ip match the restrict ip
                $isMatched = $reqMatcher->matches($request);
                
                if ($isMatched) {

                    //Matched an ip (Access Granted)
                    return $isMatched;
                }
            }

            //No ips have matched (Access Denied)
            return $isMatched;

        }else{

            //No restrict ips wanted
            return $isMatched = true;

        }
    }

}

?>