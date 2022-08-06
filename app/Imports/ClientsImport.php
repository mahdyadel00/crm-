<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToModel, WithStartRow, WithHeadingRow, WithValidation, SkipsOnFailure {

    use Importable, SkipsFailures;
    
    private $rows = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row) {

        ++$this->rows;
        
        return new Client([
            'client_importid' => request('import_ref'),
            'client_company_name' => $row['companyname'] ?? '',
            'client_phone' => $row['phone'] ?? '',
            'client_website' => $row['Website'] ?? '',
            'client_billing_street' => $row['billingstreet'] ?? '',
            'client_billing_city' => $row['billingcity'] ?? '',
            'client_billing_state' => $row['billingstate'] ?? '',
            'client_billing_zip' => $row['billingzipcode'] ?? '',
            'client_billing_country' => $row['billingcountry'] ?? '',
            'client_shipping_street' => $row['shippingstreet'] ?? '',
            'client_shipping_city' => $row['shippingcity'] ?? '',
            'client_shipping_state' => $row['shippingstate'] ?? '',
            'client_shipping_zip' => $row['shippingzipcode'] ?? '',
            'client_shipping_country' => $row['shippingcountry'] ?? '',
            'client_custom_field_1' => $row['customfield1'] ?? '',
            'client_custom_field_2' => $row['customfield3'] ?? '',
            'client_custom_field_3' => $row['customfield3'] ?? '',
            'client_custom_field_4' => $row['customfield4'] ?? '',
            'client_custom_field_5' => $row['customfield5'] ?? '',
            'client_custom_field_6' => $row['customfield6'] ?? '',
            'client_custom_field_7' => $row['customfield7'] ?? '',
            'client_custom_field_8' => $row['customfield8'] ?? '',
            'client_custom_field_9' => $row['customfield9'] ?? '',
            'client_custom_field_10' => $row['customfield10'] ?? '',
            'client_import_first_name' => $row['firstname'] ?? '',
            'client_import_last_name' => $row['lastname'] ?? '',
            'client_import_email' => $row['email'] ?? '',
            'client_import_job_title' => $row['jobtitle'] ?? '',
            'client_creatorid' => auth()->id(),
            'client_created' => now(),
            'client_status' => 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'companyname' => [
                'required',
            ],
            'firstname' => [
                'required',
            ],
            'lastname' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],
        ];
    }

    /**
     * we are ignoring the header and so we will start with row number (2)
     * @return int
     */
    public function startRow(): int {
        return 2;
    }


    /**
     * lets count the total imported rows
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }
}
