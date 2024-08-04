<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $userId = Auth::id();  // Get authenticated user ID
        $inputText = $request->input('input_text');

        $response = Http::post('http://localhost:8000/chat', [
            'input_text' => $inputText,
            'user_id' => $userId,
        ]);

        return $response->json();
    }

    public function storeChatHistory(Request $request)
    {
        $userId = $request->input('user_id');
        $messages = $request->input('chat_history');
    
        foreach ($messages as $index => $message) {
            $isUserMessage = $index % 2 === 0; // Even index: user message, Odd index: chatbot response
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $message,
                'is_user_message' => $isUserMessage,
            ]);
        }
    
        return response()->json(['status' => 'success'], 200);
    }
    
    public function getChatHistory($userId)
    {
        $chatHistories = ChatHistory::where('user_id', $userId)->orderBy('created_at', 'asc')->get();
        return response()->json($chatHistories, 200);
    }
    
}
