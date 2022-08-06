<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'emailtemplate_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['emailtemplate_id'];
    const CREATED_AT = 'emailtemplate_created';
    const UPDATED_AT = 'emailtemplate_updated';

    /**
     * Parse the template body and replace shortcode {foo} with real data
     * @source http://tnt.studio/blog/email-templates-from-database
     * @usage $subject = $template->parse('subject', $data);
     * @usage $body = $template->parse('body', $data);
     * @section string 'body|subject'
     * @data array the data we want to inject/replace
     * @return object
     * */
    public function parse($section = 'body', $data) {

        //validate
        if (!is_array($data) || !in_array($section, ['body', 'subject'])) {
            return $this->emailtemplate_body;
        }

        //set the content
        if ($section == 'body') {
            $content = $this->emailtemplate_body;
        } else {
            $content = $this->emailtemplate_subject;
        }

        //parse the content and inject actual data
        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortcode, $index) = $matches;
            //if shortcode is found, replace or return as is
            if (isset($data[$index])) {
                return $data[$index];
            } else {
                return $shortcode;
            }
        }, $content);

        //return
        return $parsed;
    }

}
