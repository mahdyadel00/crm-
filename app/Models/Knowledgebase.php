<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Knowledgebase extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'knowledgebase';
    protected $primaryKey = 'knowledgebase_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['knowledgebase_id'];
    const CREATED_AT = 'knowledgebase_created';
    const UPDATED_AT = 'knowledgebase_updated';

    /**
     * relatioship business rules:
     *         - the Category can have many Articles
     *         - the Articles belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'knowledgebase_categoryid', 'category_id');
    }

}
