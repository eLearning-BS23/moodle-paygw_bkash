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
 * Privacy Subsystem implementation for paygw_bkash.
 *
 * @package    paygw_bkash
 * @category   privacy
 * @copyright  2022 Brain station 23 Ltd. <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_bkash\privacy;

use coding_exception;
use context;
use core_privacy\local\request\writer;
use dml_exception;
use stdClass;

/**
 * Privacy Subsystem implementation for paygw_bkash.
 *
 * @copyright  2022 Brain station 23 Ltd. <sales@brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class provider implements \core_privacy\local\metadata\null_provider, \core_payment\privacy\paygw_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Export all user data for the specified payment record, and the given context.
     *
     * @param context $context
     * @param array $subcontext
     * @param stdClass $payment
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function export_payment_data(context $context, array $subcontext, stdClass $payment) {
        global $DB;

        $subcontext[] = get_string('gatewayname', 'paygw_bkash');
        $record = $DB->get_record('paygw_bkash', ['paymentid' => $payment->id]);

        $data = (object) [
            'orderid' => $record->pp_orderid,
        ];
        writer::with_context($context)->export_data(
            $subcontext,
            $data
        );
    }

    /**
     * Delete all user data related to the given payments.
     *
     * @param string $paymentsql
     * @param array $paymentparams
     * @return void
     * @throws dml_exception
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams) {
        global $DB;

        $DB->delete_records_select('paygw_bkash', "paymentid IN ({$paymentsql})", $paymentparams);
    }
}
