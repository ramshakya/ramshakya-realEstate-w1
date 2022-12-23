<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $body;
    public $fromMail;
    public $subject;

    public function __construct($data)
    {
        //
        $this->subject = $data["subject"];
        $this->fromMail = $data["from"];
        $this->body = $data["body"];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->from($this->fromMail)->view('emails.commonMail')->with([
                "content" => $this->body
            ]
        );


    }
}
