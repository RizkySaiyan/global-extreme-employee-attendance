<?php

namespace App\Models\Component;

use App\Models\BaseModel;
use App\Models\Component\Traits\HasActivityComponentProperty;

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



    /** FUNCTIONS */

    public function delete()
    {
        $this->load('companyOfficeDepartments');

        if ($this->companyOfficeDepartments->isNotEmpty()) {
            errDepartmentDelete();
        }

        return parent::delete();
    }

    /** RELATIONSHIPS */

    public function companyOfficeDepartments()
    {
        return $this->hasMany(CompanyOfficeDepartment::class, 'departmentId');
    }
}
