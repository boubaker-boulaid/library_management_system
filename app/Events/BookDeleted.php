<?php

namespace App\Events;

use App\Models\Book;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookDeleted
{
    use Dispatchable, SerializesModels;

    public Book $book;

    /**
     * Create a new event instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }
}
