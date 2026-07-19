<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#305327]">Les Fermes Safia</h1>
            <p class="text-gray-600 mt-2">Saisissez le code reçu</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.code.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            <div class="mb-4">
                <label for="code" class="block text-gray-700 text-sm font-bold mb-2">Code à 6 chiffres</label>
                <input type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#008d36] text-center text-2xl tracking-widest"
                    placeholder="000000">
            </div>

            <button type="submit"
                class="w-full bg-[#008d36] text-white font-bold py-2 px-4 rounded-md hover:bg-[#305327] transition duration-200">
                Vérifier
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="{{ route('password.forgot') }}" class="text-[#008d36] hover:underline">Renvoyer un code</a>
        </div>
    </div>
</body>
</html>
