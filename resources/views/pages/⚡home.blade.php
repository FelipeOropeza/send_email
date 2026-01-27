<?php

use Livewire\Component;
use Illuminate\Support\Str;

new class extends Component {
    public string $email = '';
    public array $emails = [];

    public string $subject = '';
    public string $message = '';

    public function addEmail()
    {
        $email = Str::lower(trim($this->email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $this->emails = collect($this->emails)->push($email)->unique()->values()->all();

        $this->email = '';
    }

    public function removeEmail(string $email)
    {
        $this->emails = collect($this->emails)->reject(fn($e) => $e === $email)->values()->all();
    }

    public function send()
    {
        // aqui depois entra Job + Mailable
        // foreach ($this->emails as $email) {}

        $this->reset(['email', 'emails', 'subject', 'message']);
    }
};
?>

<flux:main class="min-h-screen flex items-center justify-center" container>
    <div class="w-full max-w-lg p-6 flex flex-col gap-4">

        <h1 class="text-xl font-semibold text-center">
            Envio de Emails
        </h1>

        <div class="flex gap-2">
            <flux:input wire:model.defer="email" type="email" placeholder="email@exemplo.com" class="w-full" />

            <flux:button wire:click="addEmail">
                Adicionar
            </flux:button>
        </div>

        @if (count($emails))
            <div class="border rounded-lg p-3 flex flex-col gap-2">
                @foreach ($emails as $email)
                    <div class="flex justify-between items-center text-sm">
                        <span>{{ $email }}</span>

                        <flux:button size="xs" variant="ghost" wire:click="removeEmail('{{ $email }}')">
                            remover
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col gap-2">
            <flux:label>Assunto</flux:label>
            <flux:input wire:model.defer="subject" class="w-full" />
        </div>

        <div class="flex flex-col gap-2">
            <flux:label>Mensagem</flux:label>
            <flux:textarea wire:model.defer="message" class="w-full h-40" />
        </div>

        <flux:button wire:click="send">
            Enviar para {{ count($emails) }} emails
        </flux:button>

    </div>
</flux:main>
