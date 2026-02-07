<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TesteEnvio extends Mailable
{
    use Queueable, SerializesModels;

    // Propriedades públicas para serem acessadas na view e para definir o assunto
    public string $mensagem;
    public string $email_teste;

    /**
     * Create a new message instance.
     *
     * @param string $assunto O assunto do e-mail.
     * @param string $mensagem O corpo da mensagem.
     * @param string $email_teste O e-mail do destinatário (para exibição no corpo).
     */
    public function __construct(
        public string $assunto,
        string $mensagem,
        string $email_teste
    ) {
        $this->mensagem = $mensagem;
        $this->email_teste = $email_teste;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'felipe2006.co@gmail.com',
            subject: $this->assunto, // Usando o assunto dinâmico
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.teste-envio',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
