<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-navbar title="Notifications" color="green" />
    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="mb-5">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('comptable.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#008d36] hover:text-[#305327] transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour au tableau de bord
            </a>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-[#008d36] text-white flex items-center justify-center font-bold">{{ strtoupper(substr($user->nom, 0, 1)) }}{{ strtoupper(substr($user->prenom, 0, 1)) }}</div>
                    <div>
                        <h1 class="text-2xl font-bold text-[#305327]">Mes notifications</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $user->prenom }} {{ $user->nom }} · <span class="capitalize">{{ $user->role }}</span></p>
                    </div>
                </div>
            </div>
            @if(auth()->user()->unreadNotifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="px-4 py-2 rounded-lg bg-[#008d36] text-white text-sm font-semibold hover:bg-[#305327] transition">Tout marquer comme lu</button>
            </form>
            @endif
        </div>

        <div class="mb-4 rounded-xl border border-green-100 bg-green-50/60 px-4 py-3 text-sm text-green-800">
            Ces notifications sont uniquement celles destinées à votre compte.
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
            @php $data = $notification->data; @endphp
            <div class="bg-white rounded-xl border {{ $notification->read_at ? 'border-gray-100' : 'border-green-300 bg-green-50/30' }} shadow-sm p-4 flex gap-4 items-start">
                <div class="h-10 w-10 rounded-full bg-{{ $data['color'] ?? 'blue' }}-100 text-{{ $data['color'] ?? 'blue' }}-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"></path></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-bold text-gray-900">{{ $data['title'] ?? 'Notification' }}</h2>
                        @if(!$notification->read_at)<span class="rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs font-semibold">Non lue</span>@endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $data['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-600" aria-label="Supprimer">×</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl border border-gray-100 text-center py-16 text-gray-500">Aucune notification.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    </main>
</body>
</html>
