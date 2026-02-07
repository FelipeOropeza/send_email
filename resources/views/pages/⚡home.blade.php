<?php

use Livewire\Component;
use Illuminate\Support\Str;

new class extends Component {
    // Propriedades do formulário agora vivem diretamente no componente
    public string $email = '';
    public string $subject = '';
    public string $message = '';

    public array $emails = [];

    /**
     * Valida e adiciona um novo e-mail à lista de destinatários.
     */
    public function addEmail()
    {
        // Valida a propriedade pública 'email' do componente.
        $this->validate(['email' => 'required|email']);

        $email = Str::lower(trim($this->email));

        $this->emails = collect($this->emails)->push($email)->unique()->values()->all();

        // Limpa a propriedade 'email'.
        $this->reset('email');
    }

    /**
     * Remove um e-mail da lista de destinatários.
     */
    public function removeEmail(string $email)
    {
        $this->emails = collect($this->emails)->reject(fn($e) => $e === $email)->values()->all();
    }

    /**
     * Valida o formulário principal e envia os e-mails para a lista de destinatários.
     */
    public function send()
    {
        // 1. Garante que a lista de destinatários não esteja vazia.
        if (empty($this->emails)) {
            $this->addError('emails', 'Você precisa adicionar pelo menos um e-mail à lista.');
            return;
        }

        // 2. Valida as propriedades públicas do componente.
        $validated = $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // 3. Envia o e-mail para cada destinatário da lista.
        foreach ($this->emails as $recipientEmail) {
            \Mail::to($recipientEmail)->send(new \App\Mail\TesteEnvio(
                $this->subject,
                $this->message,
                $recipientEmail
            ));
        }
        
        session()->flash('status', 'E-mails enviados com sucesso!');

        // 4. Limpa todas as propriedades públicas relevantes.
        $this->reset('email', 'subject', 'message', 'emails');
    }
};
?>

<flux:main class="min-h-screen flex items-center justify-center" container>
    <div class="w-full max-w-lg p-6 flex flex-col gap-4">

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <h1 class="text-xl font-semibold text-center">
            Envio de Emails
        </h1>

        <div class="flex gap-2 items-start">
            <div class="w-full">
                <flux:input wire:model="email" wire:keydown.enter="addEmail" type="email" placeholder="email@exemplo.com" class="w-full" />
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <flux:button wire:click="addEmail">
                Adicionar
            </flux:button>
        </div>

        @error('emails')
            <div class="text-red-500 text-sm -mt-2">{{ $message }}</div>
        @enderror

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
            <flux:input wire:model="subject" class="w-full" />
            @error('subject')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex flex-col gap-2">
            <flux:label>Mensagem</flux:label>
            <flux:textarea wire:model="message" class="w-full h-40" />
            @error('message')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <flux:button wire:click="send" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Enviar para {{ count($emails) }} emails
            </span>
            <span wire:loading>
                Enviando...
            </span>
        </flux:button>

    </div>
</flux:main>
