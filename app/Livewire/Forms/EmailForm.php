<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailForm extends Form
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';
    
    #[Validate('required|string')]
    public string $message = '';
}
