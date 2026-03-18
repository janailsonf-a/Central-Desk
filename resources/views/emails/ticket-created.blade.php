<h1>Novo chamado criado</h1>

<p>Olá, {{ $ticket->requester->name }}.</p>
<p>Seu chamado foi criado com sucesso.</p>

<ul>
    <li>Protocolo: {{ $ticket->protocol }}</li>
    <li>Título: {{ $ticket->title }}</li>
    <li>Status: {{ $ticket->status->name }}</li>
</ul>