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
 * Redirects to the bkash checkout for payment
 *
 * @package    paygw_bkash
 * @copyright  2021 Brain station 23 ltd.
 * @author     Brain station 23 ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_bkash;

use coding_exception;
use core_payment\form\account_gateway;
use stdClass;

class gateway extends \core_payment\gateway {

    /**
     * @inheritDoc
     */
    public static function get_supported_currencies(): array {
        return [ 'AUD', 'AED', 'BRL', 'BDT' , 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'INR', 'JPY',
            'MXN', 'MYR', 'NOK', 'NZD', 'PKR', 'PHP', 'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'USD'];
    }

    /**
     * @inheritDoc
     */
    public static function add_configuration_to_gateway_form(account_gateway $form): void {
        $mform = $form->get_mform();

        $mform->addElement('text', 'username', get_string('username', 'paygw_bkash'));
        $mform->setType('username', PARAM_TEXT);
        $mform->addHelpButton('username', 'username', 'paygw_bkash');

        $mform->addElement('text', 'password', get_string('password', 'paygw_bkash'));
        $mform->setType('password', PARAM_TEXT);
        $mform->addHelpButton('password', 'password', 'paygw_bkash');

        $mform->addElement('text', 'appkey', get_string('appkey', 'paygw_bkash'));
        $mform->setType('appkey', PARAM_TEXT);
        $mform->addHelpButton('appkey', 'appkey', 'paygw_bkash');

        $mform->addElement('text', 'appsecret', get_string('appsecret', 'paygw_bkash'));
        $mform->setType('appsecret', PARAM_TEXT);
        $mform->addHelpButton('appsecret', 'appsecret', 'paygw_bkash');

        $paymentmodes = array(
            'sandbox' => get_string('paymentmodes:sandbox', 'paygw_bkash'),
            'live' => get_string('paymentmodes:live', 'paygw_bkash'),
        );
        $mform->addElement('select', 'paymentmodes', get_string('paymentmodes', 'paygw_bkash'), $paymentmodes);
        $mform->setType('paymentmodes', PARAM_TEXT);
        $mform->setDefault('paymentmodes', 'sandbox');

    }

    /**
     * Validates the gateway configuration form.
     *
     * @param account_gateway $form
     * @param stdClass $data
     * @param array $files
     * @param array $errors form errors (passed by reference)
     * @throws coding_exception
     */
    public static function validate_gateway_form(
        account_gateway $form,
        stdClass        $data,
        array           $files,
        array           &$errors
    ): void {
        if ( !$data->enabled
            || empty($data->username) || empty($data->password)
            || empty($data->paymentmodes)
        ) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
