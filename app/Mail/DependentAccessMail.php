<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Dependent;

class DependentAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dependent;
    public $accessLink;

    public function __construct(Dependent $dependent)
    {
        $this->dependent = $dependent;
        $this->accessLink = url('dependente/login'); // ou rota específica para dependente
    }

    public function build()
    {
        return $this->subject('Acesso ao Sistema - BoxFarma')
                    ->view('emails.dependent-access');
    }
}
