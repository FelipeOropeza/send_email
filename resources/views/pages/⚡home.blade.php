<?php

use Livewire\Component;
use Illuminate\Support\Str;

new class extends Component {
    public string $email = '';
    public string $subject = '';
    public string $message = '';

    public array $emails = [];

    public function addEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $email = Str::lower(trim($this->email));

        $this->emails = collect($this->emails)->push($email)->unique()->values()->all();

        $this->reset('email');
        $this->resetErrorBag('email');
    }

    public function removeEmail(string $email)
    {
        $this->emails = collect($this->emails)->reject(fn($e) => $e === $email)->values()->all();
    }

    public function send()
    {
        if (empty($this->emails)) {
            $this->addError('emails', 'Você precisa adicionar pelo menos um e-mail à lista.');
            return;
        }

        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        foreach ($this->emails as $recipientEmail) {
            \Mail::to($recipientEmail)->send(new \App\Mail\TesteEnvio($this->subject, $this->message, $recipientEmail));
        }

        session()->flash('status', 'E-mails enviados com sucesso!');

        $this->reset('email', 'subject', 'message', 'emails');
    }
};

?>

<flux:main class="min-h-screen flex items-center justify-center" container>
    <div class="w-full max-w-lg p-6 flex flex-col gap-4">

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <h1 class="text-xl font-semibold text-center">
            Envio de Emails
        </h1>

        {{-- ADD EMAIL --}}
        <div class="flex gap-2 items-start">
            <div class="w-full">
                <flux:input wire:model.defer="email" type="email" placeholder="email@exemplo.com"
                    wire:keydown.enter.prevent="addEmail" />

                @error('email')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <flux:button type="button" wire:click="addEmail" wire:loading.attr="disabled"
                wire:loading.target="addEmail">
                <span wire:loading.remove wire:target="addEmail">
                    Adicionar
                </span>

                <span wire:loading wire:target="addEmail">
                    Adicionando...
                </span>
            </flux:button>

        </div>

        @error('emails')
            <div class="text-red-500 text-sm -mt-2">
                {{ $message }}
            </div>
        @enderror

        @if (count($emails))
            <div class="border rounded-lg p-3 flex flex-col gap-2">
                @foreach ($emails as $email)
                    <div class="flex justify-between items-center text-sm">
                        <span>{{ $email }}</span>

                        <flux:button size="xs" variant="ghost" type="button"
                            wire:click="removeEmail('{{ $email }}')">
                            remover
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- FORM PRINCIPAL --}}
        <form wire:submit.prevent="send" novalidate class="flex flex-col gap-4">

            <div class="flex flex-col gap-2">
                <flux:label>Assunto</flux:label>
                <flux:input wire:model.defer="subject" />
                @error('subject')
                    <div class="text-red-500 text-sm">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <flux:label>Mensagem</flux:label>
                <flux:textarea wire:model.defer="message" class="h-40" />
                @error('message')
                    <div class="text-red-500 text-sm">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <flux:button type="submit">
                Enviar para {{ count($emails) }} emails
            </flux:button>

        </form>

    </div>
</flux:main>
