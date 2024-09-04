<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    //Add Question Function
    public function addQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required',
        ]);

        $question = FAQ::create([
            'passenger_id' => Auth::guard('user')->user()->passenger->passenger_id,
            'question' => $request->question,
        ]);

        return success($question, 'your question added successfully', 201);
    }

    //Edit Question Function
    public function editQuestion(FAQ $fAQ, Request $request)
    {
        if ($fAQ->employee_id) {
            return error('some thing went wrong', 'you cannot edit this question', 422);
        }
        $request->validate([
            'question' => 'required',
        ]);

        $fAQ->update([
            'question' => $request->question,
        ]);

        return success($fAQ, 'your question updated successfully');
    }

    //Delete Question Function
    public function deleteQuestion(FAQ $fAQ)
    {
        if ($fAQ->employee_id) {
            return error('some thing went wrong', 'you cannot delete this question', 422);
        }

        $fAQ->delete();

        return success(null, 'your question deleted successfully');
    }

    //Answer Question Function
    public function answerQuestion(FAQ $fAQ, Request $request)
    {
        $request->validate([
            'answer' => 'required',
        ]);

        $fAQ->update([
            'employee_id' => Auth::guard('user')->user()->employee->employee_id,
            'answer' => $request->answer,
        ]);

        return success($fAQ->with('passenger')->find($fAQ->faq_id), 'your answer added successfully');
    }

    //Get Questions Function
    public function getQuestions()
    {
        $questions = FAQ::with('passenger.travelRequirement', 'employee')->get();

        return success($questions, null);
    }

    //Get Question Information Function
    public function getQuestionInformation(FAQ $fAQ)
    {
        return success($fAQ->with(['passenger.travelRequirement', 'employee'])->find($fAQ->faq_id), null);
    }
}