<?php

use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public array $emails = [];

    public string $subject = '';
    public string $message = '';

    public function addEmail()
    {
        if ($this->email && !in_array($this->email, $this->emails)) {
            $this->emails[] = $this->email;
        }

        $this->email = '';
    }

    public function removeEmail($email)
    {
        $this->emails = array_values(
            array_filter($this->emails, fn ($e) => $e !== $email)
        );
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
            <flux:input
                wire:model.defer="email"
                type="email"
                placeholder="email@exemplo.com"
                class="w-full"
            />

            <flux:button wire:click="addEmail">
                Adicionar
            </flux:button>
        </div>

        @if(count($emails))
            <div class="border rounded-lg p-3 flex flex-col gap-2">
                @foreach($emails as $email)
                    <div class="flex justify-between items-center text-sm">
                        <span>{{ $email }}</span>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            wire:click="removeEmail('{{ $email }}')">
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
            <flux:textarea
                wire:model.defer="message"
                class="w-full h-40"
            />
        </div>

        <flux:button wire:click="send">
            Enviar para {{ count($emails) }} emails
        </flux:button>

    </div>
</flux:main>
