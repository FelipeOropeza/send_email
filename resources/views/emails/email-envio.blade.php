 <!DOCTYPE html>
<html>

<head>
    <title>{{ $assunto }}</title>
</head>

<body>
    <h1>Olá, {{ $email_teste }}!</h1>

    <p>Você recebeu uma nova mensagem:</p>

    <div style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
        <p>{{ $mensagem }}</p>
    </div>
</body>

</html>
