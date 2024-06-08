<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSibling extends BaseModel
{
    use HasFactory;
    // protected $table = '';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];
}
