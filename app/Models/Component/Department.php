<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityComponentProperty;
use App\Parser\Component\CompanyOfficeParser;
use App\Parser\Component\DepartmentParser;

class Department extends BaseModel
{
    use HasActivityComponentProperty;
    
    protected $table = 'component_departments';
    
    protected $guarded = ['id'];    
    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];


    public function delete()
    {
        if ($this->companyOfficeDepartments()->count() > 0) {
            errDepartmentDelete();
        }

        return parent::delete();
    }

    /** FUNCTIONS */

    //overide delete method to prevent delete department if it has companyOffice
    /** RELATIONSHIPS */
    public function companyOfficeDepartments()
    {
        return $this->hasMany(CompanyOfficeDepartment::class, 'departmentId');
    }
}
