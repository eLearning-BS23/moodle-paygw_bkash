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
 * Settings for the bkash payment gateway
 *
 * @package    paygw_bkash
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;
use paygw_bkash\bkash_helper;

require_once(__DIR__ . '/../../../config.php');

global $CFG, $USER, $DB;

$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);
$amount = optional_param('amount', '0', PARAM_FLOAT);

$courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);


$config = (object)helper::get_gateway_configuration($component, $paymentarea, $itemid, 'bkash');
$payable = helper::get_payable($component, $paymentarea, $itemid);
$surcharge = helper::get_gateway_surcharge('bkash');
$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

// if($amount == 0) {
//     $cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);
// } else {
//     if ($DB->record_exists('local_discount_used_coupon', ['used_by' => $userid, 'amount' => $amount])) {
//         $cost = $amount;
//     } else {
//         // Redirect to frontend.
//         $redirecturl = $config->frontendurl . 'status=3&message=payment_cancelled&paygw=bkash';
//         header("Location: $redirecturl", true, 301);  
//         exit(); 
//     }
// }

$bkashhelper = new bkash_helper(
    $config->username,
    $config->password,
    $config->appkey,
    $config->appsecret,
    $config->paymentmodes
);

$callbackurl = $CFG->wwwroot.'/payment/gateway/bkash/process.php?courseid=' .
                $courseid . '&component=' . $component .
                '&paymentarea=' . $paymentarea . '&itemid=' . $itemid;
$bkashhelper->create_payment($callbackurl, $cost);

