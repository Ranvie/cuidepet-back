@component('mail::message')
# Novo contato recebido! 🐾

Recebemos uma nova mensagem de contato através do formulário em nosso site. Aqui estão os detalhes:

---

**Nome**: {{ $name }}

**E-mail**: {{ $email }}

---

## Mensagem:

{{ $message }}

### Dicas de segurança:
- Nunca compartilhe seus dados de acesso.
- Sempre verifique se está no site oficial antes de inserir informações.
- Mantenha seu e-mail seguro e atualizado.
@endcomponent