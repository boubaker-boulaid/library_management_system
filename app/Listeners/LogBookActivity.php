<?php

namespace App\Listeners;

use App\Events\BookCreated;
use App\Events\BookDeleted;
use App\Events\BookUpdated;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogBookActivity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $action = '';
        if ($event instanceof BookCreated) {
            $action = 'created';
        } elseif ($event instanceof BookUpdated) {
            $action = 'updated';
        } elseif ($event instanceof BookDeleted) {
            $action = 'deleted';
        }

        if ($action) {
            $details = null;
            if ($action === 'updated') {
                $details = $event->book->getChanges();
            } elseif ($action === 'created') {
                $details = $event->book->only([
                    'designation', 'description', 'prix', 'auteur', 'type_id', 'language_id', 'annee', 'editeur',
                ]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'book_id' => $event->book->id,
                'action' => $action,
                'book_title' => $event->book->designation,
                'details' => $details,
            ]);
        }
    }
}
