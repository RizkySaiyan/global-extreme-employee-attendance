<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityCompanyOfficeProperty;
use App\Models\Component\Traits\HasActivityComponentProperty;

class CompanyOffice extends BaseModel
{
    // protected $table = '';
    use HasActivityCompanyOfficeProperty;
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** RELATIONSHIPS */
    public function officeDepartments(){
        return $this->hasMany(CompanyOfficeDepartment::class, 'companyOfficeId');
    }

    public function departments(){
        return $this->belongsToMany(Department::class, 'company_office_departments','companyOfficeId','departmentId');
    }

    /** FUNCTION */

    //override delete function to delete all record CompanyOfficeDepartment 
    public function delete(){
            
        $this->officeDepartments()->delete();

        return parent::delete();
    }
}
