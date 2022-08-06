<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @foo    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Foo;

use App\Models\Foo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RecurringTasksRepository {

    /**
     * The leads repository instance.
     */
    protected $foo;

    /**
     * Inject dependecies
     */
    public function __construct(Foo $foo) {
        $this->foo = $foo;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object foos collection
     */
    public function search($id = '', $data = []) {

        $foos = $this->foo->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //joins
        $foos->leftJoin('users', 'users.id', '=', 'foos.foo_creatorid');

        //join: multiple condition
        $foos->leftJoin('items', function ($join) {
            $join->on('items.item_type', '=', DB::raw("'foo'"));
            $join->on('items.foo_id', '=', 'foos.foo_id');
            $join->on('items.item_userid', '=', DB::raw(auth()->id()));
        });

        // all client fields
        $foos->selectRaw('*');

        //count all items
        $foos->selectRaw("(SELECT COUNT(*)
                                      FROM items
                                      WHERE item_fooid = foos.foo_id)
                                      AS count_all_items");

        //sum all items
        $foos->selectRaw("(SELECT COALESCE(SUM(item_amount), 0.00)
                                      FROM items
                                      WHERE item_fooid = foos.foo_id
                                      AND item_status = 'overdue')
                                      AS sum_items");

        //default where
        $foos->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_foo_id')) {
            $foos->where('foo_id', request('filter_foo_id'));
        }
        if (is_numeric($id)) {
            $foos->where('foo_id', $id);
        }

        //apply filters
        if ($data['apply_filters']) {

            //filter foo id
            if (request()->filled('filter_foo_id')) {
                $foos->where('foo_id', request('filter_foo_id'));
            }

            //filter: start date (start)
            if (request()->filled('filter_start_date_start')) {
                $foos->whereDate('foo_date_start', '>=', request('filter_start_date_start'));
            }

            //filter status
            if (is_array(request('filter_foo_status')) && !empty(array_filter(request('filter_foo_status')))) {
                $foos->whereIn('foo_status', request('filter_foo_status'));
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $foos->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $foos->where(function ($query) {
                $query->orWhere('foo_surname', '=', request('search_query'));
                $query->orWhere('foo_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('foo_date', '=', date('Y-m-d', strtotime(request('search_query'))));
                if (is_numeric(request('search_query'))) {
                    $query->orWhere('foo_amount', '=', request('search_query'));
                }
                $query->orWhereHas('tags', function ($q) {
                    $q->where('tag_title', 'LIKE', '%' . request('search_query') . '%');
                });

            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('foos', request('orderby'))) {
                $foos->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $foos->orderBy('foo_name', 'asc');
        }

        //eager load
        $foos->with([
            'tags',
        ]);

        // Get the results and return them.
        return $foos->paginate(config('system.settings_system_pagination_limits'));
    }
}