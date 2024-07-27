<?php

namespace App\Repositories;

use App\Models\Note;

class NoteRepository
{
    public function create(array $note)
    {
        return Note::create($note);
    }
}
