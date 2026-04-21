@component('mail::message')
Olá, {{ $username }}! 🐾

Notamos que há um novo anúncio de animal {{ $type }} na sua região!
Clique abaixo para ver os detalhes e saber como você pode ajudar.

@component('mail::button', ['url' => $announcementUrl])
Ver Anúncio
@endcomponent

---

### Dicas de segurança:
- Nunca compartilhe suas informações pessoais com desconhecidos.
- Fique atento a mensagens suspeitas e links duvidosos.

Caso tenha alguma dúvida, entre em contato conosco pelo e-mail **[suporte@cuidepet.com.br](mailto:suporte@cuidepet.com.br)**.

Abraços,<br>
Equipe **{{ config('app.name') }}** 🐾

<br>
<p style="text-align: center;font-size: 12px">
  <a style="color: #2d3748; text-decoration: none;" href="{{ $unsubscribeUrl }}">
    Desinscrever-me
  </a>
</p>
@endcomponent