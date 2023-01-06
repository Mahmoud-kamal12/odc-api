<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Question;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{

    public function index(Request $request)
    {
        $levels = Quiz::all();
        if ($request->has('level_id')){
            $levels = Quiz::where('level_id' , $request->get('level_id'));
        }

        return response()->json(['data' => $levels]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only('quiz_name' , 'questions' , 'level_id' , 'time');
            $quiz = Quiz::create(['name' => $data['quiz_name'] , 'level_id' => $data['level_id']]);
            $questions = $quiz->questions()->createMany($data['questions']);
            $quiz->total = $questions->sum('points');
            $quiz->save();
            DB::commit();
            return response()->json(['quiz' => $quiz , 'questions' => $questions]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['msg' => "error" , 'error' => $e->getMessage()]);
        }

    }

    public function show(Quiz $quiz)
    {
        return response()->json(['data' => $quiz->load('questions')]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        try {
            DB::beginTransaction();
            $data = $request->only('quiz_name' , 'questions' , 'level_id' , 'time');
            $quiz->update(['name' => $data['quiz_name'] , 'level_id' => $data['level_id']]);
            $questions = $data['questions'];
            foreach ($questions as $val){
                $question1 = Question::find($val['id']);
                $question1->update($val);
            }
            $questions = collect($questions);
            $quiz->total = $questions->sum('points');
            $quiz->save();
            DB::commit();
            return response()->json(['quiz' => $quiz , 'questions' => $questions]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['msg' => "error" , 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Quiz $quiz)
    {
        try {
            DB::beginTransaction();
            $quiz->questions()->delete();
            $quiz->delete();
            DB::commit();
            return response()->json(['msg' => 'Quiz deleted successfully']);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['msg' => "error" , 'error' => $e->getMessage()]);
        }
    }

    public function correct(Request $request , $id){
        $count = 1;
        $totalMark = 0;
        $today = Carbon::today();

        try {
            DB::beginTransaction();
            $user = auth()->user();
            $quizzes = $user->quizzes->where('id' , $id);
            if ($quizzes->isNotEmpty()){
                return response()->json(['msg' => "error" , 'error' => "You have exceeded the maximum number of times you can enter the Quiz today"]);
            }
            $quiz = Quiz::findorFail($id);
            $questionsAnswers = $request->get('questions');
            $questionsIds = $quiz->questions->pluck('id')->toArray();
            foreach ($questionsAnswers as $question){
                if (in_array((int)$question['id'],$questionsIds)){
                    $id = (int)$question['id'];
                    $q = Question::findOrFail($id);
                    if ($q->correct_answer === $question['correct_answer']){
                        $totalMark += $q->points;
                    }
                }
            }
            $user->quizzes()->attach($quiz , ['count' => $count , 'mark' => $totalMark , 'last_enter' => $today]);
            DB::commit();
            return response()->json(['msg' => 'successfully' , 'quizzes' => $user->load('quizzes.questions')]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['msg' => "error" , 'error' => $e->getMessage()]);
        }
    }
}
