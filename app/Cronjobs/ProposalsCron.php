<?php

/** -------------------------------------------------------------------------------------------------
 * PROPOSALS
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;

class ProposalsCron {

    public function __invoke() {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        /*--------------------------------------------------------
         * Process expired proposals
         * *-----------------------------------------------------*/
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        \App\Models\Proposal::WhereIn('doc_status', ['new', 'revised'])
            ->where('doc_date_end', '<', $today)
            ->update(['doc_status' => 'expired']);

    }
}