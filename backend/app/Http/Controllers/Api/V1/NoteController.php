<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\NoteIndexRequest;
use App\Http\Requests\Api\V1\NoteRequest;
use App\Http\Resources\Api\V1\NoteResource;
use App\Http\Resources\Api\V1\NoteResourceCollection;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected NoteService $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function index(NoteIndexRequest $request): NoteResourceCollection
    {
        $notes = $this->noteService->getNotes($request->validated(), $request->user()->id);
        return new NoteResourceCollection($notes);
    }

    public function store(NoteRequest $request): NoteResource
    {
        $note = $this->noteService->createNote($request->validated(), $request->user()->id);
        return new NoteResource($note);
    }

    public function show(Note $note): NoteResource
    {
        return new NoteResource($note);
    }

    public function update(NoteRequest $request, Note $note): NoteResource
    {
        $note = $this->noteService->updateNote($note, $request->validated());
        return new NoteResource($note);
    }

    public function destroy(Request $request, int $noteId): JsonResponse
    {
        return $this->noteService->deleteNote($noteId);
    }
}
