<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Models\Employee\Traits\HasActivityEmployeeProperty;
use App\Services\Constant\Employee\EmployeeUserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Employee extends BaseModel
{
    use HasActivityEmployeeProperty;

    // protected $table = '';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    /** RELATIONSHIPS */
    public function siblings()
    {
        return $this->hasMany(EmployeeSibling::class, 'employeeId');
    }
    public function user()
    {
        return $this->hasOne(EmployeeUser::class, 'employeeId');
    }


    /** FUNCTIONS */
    public function delete()
    {
        if ($this->user) {
            $this->user()->delete();
        }

        if ($this->siblings) {
            $this->siblings()->delete();
        }
        
        return parent::delete();
    }

    public function saveUser($attributes){
        $user = $this->user;
        if ($user) {
            unset($attributes['password']);
        }
        return $user->update($attributes);
    }

    public function saveSiblings($attributes){
        $siblings = $this->siblings;
        if ($siblings) { 
            $existingSiblingsIds = $siblings->pluck('id')->toArray();
            $reqSiblingIds = collect($attributes)->pluck('id')->filter()->toArray();

            $deleteSiblings = array_diff($existingSiblingsIds, $reqSiblingIds);
            $this->siblings()->whereIn('id',$deleteSiblings)->delete();
        }

        foreach ($attributes as $sibling) {
            if (isset($sibling['id'])) {
                $this->siblings()->where('id', $sibling['id'])->update($sibling);
            } else {
                $this->siblings()->create($sibling);
            }
        }
        return $siblings;
    }
}
