@component('mail::message')
# Olá, {{ $username }}! 🐾

Recebemos uma solicitação para redefinir sua senha na **{{ config('app.name') }}**. Sabemos como é importante manter sua conta segura, então criamos um link exclusivo para você:

@component('mail::button', ['url' => $resetUrl])
Redefinir Minha Senha
@endcomponent
Este link estará disponível pelos próximos **30 minutos**.
Se você não solicitou esta alteração, pode ignorar este e-mail — sua senha permanecerá a mesma.

---

### Dicas de segurança:
- Nunca compartilhe sua senha com ninguém.
- Utilize uma senha forte e única.
- Atualize sua senha regularmente.

Caso tenha alguma dúvida, entre em contato conosco pelo e-mail **[suporte@cuidepet.com.br](mailto:suporte@cuidepet.com.br)**.

Abraços,<br>
Equipe **{{ config('app.name') }}** 🐾
@endcomponent
