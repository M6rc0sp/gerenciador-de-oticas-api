<x-mail::message>
    # Bem-vindo!

    Olá {{ $user->name }},

    Sua conta foi criada automaticamente em nossa plataforma. Abaixo estão suas credenciais de acesso:

    **Email:** {{ $email }}

    **Senha:** {{ $password }}

    <x-mail::button :url="config('app.url')">
        Fazer Login
    </x-mail::button>

    ---

    ⚠️ **Importante:** Guarde essas credenciais em um local seguro. Você pode alterá-las depois no seu perfil.

    Se você não criou essa conta, ignore este email.

    Obrigado,<br>
    {{ config('app.name') }}
</x-mail::message>
