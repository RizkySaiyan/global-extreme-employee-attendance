<?php

namespace App\Services\Number\Generator;

use App\Models\Employee\Employee;
use App\Services\Number\BaseNumber;
use Illuminate\Database\Eloquent\Model;

class EmployeeNumber extends BaseNumber
{
    /**
     * @var string
     */
    protected static string $prefix = "/GX/Employee/";

    /**
     * @var Model|string|null
     */
    protected Model|string|null $model = Employee::class;

    public static function generate(): string
    {
        $date = now();
    
        $numberPrefix = $date->year . "/" . str_pad($date->month, 2, '0', STR_PAD_LEFT) . static::$prefix;
    
        $static = new static();
    
        $latestEmployee = $static->model::whereMonth('createdAt', $date->month)
            ->whereYear('createdAt', $date->year)
            ->orderBy('createdAt', 'desc')
            ->first();
    
        if ($latestEmployee) {
            $latestNum = $latestEmployee->number;
            $explode = explode('/', $latestNum);
            $increment = (int)end($explode) + 1;
            $next = str_pad($increment, 6, '0', STR_PAD_LEFT);
        } else {
            $next = str_pad(1, 6, '0', STR_PAD_LEFT);
        }

        return $numberPrefix . $next;
    }
}
