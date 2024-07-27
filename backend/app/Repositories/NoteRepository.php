<?php

namespace App\Repositories;

use App\Models\Note;

class NoteRepository
{
    public function getNotes(array $noteIndexConditions)
    {
        return Note::paginate($noteIndexConditions['per_page'], ['*'], 'page', $noteIndexConditions['page']);
    }

    public function create(array $note)
    {
        return Note::create($note);
    }
}
