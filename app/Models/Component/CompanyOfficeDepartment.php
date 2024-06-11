<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityCompanyOfficeProperty;
use App\Parser\Component\CompanyOfficeParser;
use App\Services\Misc\CompanyOffice\HasCompanyOffice;

class CompanyOfficeDepartment extends BaseModel
{
    use HasActivityCompanyOfficeProperty;

    protected $table = 'component_company_office_departments';

    protected $guarded = ['id'];
    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

}
