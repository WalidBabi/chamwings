<?php

namespace App\Mail;

use App\Models\FAQ;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Answer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $code;
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        $code = $this->code;
        $fAQ = FAQ::find($code);
        return $this->view('answer', compact('fAQ'));
    }
}