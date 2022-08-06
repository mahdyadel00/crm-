<?php

/** -------------------------------------------------------------------------------------------------
 * Email Bills Cronob
 * Send invoice/estimate emails that need to generate a PDF file and attach it.
 * These emails are limited to a smaller number at a time (e.g. 5)
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Mail\SendQueued;
use App\Repositories\EstimateGeneratorRepository;
use App\Repositories\InvoiceGeneratorRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;
use PDF;

class EmailBillsCron {

    public function __invoke(
        InvoiceGeneratorRepository $invoicegenerator,
        EstimateGeneratorRepository $estimategenerator
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //set the language to be used in this cronjon session
        $this->setLanguage();

        /**
         * Generate PDF invoices and email them out
         *   - These emails are being sent every minute. You can set a higher or lower sending limit.
         *   - Note: processing PDF files takes some time and if you set too high a limit, the process
         *    could timeout
         */
        //Get the emails marked as [pdf] and [invoice]
        $limit = 5;
        if ($emails = \App\Models\EmailQueue::Where('emailqueue_type', 'pdf')
            ->whereIn('emailqueue_pdf_resource_type', ['invoice', 'estimate'])->where('emailqueue_status', 'new')->take($limit)->get()) {

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($emails as $email) {
                $email->update([
                    'emailqueue_status' => 'processing',
                    'emailqueue_started_at' => now(),
                ]);
            }

            //process each email in the batch
            foreach ($emails as $email) {

                //id of original bill
                $bill_id = $email->emailqueue_pdf_resource_id;

                //[invoice]
                if ($email->emailqueue_pdf_resource_type == 'invoice') {
                    if (!$payload = $invoicegenerator->generate($bill_id)) {
                        Log::error("the invoice could not be generated", ['process' => '[cronjob][email-bills]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'bill_id' => $bill_id]);
                    }
                }

                //[estimate]
                if ($email->emailqueue_pdf_resource_type == 'estimate') {
                    if (!$payload = $estimategenerator->generate($bill_id)) {
                        Log::error("the estimate could not be generated", ['process' => '[Email Bills Cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'bill_id' => $bill_id]);
                    }
                }

                //save the pdf file to disk
                $attachment = $this->savePDF($payload);

                //send email with attachement (only to a valid email address)
                if ($email->emailqueue_to != '') {
                    Mail::to($email->emailqueue_to)->send(new SendQueued($email, $attachment));

                    //log email
                    $log = new \App\Models\EmailLog();
                    $log->emaillog_email = $email->emailqueue_to;
                    $log->emaillog_subject = $email->emailqueue_subject;
                    $log->emaillog_body = $email->emailqueue_message;
                    $log->emaillog_attachment = $attachment['filename'];
                    $log->save();
                }

                //delete email from the queue
                \App\Models\EmailQueue::Where('emailqueue_id', $email->emailqueue_id)->delete();

                //reset last cron run data
                \App\Models\Settings::where('settings_id', 1)
                    ->update([
                        'settings_cronjob_has_run' => 'yes',
                        'settings_cronjob_last_run' => now(),
                    ]);
            }
        }

        //[UPCOMING] update database for items marked as processing but never completed. Mark them as 'new'. Based on processing timestamp

    }

    /**
     * Render the PDF invoice and save it to disk (temp folder)
     *  @return array filename & filepath
     */
    public function savePDF($payload) {

        //set all data to arrays
        foreach ($payload as $key => $value) {
            $$key = $value;
        }

        //unique file id & directory name
        $uniqueid = Str::random(40);
        $directory = $uniqueid;

        //[invoice] pdf filename
        if ($bill->bill_type == 'invoice') {
            $filename = strtoupper(__('lang.invoice')) . '-' . $bill->formatted_bill_invoiceid . '.pdf'; //invoice_inv0001.pdf
        }

        //[estimate] pdf filename
        if ($bill->bill_type == 'estimate') {
            $filename = strtoupper(__('lang.estimate')) . '-' . $bill->formatted_bill_estimateid . '.pdf'; //estimate_est0001.pdf
        }

        //filepath
        $filepath = BASE_DIR . "/storage/temp/$directory/$filename";

        //custom fields
        $customfields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //save file
        config(['css.bill_mode' => 'pdf-mode-download']);
        $pdf = PDF::loadView('pages/bill/bill-pdf', compact('bill', 'taxrates', 'taxes', 'elements', 'lineitems', 'customfields'));

        //save file
        Storage::put("temp/$directory/$filename", $pdf->output());

        //return the file path
        return [
            'filename' => $filename,
            'filepath' => $filepath,
        ];

    }

    /**
     * set the language to be used by the app
     * @return void
     */
    private function setLanguage() {

        //set the language to be used in this cronjon session
        $lang = config('system.settings_system_language_default');
        if (file_exists(resource_path("lang/$lang"))) {
            \App::setLocale($lang);
        } else {
            \App::setLocale('english');
        }
    }
}