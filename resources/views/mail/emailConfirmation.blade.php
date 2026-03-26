@component('mail::message')
# Olá, {{ $username }}! 🐾

Seja bem-vindo à **{{ config('app.name') }}**!  
Para começar a usar sua conta com segurança, precisamos confirmar seu endereço de e-mail.

Clique no botão abaixo para confirmar:

@component('mail::button', ['url' => $confirmationUrl])
Confirmar Meu E-mail
@endcomponent

Este link estará disponível pelos próximos **30 minutos**.

Se você não criou uma conta, pode ignorar este e-mail com segurança.

---

### Dicas de segurança:
- Nunca compartilhe seus dados de acesso.
- Sempre verifique se está no site oficial antes de inserir informações.
- Mantenha seu e-mail seguro e atualizado.

Caso tenha alguma dúvida, entre em contato conosco pelo e-mail **[suporte@cuidepet.com.br](mailto:suporte@cuidepet.com.br)**.

Abraços,<br>
Equipe **{{ config('app.name') }}** 🐾
@endcomponent