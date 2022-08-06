<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToModel, WithStartRow, WithHeadingRow, WithValidation, SkipsOnFailure {

    use Importable, SkipsFailures;
    
    private $rows = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row) {

        ++$this->rows;
        
        return new Lead([
            'lead_importid' => request('import_ref'),
            'lead_firstname' => $row['firstname'] ?? '',
            'lead_lastname' => $row['lastname'] ?? '',
            'lead_email' => $row['email'] ?? '',
            'lead_title' => $row['title'] ?? '',
            'lead_value' => $row['value'] ?? '',
            'lead_phone' => $row['telephone'] ?? '',
            'lead_source' => $row['source'] ?? '',
            'lead_company_name' => $row['companyname'] ?? '',
            'lead_job_position' => $row['jobposition'] ?? '',
            'lead_street' => $row['street'] ?? '',
            'lead_city' => $row['city'] ?? '',
            'lead_state' => $row['state'] ?? '',
            'lead_zip' => $row['zipcode'] ?? '',
            'lead_country' => $row['country'] ?? '',
            'lead_website' => $row['website'] ?? '',
            'lead_description' => $row['description'] ?? '',
            'lead_custom_field_1' => $row['lead_custom_field_1'] ?? '',
            'lead_custom_field_2' => $row['lead_custom_field_2'] ?? '',
            'lead_custom_field_3' => $row['lead_custom_field_3'] ?? '',
            'lead_custom_field_4' => $row['lead_custom_field_4'] ?? '',
            'lead_custom_field_5' => $row['lead_custom_field_5'] ?? '',
            'lead_custom_field_6' => $row['lead_custom_field_6'] ?? '',
            'lead_custom_field_7' => $row['lead_custom_field_7'] ?? '',
            'lead_custom_field_8' => $row['lead_custom_field_8'] ?? '',
            'lead_custom_field_9' => $row['lead_custom_field_9'] ?? '',
            'lead_custom_field_10' => $row['lead_custom_field_10'] ?? '',
            'lead_custom_field_11' => $row['lead_custom_field_11'] ?? '',
            'lead_custom_field_12' => $row['lead_custom_field_12'] ?? '',
            'lead_custom_field_13' => $row['lead_custom_field_13'] ?? '',
            'lead_custom_field_14' => $row['lead_custom_field_14'] ?? '',
            'lead_custom_field_15' => $row['lead_custom_field_15'] ?? '',
            'lead_custom_field_16' => $row['lead_custom_field_16'] ?? '',
            'lead_custom_field_17' => $row['lead_custom_field_17'] ?? '',
            'lead_custom_field_18' => $row['lead_custom_field_18'] ?? '',
            'lead_custom_field_19' => $row['lead_custom_field_19'] ?? '',
            'lead_custom_field_20' => $row['lead_custom_field_20'] ?? '',
            'lead_custom_field_21' => $row['lead_custom_field_21'] ?? '',
            'lead_custom_field_22' => $row['lead_custom_field_22'] ?? '',
            'lead_custom_field_23' => $row['lead_custom_field_23'] ?? '',
            'lead_custom_field_24' => $row['lead_custom_field_24'] ?? '',
            'lead_custom_field_25' => $row['lead_custom_field_25'] ?? '',
            'lead_custom_field_26' => $row['lead_custom_field_26'] ?? '',
            'lead_custom_field_27' => $row['lead_custom_field_27'] ?? '',
            'lead_custom_field_28' => $row['lead_custom_field_28'] ?? '',
            'lead_custom_field_29' => $row['lead_custom_field_29'] ?? '',
            'lead_custom_field_30' => $row['lead_custom_field_30'] ?? '',
            'lead_custom_field_31' => $row['lead_custom_field_31'] ?? '',
            'lead_custom_field_32' => $row['lead_custom_field_32'] ?? '',
            'lead_custom_field_33' => $row['lead_custom_field_33'] ?? '',
            'lead_custom_field_34' => $row['lead_custom_field_34'] ?? '',
            'lead_custom_field_35' => $row['lead_custom_field_35'] ?? '',
            'lead_custom_field_36' => $row['lead_custom_field_36'] ?? '',
            'lead_custom_field_37' => $row['lead_custom_field_37'] ?? '',
            'lead_custom_field_38' => $row['lead_custom_field_38'] ?? '',
            'lead_custom_field_39' => $row['lead_custom_field_39'] ?? '',
            'lead_custom_field_40' => $row['lead_custom_field_40'] ?? '',
            'lead_custom_field_41' => $row['lead_custom_field_41'] ?? '',
            'lead_custom_field_42' => $row['lead_custom_field_42'] ?? '',
            'lead_custom_field_43' => $row['lead_custom_field_43'] ?? '',
            'lead_custom_field_44' => $row['lead_custom_field_44'] ?? '',
            'lead_custom_field_45' => $row['lead_custom_field_45'] ?? '',
            'lead_custom_field_46' => $row['lead_custom_field_46'] ?? '',
            'lead_custom_field_47' => $row['lead_custom_field_47'] ?? '',
            'lead_custom_field_48' => $row['lead_custom_field_48'] ?? '',
            'lead_custom_field_49' => $row['lead_custom_field_49'] ?? '',
            'lead_custom_field_50' => $row['lead_custom_field_50'] ?? '',
            'lead_custom_field_51' => $row['lead_custom_field_51'] ?? '',
            'lead_custom_field_52' => $row['lead_custom_field_52'] ?? '',
            'lead_custom_field_53' => $row['lead_custom_field_53'] ?? '',
            'lead_custom_field_54' => $row['lead_custom_field_54'] ?? '',
            'lead_custom_field_55' => $row['lead_custom_field_55'] ?? '',
            'lead_custom_field_56' => $row['lead_custom_field_56'] ?? '',
            'lead_custom_field_57' => $row['lead_custom_field_57'] ?? '',
            'lead_custom_field_58' => $row['lead_custom_field_58'] ?? '',
            'lead_custom_field_59' => $row['lead_custom_field_59'] ?? '',
            'lead_custom_field_60' => $row['lead_custom_field_60'] ?? '',
            'lead_custom_field_61' => $row['lead_custom_field_61'] ?? '',
            'lead_custom_field_62' => $row['lead_custom_field_62'] ?? '',
            'lead_custom_field_63' => $row['lead_custom_field_63'] ?? '',
            'lead_custom_field_64' => $row['lead_custom_field_64'] ?? '',
            'lead_custom_field_65' => $row['lead_custom_field_65'] ?? '',
            'lead_custom_field_66' => $row['lead_custom_field_66'] ?? '',
            'lead_custom_field_67' => $row['lead_custom_field_67'] ?? '',
            'lead_custom_field_68' => $row['lead_custom_field_68'] ?? '',
            'lead_custom_field_69' => $row['lead_custom_field_69'] ?? '',
            'lead_custom_field_70' => $row['lead_custom_field_70'] ?? '',
            'lead_custom_field_71' => $row['lead_custom_field_71'] ?? '',
            'lead_custom_field_72' => $row['lead_custom_field_72'] ?? '',
            'lead_custom_field_73' => $row['lead_custom_field_73'] ?? '',
            'lead_custom_field_74' => $row['lead_custom_field_74'] ?? '',
            'lead_custom_field_75' => $row['lead_custom_field_75'] ?? '',
            'lead_custom_field_76' => $row['lead_custom_field_76'] ?? '',
            'lead_custom_field_77' => $row['lead_custom_field_77'] ?? '',
            'lead_custom_field_78' => $row['lead_custom_field_78'] ?? '',
            'lead_custom_field_79' => $row['lead_custom_field_79'] ?? '',
            'lead_custom_field_80' => $row['lead_custom_field_80'] ?? '',
            'lead_custom_field_81' => $row['lead_custom_field_81'] ?? '',
            'lead_custom_field_82' => $row['lead_custom_field_82'] ?? '',
            'lead_custom_field_83' => $row['lead_custom_field_83'] ?? '',
            'lead_custom_field_84' => $row['lead_custom_field_84'] ?? '',
            'lead_custom_field_85' => $row['lead_custom_field_85'] ?? '',
            'lead_custom_field_86' => $row['lead_custom_field_86'] ?? '',
            'lead_custom_field_87' => $row['lead_custom_field_87'] ?? '',
            'lead_custom_field_88' => $row['lead_custom_field_88'] ?? '',
            'lead_custom_field_89' => $row['lead_custom_field_89'] ?? '',
            'lead_custom_field_90' => $row['lead_custom_field_90'] ?? '',
            'lead_custom_field_91' => $row['lead_custom_field_91'] ?? '',
            'lead_custom_field_92' => $row['lead_custom_field_92'] ?? '',
            'lead_custom_field_93' => $row['lead_custom_field_93'] ?? '',
            'lead_custom_field_94' => $row['lead_custom_field_94'] ?? '',
            'lead_custom_field_95' => $row['lead_custom_field_95'] ?? '',
            'lead_custom_field_96' => $row['lead_custom_field_96'] ?? '',
            'lead_custom_field_97' => $row['lead_custom_field_97'] ?? '',
            'lead_custom_field_98' => $row['lead_custom_field_98'] ?? '',
            'lead_custom_field_99' => $row['lead_custom_field_99'] ?? '',
            'lead_custom_field_100' => $row['lead_custom_field_100'] ?? '',
            'lead_custom_field_101' => $row['lead_custom_field_101'] ?? '',
            'lead_custom_field_102' => $row['lead_custom_field_102'] ?? '',
            'lead_custom_field_103' => $row['lead_custom_field_103'] ?? '',
            'lead_custom_field_104' => $row['lead_custom_field_104'] ?? '',
            'lead_custom_field_105' => $row['lead_custom_field_105'] ?? '',
            'lead_custom_field_106' => $row['lead_custom_field_106'] ?? '',
            'lead_custom_field_107' => $row['lead_custom_field_107'] ?? '',
            'lead_custom_field_108' => $row['lead_custom_field_108'] ?? '',
            'lead_custom_field_109' => $row['lead_custom_field_109'] ?? '',
            'lead_custom_field_110' => $row['lead_custom_field_110'] ?? '',
            'lead_custom_field_111' => $row['lead_custom_field_111'] ?? '',
            'lead_custom_field_112' => $row['lead_custom_field_112'] ?? '',
            'lead_custom_field_113' => $row['lead_custom_field_113'] ?? '',
            'lead_custom_field_114' => $row['lead_custom_field_114'] ?? '',
            'lead_custom_field_115' => $row['lead_custom_field_115'] ?? '',
            'lead_custom_field_116' => $row['lead_custom_field_116'] ?? '',
            'lead_custom_field_117' => $row['lead_custom_field_117'] ?? '',
            'lead_custom_field_118' => $row['lead_custom_field_118'] ?? '',
            'lead_custom_field_119' => $row['lead_custom_field_119'] ?? '',
            'lead_custom_field_120' => $row['lead_custom_field_120'] ?? '',
            'lead_custom_field_121' => $row['lead_custom_field_121'] ?? '',
            'lead_custom_field_122' => $row['lead_custom_field_122'] ?? '',
            'lead_custom_field_123' => $row['lead_custom_field_123'] ?? '',
            'lead_custom_field_124' => $row['lead_custom_field_124'] ?? '',
            'lead_custom_field_125' => $row['lead_custom_field_125'] ?? '',
            'lead_custom_field_126' => $row['lead_custom_field_126'] ?? '',
            'lead_custom_field_127' => $row['lead_custom_field_127'] ?? '',
            'lead_custom_field_128' => $row['lead_custom_field_128'] ?? '',
            'lead_custom_field_129' => $row['lead_custom_field_129'] ?? '',
            'lead_custom_field_130' => $row['lead_custom_field_130'] ?? '',
            'lead_custom_field_131' => $row['lead_custom_field_131'] ?? '',
            'lead_custom_field_132' => $row['lead_custom_field_132'] ?? '',
            'lead_custom_field_133' => $row['lead_custom_field_133'] ?? '',
            'lead_custom_field_134' => $row['lead_custom_field_134'] ?? '',
            'lead_custom_field_135' => $row['lead_custom_field_135'] ?? '',
            'lead_custom_field_136' => $row['lead_custom_field_136'] ?? '',
            'lead_custom_field_137' => $row['lead_custom_field_137'] ?? '',
            'lead_custom_field_138' => $row['lead_custom_field_138'] ?? '',
            'lead_custom_field_139' => $row['lead_custom_field_139'] ?? '',
            'lead_custom_field_140' => $row['lead_custom_field_140'] ?? '',
            'lead_custom_field_141' => $row['lead_custom_field_141'] ?? '',
            'lead_custom_field_142' => $row['lead_custom_field_142'] ?? '',
            'lead_custom_field_143' => $row['lead_custom_field_143'] ?? '',
            'lead_custom_field_144' => $row['lead_custom_field_144'] ?? '',
            'lead_custom_field_145' => $row['lead_custom_field_145'] ?? '',
            'lead_custom_field_146' => $row['lead_custom_field_146'] ?? '',
            'lead_custom_field_147' => $row['lead_custom_field_147'] ?? '',
            'lead_custom_field_148' => $row['lead_custom_field_148'] ?? '',
            'lead_custom_field_149' => $row['lead_custom_field_149'] ?? '',
            'lead_custom_field_150' => $row['lead_custom_field_150'] ?? '',            
            'lead_creatorid' => auth()->id(),
            'lead_created' => now(),
            'lead_status' => request('lead_status'),
        ]);
    }

    public function rules(): array
    {
        return [
            'firstname' => [
                'required',
            ],
            'lastname' => [
                'required',
            ],
            'title' => [
                'required',
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
