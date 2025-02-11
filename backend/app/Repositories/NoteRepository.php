<?php

namespace App\Repositories;

use App\Models\Note;

class NoteRepository
{
    public function getNotes(array $noteIndexConditions, int $userId)
    {
        return Note::where('user_id', $userId)->paginate($noteIndexConditions['per_page'], ['*'], 'page', $noteIndexConditions['page']);
    }

    public function create(array $note)
    {
        return Note::create($note);
    }

    public function getNoteById(int $noteId)
    {
        return Note::find($noteId);
    }

    public function update(Note $note, array $updateNote)
    {
        $note->body = $updateNote['body'];
        $note->save();
        return $note;
    }

    public function delete(int $noteId)
    {
        Note::where('id', $noteId)->delete();
    }
}
