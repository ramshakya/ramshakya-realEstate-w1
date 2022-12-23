<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendMailNew extends Mailable
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
    public $agentId;
    public $hashId;
    public function __construct($data)
    {
        //
        $this->subject = $data["subject"];
        $this->fromMail = $data["from"];
        $this->body = $data["body"];
        $this->agentId = $data["agentId"];
        $this->hashId = $data["hashId"];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if ($this->agentId == env('HOUSENAGENTID')) {
            return $this->subject($this->subject)->from($this->fromMail, env('MAIL_FROM_NAME_HOUSEN'))->view('emails.send-email')->with([
                    "content" => $this->body,
                    "subject" => $this->subject,
                    "hashId" => $this->hashId
                ]
            );
        } elseif ($this->agentId == env('HOUSENAGENTID')) {
            return $this->subject($this->subject)->from($this->fromMail, env('MAIL_FROM_NAME_HOUSEN'))->view('emails.send-email')->with([
                    "content" => $this->body,
                    "subject" => $this->subject,
                    "hashId" => $this->hashId
                ]
            );
        } else {
            return $this->subject($this->subject)->from($this->fromMail, env('MAIL_FROM_NAME'))->view('emails.send-email')->with([
                    "content" => $this->body,
                    "subject" => $this->subject,
                    "hashId" => $this->hashId
                ]
            );
        }
    }
}
