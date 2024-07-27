<?php

namespace App\Services;

use App\Repositories\NoteRepository;

class NoteService
{
    protected $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function createNote(array $note)
    {
        return $this->noteRepository->create($note);
    }
}
