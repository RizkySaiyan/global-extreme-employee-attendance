<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityComponentProperty;

class Department extends BaseModel
{
    // protected $table = '';
    use HasActivityComponentProperty;
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** RELATIONSHIPS */
    public function companyOfficeDepartments(){
        return $this->hasMany(CompanyOfficeDepartment::class,'departmentId');
    }

    /** FUNCTIONS */
    //overide delete method
    public function delete()
    {
        if ($this->companyOfficeDepartments()->count() > 0) {
            errDepartmentDelete();
        }

        return parent::delete();
    }
}
