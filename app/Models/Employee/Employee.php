<?php

namespace App\Models\Employee;

use App\Models\Attendance\Schedule;
use App\Models\Attendance\Timesheets;
use App\Models\BaseModel;
use App\Models\Component\CompanyOffice;
use App\Models\Component\Department;
use App\Models\Employee\Traits\HasActivityEmployeeProperty;
use App\Models\Scopes\Employee\EmployeeNonResign;
use App\Parser\Employee\EmployeeParser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Employee extends BaseModel
{
    use HasActivityEmployeeProperty;
    use HasFactory;

    // protected $table = '';
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'datetime',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = EmployeeParser::class;

    /** SCOPES */

    public function scopeFilter($query, $request)
    {

        return $query->where(function ($query) use ($request) {

            if ($this->hasSearch($request)) {
                $query->Where('name', 'LIKE', "%$request->search%")
                    ->orWhere('number', 'LIKE', "%$request->search%");
            }

            if ($request->has('role')) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('role', $request->role);
                });
            }
        });
    }

    /** RELATIONSHIPS */

    public function companyOffice()
    {
        return $this->belongsTo(CompanyOffice::class, 'companyOfficeId');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    public function siblings()
    {
        return $this->hasMany(EmployeeSibling::class, 'employeeId');
    }

    public function resign()
    {
        return $this->hasOne(EmployeeResignation::class, 'employeeId');
    }

    public function user()
    {
        return $this->hasOne(EmployeeUser::class, 'employeeId');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'employeeId');
    }

    public function attendances()
    {
        return $this->hasMany(Timesheets::class, 'employeeId');
    }

    /** FUNCTIONS */

    public function delete()
    {
        if ($this->siblings) {
            $this->siblings()->delete();
        }

        if ($this->user) {
            $this->user()->delete();
        }

        return parent::delete();
    }

    public function deleteAttendance()
    {
        $this->attendances()->delete();
    }

    public function saveUser($attributes)
    {
        return $this->user()->updateOrCreate(['employeeId' => $this->id], $attributes);
    }

    public function saveSiblings($attributes)
    {
        $user = Auth::user();
        $createdBy = [
            'createdBy' => $user?->id,
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

    /** STATIC FUNCTIONS */

    protected static function booted()
    {
        static::addGlobalScope(new EmployeeNonResign);
    }
}
