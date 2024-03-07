<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $data;
    protected $subjectTitle;

    public function __construct($data, $subjectTitle)
    {
        $this->data = $data;
        $this->subjectTitle = $subjectTitle;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.template')
            ->with(['data' => $this->data, 'title' => $this->subjectTitle])
            ->subject($this->subjectTitle . ' Email')
            ->from('muhammad.umer@unitedsol.net', $this->subjectTitle);
    }
}
