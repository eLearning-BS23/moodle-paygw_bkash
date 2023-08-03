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
 * Process payment of bkash.
 *
 * @package    paygw_bkash
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_payment\helper;
use paygw_bkash\bkash_helper;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

require_login();

global $CFG, $USER, $DB;

$courseid = required_param("courseid", PARAM_INT);
$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);

// From Create payment API Response.
$status = required_param('status', PARAM_TEXT);
$paymentidparam = required_param('paymentID', PARAM_TEXT);

$config = (object)helper::get_gateway_configuration($component, $paymentarea, $itemid, 'bkash');
$payable = helper::get_payable($component, $paymentarea, $itemid);
$surcharge = helper::get_gateway_surcharge('bkash');

if ($status == 'failure') {
    // Redirect to frontend url with failure status.

    redirect(new moodle_url('/'), get_string('paymentfailed', 'paygw_bkash'));

} else if ($status == 'cancel') {

    redirect(new moodle_url('/'), get_string('paymentcancelled', 'paygw_bkash'));

} else {
    // Success status.
    $bkashhelper = new bkash_helper(
        $config->username,
        $config->password,
        $config->appkey,
        $config->appsecret,
        $config->paymentmodes
    );
    // Get response after execute payment API.
    $response = $bkashhelper->execute_payment($paymentidparam);

    $transactiondata = json_decode($response, true);

    if (array_key_exists("statusCode", $transactiondata) && $transactiondata['statusCode'] != '0000') {
        // Redirect to frontend url with failure status.
        // Case for insufficient balance.
        redirect(new moodle_url('/'), get_string('insufficient_balance', 'paygw_bkash'));
    } else if (array_key_exists("errorCode", $transactiondata)) {
        // If execute api failed to response.

        redirect(new moodle_url('/'), get_string('paymentfailed', 'paygw_bkash'));
    } else if (array_key_exists("message", $transactiondata)) {
        redirect(new moodle_url('/'), get_string('paymentfailed', 'paygw_bkash'));
    }

    // Execution payment successful.
    // TODO: Redirect to frontend url with success status after saving data.
    $data = new stdClass();

    $data->userid = $USER->id;
    $data->txn_id = $transactiondata['trxID'];
    $data->payment_id = $transactiondata['paymentID'];
    $data->payer_reference = $transactiondata['payerReference'];
    $data->amount = $transactiondata['amount'];
    $data->currency = $transactiondata['currency'];
    $data->customer_msisdn = $transactiondata['customerMsisdn'];
    $data->payment_execute_time = $transactiondata['paymentExecuteTime'];
    $data->transaction_status = $transactiondata['transactionStatus'];
    $data->intent = $transactiondata['intent'];
    $data->merchant_invoice_number = $transactiondata['merchantInvoiceNumber'];
    $data->component = $component;
    $data->itemid = $itemid;
    $data->paymentarea = $paymentarea;
    $data->timeupdated = time();

    $DB->insert_record('paygw_bkash_log', $data);

    // Deliver course.
    $payable = helper::get_payable($component, $paymentarea, $itemid);
    $cost = helper::get_rounded_cost($payable->get_amount(),
        $payable->get_currency(),
        helper::get_gateway_surcharge('bkash'));
    $paymentid = helper::save_payment(
        $payable->get_account_id(),
        $component,
        $paymentarea,
        $itemid,
        $USER->id,
        $cost,
        $payable->get_currency(),
        'bkash'
    );
    helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $USER->id);
    // Find redirection.
    $url = new moodle_url('/');
    // Method only exists in 3.11+.
    if (method_exists('\core_payment\helper', 'get_success_url')) {
        $url = helper::get_success_url($component, $paymentarea, $itemid);
    } else if ($component == 'enrol_fee' && $paymentarea == 'fee') {
        $courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);
        if (!empty($courseid)) {
            $url = course_get_url($courseid);
        }

    }
    redirect($url, get_string('paymentsuccessful', 'paygw_bkash'), 0, 'success');
}

