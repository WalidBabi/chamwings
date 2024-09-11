<?php

namespace App\Http\Controllers;

use App\Helper\BannedWordsHelper;
use App\Mail\Answer;
use App\Models\FAQ;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QuestionController extends Controller
{
    //Add Question Function
    public function addQuestion(Request $request)
    {
        $user = Auth::guard('user')->user();
        $request->validate([
            'question' => 'required',
        ]);
        $question = str_replace(BannedWordsHelper::getBannedWords(), '***', $request->question);
        $question = FAQ::create([
            'passenger_id' => Auth::guard('user')->user()->passenger->passenger_id,
            'question' => $question,
        ]);

        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' asked a question',
            'type' => 'insert',
        ]);

        return success($question, 'your question added successfully', 201);
    }

    //Edit Question Function
    public function editQuestion(FAQ $fAQ, Request $request)
    {
        $user = Auth::guard('user')->user();
        if ($fAQ->employee_id) {
            return error('some thing went wrong', 'you cannot edit this question', 422);
        }
        $request->validate([
            'question' => 'required',
        ]);
        $question = str_replace(BannedWordsHelper::getBannedWords(), '***', $request->question);

        $fAQ->update([
            'question' => $question,
        ]);

        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' updated his question',
            'type' => 'update',
        ]);

        return success($fAQ, 'your question updated successfully');
    }

    //Delete Question Function
    public function deleteQuestion(FAQ $fAQ)
    {
        $user = Auth::guard('user')->user();
        if ($fAQ->employee_id) {
            return error('some thing went wrong', 'you cannot delete this question', 422);
        }
        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' deleted his question',
            'type' => 'delete',
        ]);

        $fAQ->delete();

        return success(null, 'your question deleted successfully');
    }

    //Answer Question Function
    public function answerQuestion(FAQ $fAQ, Request $request)
    {
        $user = Auth::guard('user')->user();
        $request->validate([
            'answer' => 'required',
        ]);
        $answer = str_replace(BannedWordsHelper::getBannedWords(), '***', $request->answer);
        $fAQ->update([
            'employee_id' => Auth::guard('user')->user()->employee->employee_id,
            'answer' => $answer,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' answerd passenger question',
            'type' => 'update',
        ]);
        try {
            Mail::to($fAQ->passenger->user->email)->send(new Answer($fAQ->faq_id));
        } catch (Exception $e) {
            return error('some thing went wrong', 'cannot send answer to passenger, try arain later....', 422);
        }

        return success($fAQ->with('passenger')->find($fAQ->faq_id), 'your answer added successfully');
    }

    //Get Questions Function
    public function getQuestions()
    {
        $questions = FAQ::with('passenger.travelRequirement', 'employee')
            ->orderByDesc('faq_id')
            ->paginate();
        $data = [
            'data' => $questions->items(),
            'total' => $questions->total(),
        ];
        return success($data, null);
    }

    //Get Question Information Function
    public function getQuestionInformation(FAQ $fAQ)
    {
        return success($fAQ->with(['passenger.travelRequirement', 'employee'])->find($fAQ->faq_id), null);
    }
}