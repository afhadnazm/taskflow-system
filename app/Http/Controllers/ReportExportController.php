<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

class ReportExportController extends Controller
{
    public function export()
    {
        $user = auth()->user();

        abort_unless(in_array($user->role, ['manager', 'admin'], true), 403);

        $employees = User::where('role', 'employee')
            ->when($user->role === 'manager', function ($query) use ($user) {
                $query->where('manager_id', $user->id);
            })
            ->orderBy('name')
            ->get();

        $tasksByEmployee = Task::whereIn('assigned_to', $employees->pluck('id'))
            ->get()
            ->groupBy('assigned_to');

        $data = $employees->map(function ($employee) use ($tasksByEmployee) {
            $tasks = $tasksByEmployee->get($employee->id, collect());
            $total = $tasks->count();
            $done = $tasks->where('status', 'done')->count();
            $progress = $tasks->where('status', 'in_progress')->count();
            $todo = $tasks->where('status', 'todo')->count();

            return [
                'name' => $employee->name,
                'total' => $total,
                'done' => $done,
                'progress' => $progress,
                'todo' => $todo,
                'percentage' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        });

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Employee', 'Total Tasks', 'Completed', 'In Progress', 'To Do', 'Completion %']);

            foreach ($data as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['total'],
                    $row['done'],
                    $row['progress'],
                    $row['todo'],
                    $row['percentage'] . '%',
                ]);
            }

            fclose($handle);
        }, 'report.csv');
    }
}
