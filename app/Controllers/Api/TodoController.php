<?php

namespace App\Controllers\Api;

use App\Core\Exceptions\HttpException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validation\Validator;
use App\Models\Todo;

class TodoController
{
    public function index(Request $request): Response
    {
        return Response::json(Todo::query()->orderByDesc('id')->get());
    }

    public function store(Request $request): Response
    {
        $data = Validator::validate($request->json(), [
            'title' => 'required|string|max:255',
        ]);

        $title = trim($data['title']);

        $todo = Todo::create([
            'title' => $title,
            'completed' => false,
        ]);

        return Response::json($todo, 201);
    }

    public function show(Request $request, int $id): Response
    {
        $todo = Todo::find($id);

        if (!$todo) {
            throw new HttpException(404, "Task #{$id} not found.", ['error' => "Task #{$id} not found."]);
        }

        return Response::json($todo);
    }

    public function update(Request $request, int $id): Response
    {
        $todo = Todo::find($id);

        if (!$todo) {
            throw new HttpException(404, "Task #{$id} not found.", ['error' => "Task #{$id} not found."]);
        }

        $payload = $request->json();
        $updates = [];

        if (array_key_exists('title', $payload)) {
            $validated = Validator::validate(['title' => $payload['title']], ['title' => 'required|string|max:255']);
            $title = trim($validated['title']);
            $updates['title'] = $title;
        }

        if (array_key_exists('completed', $payload)) {
            $validated = Validator::validate(['completed' => $payload['completed']], ['completed' => 'boolean']);
            $updates['completed'] = $validated['completed'];
        }

        if (!empty($updates)) {
            $todo->update($updates);
        }

        return Response::json($todo->fresh());
    }

    public function destroy(Request $request, int $id): Response
    {
        $todo = Todo::find($id);

        if (!$todo) {
            throw new HttpException(404, "Task #{$id} not found.", ['error' => "Task #{$id} not found."]);
        }

        $todo->delete();

        return Response::json(['success' => true, 'id' => $id]);
    }
}
