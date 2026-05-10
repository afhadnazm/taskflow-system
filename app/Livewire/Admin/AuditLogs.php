<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;

class AuditLogs extends Component
{
    public string $action = '';
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function render()
    {
        $query = AuditLog::with(['actor', 'targetUser'])->latest();

        if ($this->action !== '') {
            $query->where('action', $this->action);
        }

        if ($this->search !== '') {
            $search = '%' . $this->search . '%';

            $query->where(function ($query) use ($search) {
                $query->where('description', 'like', $search)
                    ->orWhereHas('actor', function ($query) use ($search) {
                        $query->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    })
                    ->orWhereHas('targetUser', function ($query) use ($search) {
                        $query->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        return view('livewire.admin.audit-logs', [
            'logs' => $query->take(100)->get(),
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
        ])->layout('layouts.app');
    }
}
