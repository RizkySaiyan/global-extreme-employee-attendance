<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityCompanyOfficeProperty;
use App\Parser\Component\CompanyOfficeParser;

class CompanyOffice extends BaseModel
{
    use HasActivityCompanyOfficeProperty;

    protected $table = 'component_company_offices';

    public $parserClass = CompanyOfficeParser::class;

    protected $guarded = ['id'];
    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'component_company_office_departments', 'companyOfficeId', 'departmentId');
    }

    /** FUNCTION */

    public function delete()
    {
        $this->officeDepartments()->delete();

        return parent::delete();
    }

    /** RELATIONSHIPS */

    public function officeDepartments()
    {
        return $this->hasMany(CompanyOfficeDepartment::class, 'companyOfficeId');
    }
}
