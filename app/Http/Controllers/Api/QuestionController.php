<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->only(['question' , 'answers' ,'correct_answer' , 'points' , 'quiz_id']);
        $question = Question::create($data);
        return response()->json(['data' => $question]);

    }

    public function show(Question $question)
    {
        //
    }

    public function update(Request $request, Question $question)
    {
        //
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(['msg' => 'Question deleted successfully']);

    }
}
