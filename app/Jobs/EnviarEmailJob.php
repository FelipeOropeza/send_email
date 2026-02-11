<?php

namespace App\Jobs;

use App\Mail\EmailEnvio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Propriedades para armazenar os dados do e-mail.
     */
    public string $destinatario;
    public string $assunto;
    public string $mensagem;

    /**
     * Create a new job instance.
     *
     * @param string $destinatario O e-mail para quem vamos enviar.
     * @param string $assunto O assunto do e-mail.
     * @param string $mensagem A mensagem do e-mail.
     */
    public function __construct(string $destinatario, string $assunto, string $mensagem)
    {
        $this->destinatario = $destinatario;
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
    }

    /**
     * Execute the job.
     *
     * Este método é chamado pelo "worker" da fila.
     */
    public function handle(): void
    {
        // Cria uma instância do seu Mailable com os dados recebidos
        $email = new EmailEnvio(
            $this->assunto,
            $this->mensagem,
            $this->destinatario
        );

        // Envia o e-mail para o destinatário
        Mail::to($this->destinatario)->send($email);
    }
}
