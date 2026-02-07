<?php

use Livewire\Component;
use Illuminate\Support\Str;
use App\Livewire\Forms\EmailForm;

new class extends Component {
    public EmailForm $form;
    public array $emails = [];

    /**
     * Valida e adiciona um novo e-mail à lista de destinatários.
     */
    public function addEmail()
    {
        // Valida apenas o campo de e-mail usando as regras do Form Object.
        // Se a validação falhar, o Livewire exibirá o erro automaticamente.
        $this->validate(['form.email' => 'required|email']);

        $email = Str::lower(trim($this->form->email));

        // Adiciona o e-mail à lista, garantindo que seja único.
        $this->emails = collect($this->emails)->push($email)->unique()->values()->all();

        // Limpa apenas o campo de e-mail do formulário.
        $this->form->reset('email');
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

        // 2. Valida apenas os campos necessários para o envio (ignora o form.email).
        $validated = $this->validate([
            'form.subject' => 'required|string|max:255',
            'form.message' => 'required|string',
        ]);

        // 3. Envia o e-mail para cada destinatário da lista.
        foreach ($this->emails as $email) {
            \Mail::to($email)->send(new \App\Mail\TesteEnvio(
                $this->form->subject,
                $this->form->message,
                $email
            ));
        }
        
        session()->flash('status', 'E-mails enviados com sucesso!');

        // 4. Limpa o formulário e a lista de e-mails após o envio.
        $this->form->reset();
        $this->emails = [];
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
                <flux:input wire:model="form.email" wire:keydown.enter="addEmail" type="email" placeholder="email@exemplo.com" class="w-full" />
                @error('form.email')
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
            <flux:input wire:model="form.subject" class="w-full" />
            @error('form.subject')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex flex-col gap-2">
            <flux:label>Mensagem</flux:label>
            <flux:textarea wire:model="form.message" class="w-full h-40" />
            @error('form.message')
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
