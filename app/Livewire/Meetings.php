<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Meeting;

class Meetings extends Component
{
    public $title, $description, $meeting_time;

    public function create()
    {
        $this->validate([
            'title' => 'required',
            'meeting_time' => 'required|date',
        ]);

        Meeting::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'meeting_time' => $this->meeting_time,
        ]);

        $this->reset(['title', 'description', 'meeting_time']);
    }

    public function render()
    {
        if (auth()->user()->role === 'manager') {
            $meetings = Meeting::latest()->get();
        } else {
            $meetings = Meeting::where('user_id', auth()->user()->manager_id)->latest()->get();
        }

        return view('livewire.meetings', compact('meetings'));
    }
}
