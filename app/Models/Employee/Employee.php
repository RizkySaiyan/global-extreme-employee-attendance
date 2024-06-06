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

    public function resign()
    {
        return $this->hasOne(EmployeeResignation::class, 'employeeId');
    }

    public function attendances(){
        return $this->hasMany('attendance','employeeId');
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
            unset($attributes['role']);
        }

        return $this->user()->updateOrCreate(['employeeId'=>$this->id],$attributes);
    }

    public function saveSiblings($attributes){

        $user = Auth::user();
        $createdBy = [
            'createdBy'=> $user?->id,
            'createdByName' => $user->employee?->name
        ];

        $existingIds = [];
        foreach ($attributes as $sibling) {
            if (isset($sibling['id'])) {
                $this->siblings()->where('id', $sibling['id'])->update($sibling);
                $existingIds[] = $sibling['id'];
            } else {
                $newSibling = $this->siblings()->create($sibling + $createdBy);
                $existingIds[] = $newSibling->id;
            }
        }
        $this->siblings()->whereNotIn('id', $existingIds)->delete();

        return $this->siblings;
    }

    public function deleteAttendance(){
        $this->attendances()->delete();
    }
}
