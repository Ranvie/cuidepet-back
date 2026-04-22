@component('mail::message')
# Confirme sua inscrição na Newsletter 🐾

Olá! Recebemos uma solicitação para inscrever este endereço de e-mail na newsletter da **{{ config('app.name') }}**.

Para confirmar sua inscrição e começar a receber nossas novidades, clique no botão abaixo:

@component('mail::button', ['url' => $subscriptionUrl])
Confirmar Inscrição
@endcomponent

Este link estará disponível por **60 minutos**.

Ao confirmar, você receberá alertas regionais sobre pets perdidos e em doação na sua área.

Se você não solicitou esta inscrição, pode ignorar este e-mail com segurança — nenhuma ação será tomada.

---

### Dicas de segurança:
- Nunca compartilhe seus dados de acesso.
- Sempre verifique se está no site oficial antes de inserir informações.
- Mantenha seu e-mail seguro e atualizado.

Caso tenha alguma dúvida, entre em contato conosco pelo e-mail **[suporte@cuidepet.com.br](mailto:suporte@cuidepet.com.br)**.

Abraços,<br>
Equipe **{{ config('app.name') }}** 🐾
@endcomponent
