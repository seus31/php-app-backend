<?php

namespace App\Services;

use App\Models\Note;
use App\Repositories\NoteRepository;

class NoteService
{
    protected NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function getNotes(array $noteIndexConditions, int $userId)
    {
        if (empty($noteIndexConditions['page'])) {
            $noteIndexConditions['page'] = config('const.pagination.page');
        }

        if (empty($noteIndexConditions['per_page'])) {
            $noteIndexConditions['per_page'] = config('const.pagination.per_page');
        }

        return $this->noteRepository->getNotes($noteIndexConditions, $userId);
    }

    public function createNote(array $note, int $userId)
    {
        $note['user_id'] = $userId;
        return $this->noteRepository->create($note);
    }

    public function getNote(int $noteId)
    {
        return $this->noteRepository->getNoteById($noteId);
    }

    public function updateNote(Note $note, array $noteData)
    {
        return $this->noteRepository->update($note, $noteData);
    }

    public function deleteNote(int $noteId)
    {
        $this->noteRepository->delete($noteId);

        return response()->json(['message' => 'Note deleted successfully']);
    }
}
