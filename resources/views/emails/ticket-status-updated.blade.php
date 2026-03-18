<h1>Status atualizado</h1>

<p>Olá, {{ $ticket->requester->name }}.</p>
<p>O status do seu chamado foi atualizado.</p>

<ul>
    <li>Protocolo: {{ $ticket->protocol }}</li>
    <li>Título: {{ $ticket->title }}</li>
    <li>Status atual: {{ $ticket->status->name }}</li>
</ul>