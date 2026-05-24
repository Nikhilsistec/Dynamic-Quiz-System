<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOptionRequest;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionController extends Controller
{
    public function store(StoreOptionRequest $request, Question $question)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/options', 'public');
        }

        $order  = $question->options()->max('order') + 1;
        $option = Option::create([
            'question_id' => $question->id,
            'body'        => $request->body,
            'image_path'  => $imagePath,
            'is_correct'  => $request->boolean('is_correct'),
            'order'       => $order,
        ]);

        return response()->json([
            'id'         => $option->id,
            'body'       => $option->body,
            'image_url'  => $option->image_url,
            'is_correct' => $option->is_correct,
            'order'      => $option->order,
            'update_url' => route('admin.questions.options.update', [$question, $option]),
            'delete_url' => route('admin.questions.options.destroy', [$question, $option]),
        ]);
    }

    public function update(Request $request, Question $question, Option $option)
    {
        $data = $request->validate([
            'body'       => ['nullable', 'string', 'max:1000'],
            'is_correct' => ['nullable', 'boolean'],
            'image'      => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($option->image_path) {
                Storage::disk('public')->delete($option->image_path);
            }
            $data['image_path'] = $request->file('image')->store('images/options', 'public');
        }

        unset($data['image']);
        $option->update($data);

        return response()->json([
            'id'         => $option->id,
            'body'       => $option->body,
            'image_url'  => $option->image_url,
            'is_correct' => $option->is_correct,
        ]);
    }

    public function destroy(Question $question, Option $option)
    {
        if ($option->image_path) {
            Storage::disk('public')->delete($option->image_path);
        }

        $option->delete();

        return response()->json(['ok' => true]);
    }
}
