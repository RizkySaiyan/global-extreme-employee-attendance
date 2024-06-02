<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Models\GetOrPaginate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class EmployeeUser extends User implements JWTSubject
{
    use SoftDeletes;
    use GetOrPaginate;
    use Notifiable;
    // protected $table = '';

    protected $guarded = ['id'];
    // Custom date times column
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    // Add any additional properties or methods you need for your User model
    
    /** RELATIONSHIP */
    public function employee(){
        return $this->belongsTo(Employee::class, 'employeeId');
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
