<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * It marks invoices as overdue and also send overdue reminder email
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Repositories\InvoiceRepository;
use App\Repositories\UserRepository;

class OverdueInvoicesCron {

    public function __invoke(
        UserRepository $userrepo,
        InvoiceRepository $invoicerepo
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //log that its run
        //Log::info("Cronjob has started", ['process' => '[foo-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        /*--------------------------------------------------------
         * Process invoices that are already mareked as overdue
         * *-----------------------------------------------------*/
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $invoices = \App\Models\Invoice::Where('bill_due_date', '<', $today)
            ->where('bill_overdue_reminder_sent', 'no')
            ->where('bill_status', 'overdue')
            ->take(5)->get();

        //process each one
        foreach ($invoices as $invoice) {

            //get full invoice
            if ($bills = $invoicerepo->search($invoice->bill_invoiceid)) {
                $bill = $bills->first();
            }

            //send email - only do this for invoices with an amount due
            if ($bill->invoice_balance > 0) {
                if ($user = $userrepo->getClientAccountOwner($invoice->bill_clientid)) {
                    $mail = new \App\Mail\OverdueInvoice($user, [], $bill);
                    $mail->build();
                }
            }

            //mark invoice as overdue and email sent
            $invoice->bill_overdue_reminder_sent = 'yes';
            $invoice->save();
        }

        /*--------------------------------------------------------
         * Process invoices that are not yet mareked as overdue
         * *-----------------------------------------------------*/
        //mark invoice as overdue
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $invoices = \App\Models\Invoice::Where('bill_due_date', '<', $today)
            ->where('bill_overdue_reminder_sent', 'no')
            ->whereIn('bill_status', ['due ', 'part_paid'])
            ->take(5)->get();

        //process each one
        foreach ($invoices as $invoice) {

            //get full invoice
            if ($bills = $invoicerepo->search($invoice->bill_invoiceid)) {
                $bill = $bills->first();
            }

            //send email - only do this for invoices with an amount due
            if ($bill->invoice_balance > 0) {
                if ($user = $userrepo->getClientAccountOwner($invoice->bill_clientid)) {
                    $mail = new \App\Mail\OverdueInvoice($user, [], $bill);
                    $mail->build();
                }
            }

            //mark invoice as overdue and email sent
            $invoice->bill_status = 'overdue';
            $invoice->bill_overdue_reminder_sent = 'yes';
            $invoice->save();
        }

        //reset last cron run data
        \App\Models\Settings::where('settings_id', 1)
            ->update([
                'settings_cronjob_has_run' => 'yes',
                'settings_cronjob_last_run' => now(),
            ]);

    }
}