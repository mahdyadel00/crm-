<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use Closure;
use Log;

class CreateInvoice {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] invoices
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.expenses')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //expense id
        $expense_id = $request->route('expense');

        //does the expense exist
        if ($expense_id == '' || !$expense = \App\Models\Expense::Where('expense_id', $expense_id)->first()) {
            Log::error("expense could not be found", ['process' => '[permissions][expenses][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'expense id' => $expense_id ?? '']);
            abort(404);
        }

        //check if not already invoiced
        if ($expense->expense_billing_status == 'invoiced') {
            abort(409, __('lang.expense_has_already_been_invoiced'));
        }

        //check if its a billable expense
        if ($expense->expense_billable != 'billable') {
            abort(409, __('lang.expense_not_billable'));
        }

        //frontend
        $this->fronteEnd($expense);

        //permission: does user have permission create invoices
        if (auth()->user()->role->role_invoices >= 2) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][invoices][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($expense) {

        //defaults
        config([
            'visibility.invoice_modal_expenses_payload' => true,
        ]);

        //client and project are not set
        if (!is_numeric($expense->expense_projectid) && !is_numeric($expense->expense_clientid)) {
            //show client & project drop dpwns
            config([
                'visibility.invoice_modal_client_project_fields' => true,
                'settings.expense_id' => $expense->expense_id,
            ]);
        }

        //client only has been set
        if (is_numeric($expense->expense_clientid) && !is_numeric($expense->expense_projectid)) {
            //show project only drop down
            $projects = \App\Models\Project::Where('project_clientid', $expense->expense_clientid)->get();
            config([
                'settings.clients_projects' => $projects,
                'visibility.invoice_modal_clients_projects' => true,
            ]);
            request()->merge([
                'bill_clientid' => $expense->expense_clientid,
            ]);
        }

        //client and project both have already been set
        if (is_numeric($expense->expense_projectid) && is_numeric($expense->expense_clientid)) {
            request()->merge([
                'bill_projectid' => $expense->expense_projectid,
                'bill_clientid' => $expense->expense_clientid,
            ]);
        }
    }

}