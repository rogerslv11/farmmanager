<?php

namespace App\Livewire\Notifications;

use Livewire\Component;

class Bell extends Component
{
    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function render()
    {
        $notifications = auth()->user()->notifications()->latest()->limit(10)->get();
        $unread = auth()->user()->unreadNotifications()->count();

        return view('livewire.notifications.bell', compact('notifications', 'unread'));
    }
}
