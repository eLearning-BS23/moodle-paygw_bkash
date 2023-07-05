<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Various helper methods for interacting with the bkash API
 *
 * @package    paygw_bkash
 * @copyright  2023 Brain station 23 ltd.
 * @author     Brain station 23 ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_bkash;

use stdClass;

/**
 * The helper class for bkash payment gateway.
 *
 * @copyright  2021 Brain station 23 ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bkash_helper {

    /**
     * @var string public business store ID
     */
    private $username;

    /**
     * @var string public business store password
     */
    private $password;

    /**
     * @var string public business store app key
     */
    private $appkey;
    /**
     * @var string public business app secret key
     */
    private $appsecret;


    /**
     * @var string public business api url
     */
    private $apiurl;

    /**
     * @var string public production environment
     */
    private $paymentmodes;

    /**
     * Initialise the bkash API client.
     *
     * @param string $username       Merchant username
     * @param string $password       Merchant account password
     * @param string $appkey         Merchant account app key
     * @param string $appsecret      Merchant account app secret key
     * @param bool   $paymentmodes   whether we are working with the sandbox environment or not
     */
    public function __construct(
        string $username,
        string $password,
        string $appkey,
        string $appsecret,
        string $paymentmodes
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->appkey = $appkey;
        $this->appsecret = $appsecret;
        $this->paymentmodes = $paymentmodes;
        if($paymentmodes == 'sandbox') {
            $this->apiurl = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
        } else {
            $this->apiurl = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
        }
    }

    public function curl_with_body($url,$header,$method,$body_data_json){
        
        $curl = curl_init($this->apiurl.$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
    public function get_grant_token() {
        $headers = array(
                'Content-Type:application/json',
                'username:'.$this->username,
                'password:'.$this->password,
                );
        $body = array(
            'app_key'=> $this->appkey, 
            'app_secret'=>$this->appsecret
        );
        $payload=json_encode($body);
    
        $response = $this->curl_with_body('/tokenized/checkout/token/grant',$headers, 'POST', $payload);

        $token = json_decode($response)->id_token;

        return $token;
    }

    public function auth_headers() {
        return array(
            'Content-Type:application/json',
            'Authorization:' .$this->get_grant_token(),
            'X-APP-Key:'.$this->appkey
        );
    }

    public function create_payment($callbackurl, $cost) {

        $headers = $this->auth_headers();

        $body = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => $callbackurl,
            'amount' => $cost,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "Inv".rand(0, 99999999) // We can pass something like orderID. 
        );

        $payload =json_encode($body);

        $response = $this->curl_with_body('/tokenized/checkout/create',$headers,'POST',$payload);

        return redirect((json_decode($response)->bkashURL));
    }

    public function execute_payment($paymentID) {
        $headers =$this->auth_headers();

        $body = array(
            'paymentID' => $paymentID
        );
        $payload = json_encode($body);
        $response = $this->curl_with_body('/tokenized/checkout/execute',$headers,'POST',$payload);
        return $response;
    }
}
