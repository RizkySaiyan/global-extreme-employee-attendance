<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Requests\Employee\ResetPasswordRequest;
use App\Jobs\DeleteEmployeeAttendance;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeUser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Employee\EmployeeUserRole;
use App\Services\Constant\Path;
use App\Services\Number\Generator\EmployeeNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeAlgo
{

    public function __construct(public ?Employee $employee = null)
    {
    }

    public function create(EmployeeRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
   
                $this->employeeResignCheck($request);
                $this->validateEmail($request);
                $this->employee = $this->saveEmployee($request);

                if ($request->has('siblings')) {
                    $siblings = $this->saveSiblings($request);
                    if (!$siblings) {
                       errEmployeeSiblingsSave();
                    }
                }

                $employeeUser = $this->saveEmployeeUser($request);
                if (!$employeeUser) {
                    errEmployeeUserSave();
                }

                $this->employee->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Enter new Employee :  {$this->employee->name}  [{$this->employee->id}]");
            });
            return success($this->employee);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function update(Request $request){
        try {
            DB::transaction(function() use($request){

                $this->employee->setOldActivityPropertyAttributes(ActivityAction::UPDATE);
                
                $this->validateEmail($request);
                $this->saveEmployee($request);

                if($request->has('siblings')) {
                    $siblings = $this->saveSiblings($request);
                    if (!$siblings) {
                       errEmployeeSiblingsSave();
                    }
                }

                $employeeUser = $this->saveEmployeeUser($request);
                if (!$employeeUser) {
                    errEmployeeUserSave();
                }

                $this->employee->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Update Employee :  {$this->employee->name}  [{$this->employee->id}]");
            });
            return success($this->employee->fresh());
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete(){
        try { 
            DB::transaction(function (){
                $this->employee->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                // activate when employee has attendance
                // DeleteEmployeeAttendance::dispatch($this->employee);

                $this->employee->delete();
                
                $this->employee->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete employee : {$this->employee->name} [{$this->employee->id}]");
            });
            return success();
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function resign(Request $request){
        try {
            DB::transaction(function() use($request){
                if ($this->employee->resign){
                    errEmployeeResignExist();
                }

                $createdBy = [
                    'createdBy' => $this->employee->id,
                    'createdByName' => $this->employee->name
                ];

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $fileName = filename($file, 'employee'); 
                    $filePath = $file->storeAs(Path::EMPLOYEE_RESIGN, $fileName, 'public');
                }

                $this->employee->resign()->create([
                    'date' => $request->date,
                    'reason' => $request->reason,
                    'file' => $filePath
                ] + $createdBy);           
            });

            return success($this->employee);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, $id){
        try {
            DB::transaction(function () use($request, $id){
                $user = Auth::user();

                if ($id) {
                    $employee = Employee::find($id);
                    if (!$employee) {
                        return errEmployeeNotFound();
                    }
    
                    if ($employee->user->role == EmployeeUserRole::ADMIN_ID) {
                        return errEmployeeChangePassword();
                    }
    
                    $employee->user->update([
                        'password' => Hash::make($request->newPassword)
                    ]);

                } else {
                    if (!Hash::check($request->existingPassword, $user->password)) {
                        errOldPasswordNotMatch();
                    }
    
                    $user->update([
                        'password' => Hash::make($request->newPassword)
                    ]);
                }
            });
            return success();
        } 
        catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function changeRoleToAdmin(){
        try {
            DB::transaction(function (){
                $this->employee->setOldActivityPropertyAttributes(ActivityAction::UPDATE,'role');
                
                if ($this->employee->user->role == EmployeeUserRole::ADMIN_ID) {
                    errEmployeeUserAdmin();
                }

                $this->employee->user()->update([
                    'role' => EmployeeUserRole::ADMIN_ID
                ]);

                $this->employee->setActivityPropertyAttributes(ActivityAction::UPDATE,'role')
                ->saveActivity("Update Employee role to admin {$this->employee->name}, [{$this->employee->id}]");

            });
            return success($this->employee);
            
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function updateStatusResign(){
        try {
            DB::transaction(function (){

                $this->employee->resign()->update([
                    'isResign' => false
                ]);

            });
            success($this->employee);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */
    private function saveEmployee(Request $request){
        $user = Auth::user();
        $form = $request->except(['siblings','password']);

        if ($request->hasFile('photo')) {
            $form['photo'] = $this->processFile($request);
        }

        if($this->employee){
            $this->employee->update($form);
            $this->employee->refresh();
            return $this->employee;
        }

        $createdBy = [
            'createdBy' => $user->employeeId,
            'createdByName' => $user->employee->name
        ];

        return Employee::create($form + [
            'number' => EmployeeNumber::generate()
        ] + $createdBy);
    }

    private function employeeResignCheck($request){
        $existingUser = EmployeeUser::whereHas('employee', function($query) use($request){
            $query->where('name',$request->name);
        })->where('email',$request->email)->first();

        if ($existingUser) {
            $resign = $existingUser->employee->resign;
            if($resign && Carbon::parse($resign->date)->diffInYears(now()) < 1){
               return errEmployeeResign();
            }
            $existingUser->delete();
        }
    }

    private function validateEmail(Request $request) {
        $query = EmployeeUser::where('email',$request->email);
        
        if ($this->employee) {
            $query->where('employeeId','!=',$this->employee->id);
        }
        
        $email = $query->first();
        
        if ($email) {
            errEmployeeEmailUnique();
        }
    }

    private function saveEmployeeUser(Request $request){
        $form = [];
        if ($request->has('password')) {
            $form['password'] = Hash::make($request->password);
        }
        if(!$this->employee->user){
            $form['role'] = EmployeeUserRole::USER_ID;
        }
        $form['email'] = $request->email;

        return $this->employee->saveUser($form);
    }

    private function saveSiblings(Request $request){
        return $this->employee->saveSiblings($request->siblings);
    }


    private function processFile(Request $request)
    {
        $filePath = null;
    
        if ($this->employee) {
            $oldPhotoPath = $this->employee->photo;
            
            if ($request->hasFile('photo')) {
                if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }

                $file = $request->file('photo');
                $fileName = filename($file, 'employee');
                $filePath = $file->storeAs(Path::EMPLOYEE, $fileName, 'public'); 
            } else {
                $filePath = $oldPhotoPath; 
            }
        } else {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = filename($file, 'employee'); 
                $filePath = $file->storeAs(Path::EMPLOYEE, $fileName, 'public');
            }
        }
    
        return $filePath;
    }
}
