<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\EnviarEmailJob;

Route::livewire('/home', 'pages::home');

/*
|--------------------------------------------------------------------------
| Rota de Teste para a Fila de E-mails
|--------------------------------------------------------------------------
|
| Ao acessar esta rota, um novo 'EnviarEmailJob' será despachado
| para a fila. O worker irá processá-lo em segundo plano.
|
*/
Route::get('/enviar-email-fila', function () {
    $destinatario = 'teste@exemplo.com';
    $assunto = 'E-mail enviado via Fila';
    $mensagem = 'Este é um e-mail de teste para demonstrar o funcionamento das filas no Laravel.';

    // Despacha o Job para a fila
    EnviarEmailJob::dispatch($destinatario, $assunto, $mensagem);

    return 'E-mail despachado para a fila! O worker irá processá-lo em breve.';
});
