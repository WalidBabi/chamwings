<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $userId = (string) Auth::guard('user')->id();
        $inputText = $request->input('input_text');
        $threadId = $request->input('thread_id');
    
        // If no thread_id is provided, generate a new one
        $isNewThread = false;
        if (!$threadId) {
            $threadId = $this->generateNewThreadId($userId);
            $isNewThread = true;
        }
        $threadId = (string) $threadId;
    
        ini_set('max_execution_time', 1000);
        $response = Http::timeout(1000)->post('http://localhost:8001/chat', [
            'input_text' => $inputText,
            'user_id' => $userId,
            'thread_id' => $threadId
        ]);
    
        $responseData = $response->json();
    
        // Generate the summary title for the new thread
        $summaryTitle = null;
        if ($isNewThread) {
            $summaryTitle = $this->generateSummaryTitle($inputText, $responseData['answer'] ?? '');
            \Log::info('Generated Summary Title: ', ['title' => $summaryTitle]); // Log the generated title
        }
    
        // Retrieve the existing chat history for this thread
        $existingHistory = ChatHistory::where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->first();
    
        // Prepare new chat history entry
        $newChatHistory = [
            'input_text' => $inputText,
            'response_text' => $responseData['answer'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ];
    
        if ($existingHistory) {
            // Append the new chat history
            $currentHistory = is_array($existingHistory->chat_history)
                ? $existingHistory->chat_history
                : json_decode($existingHistory->chat_history, true);
        
            $existingHistory->chat_history = array_merge(
                $currentHistory ?? [],
                [$newChatHistory]
            );
        
            // Update title for both new and existing threads
            if ($summaryTitle) {
                $existingHistory->title = $summaryTitle;
            }
        
            // Save the updated chat history
            $existingHistory->save();
        } else {
            // Create a new chat history entry
            ChatHistory::create([
                'user_id' => $userId,
                'thread_id' => $threadId,
                'input_text' => $inputText,
                'response_text' => $responseData['answer'] ?? null,
                'chat_history' => json_encode([$newChatHistory]),
                'title' => $summaryTitle // Save the title for the new thread
            ]);
        }
    
        // Prepare the response
        $responsePayload = [
            'thread_id' => $threadId,
            'answer' => $responseData['answer'] ?? null
        ];
    
        // Add summary information for new threads
        if ($isNewThread) {
            $responsePayload['title'] = $summaryTitle;
        }
    
        return response()->json($responsePayload);
    }
    




    private function generateSummaryTitle($inputText, $responseText)
    {
        // Helper function to extract key phrases or words
        function extractKeyPhrases($text, $maxWords = 4)
        {
            // Remove common stop words and limit the words to maxWords
            $stopWords = ['the', 'and', 'of', 'to', 'in', 'that', 'it', 'is', 'was', 'for', 'on', 'with', 'as', 'by', 'at', 'an', 'be', 'this', 'which'];
            $words = array_filter(explode(' ', $text), function ($word) use ($stopWords) {
                return !in_array(strtolower($word), $stopWords);
            });

            // Limit to max words and return as a string
            return implode(' ', array_slice($words, 0, $maxWords));
        }

        // Extract key phrases from input and response
        $inputKeyPhrases = extractKeyPhrases($inputText);
        $responseKeyPhrases = extractKeyPhrases($responseText);

        // Concatenate the key phrases for the summary title
        $summaryTitle = $inputKeyPhrases . ' - ' . $responseKeyPhrases;

        return $summaryTitle;
    }



    private function generateNewThreadId($userId)
    {
        // Get the highest existing thread_id for the user and cast it to an integer
        $lastThread = ChatHistory::where('user_id', $userId)
            ->orderBy(DB::raw('CAST(thread_id AS UNSIGNED)'), 'desc')
            ->first();

        // If no thread exists, start with 1, otherwise increment the highest thread_id by 1
        // Cast to integer to ensure proper arithmetic
        $newThreadId = $lastThread ? (intval($lastThread->thread_id) + 1) : 1;

        return (string) $newThreadId;
    }




    public function getChatHistory(Request $request, $threadId)
    {
        $userId = (string) Auth::guard('user')->id();

        // Fetch chat history for the authenticated user and specific thread
        $chatHistories = ChatHistory::where('user_id', $userId)
            ->where('thread_id', $threadId) // Filter by thread_id
            ->orderBy('created_at', 'desc')
            ->first(); // Fetch the latest history entry

        if (!$chatHistories) {
            return response()->json([]);
        }

        // Decode only if it's a JSON-encoded string
        $chatHistoryData = $chatHistories->chat_history;

        if (is_string($chatHistoryData)) {
            $chatHistoryData = json_decode($chatHistoryData, true);
        }

        return response()->json($chatHistoryData);
    }


    public function listThreads(Request $request)
    {
        $userId = (string) Auth::guard('user')->id();

        $threads = ChatHistory::where('user_id', $userId)
            ->select('thread_id', 'title', 'created_at', 'updated_at')
            ->distinct()
            ->get();

        return response()->json($threads);
    }
}
