<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\NoteRequest;
use App\Http\Resources\Api\V1\NoteResource;
use App\Services\NoteService;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function store(NoteRequest $request)
    {
        $note = $this->noteService->createNote($request->validated());
        return new NoteResource($note);
    }
}
