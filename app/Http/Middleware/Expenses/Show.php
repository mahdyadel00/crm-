<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use App\Permissions\ProjectPermissions;
use Closure;
use Log;

class Show {

    //vars
    protected $projectpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ProjectPermissions $projectpermissions) {
        $this->projectpermissions = $projectpermissions;
    }

    /**
     * This middleware does the following
     *   1. validates that the expense exists
     *   2. checks users permissions to [view] the expense
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
            abort(404);
        }

        //team: does user have permission view expenses
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_expenses >= 1) {
                //own
                if (auth()->user()->role->role_expenses_scope == 'own') {
                    if ($expense->expense_creatorid == auth()->id()) {
                        return $next($request);
                    }
                }
                //global
                if (auth()->user()->role->role_expenses_scope == 'global') {

                    return $next($request);
                }
            }
        }

        //client: does user have permission view expenses
        if (auth()->user()->is_client) {
            if ($this->projectpermissions->check('expenses-view', $expense->expense_projectid)) {
                if ($expense->expense_clientid == auth()->user()->clientid) {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][expenses][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'expense id' => $expense_id ?? '']);
        abort(403);
    }
}
