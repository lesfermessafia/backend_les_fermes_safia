<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion - Les Fermes Safia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-navbar title="Discussion" color="green" />

    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="mb-5">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('comptable.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#008d36] hover:text-[#305327] transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Retour au tableau de bord
            </a>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-[#305327] to-[#008d36] px-6 py-5 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold">Boîte de discussion</h1>
                        <p class="text-sm text-white/75 mt-1">Échangez instantanément avec l’administration et la comptabilité.</p>
                    </div>
                    <span class="flex items-center gap-2 text-xs font-semibold bg-white/15 rounded-full px-3 py-2"><span class="h-2 w-2 rounded-full bg-green-300"></span> En ligne</span>
                </div>
            </div>

            <div id="discussion-messages" class="h-[55vh] min-h-[360px] overflow-y-auto p-5 space-y-4 bg-gray-50">
                @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-[85%] sm:max-w-[70%]">
                        <p class="text-xs text-gray-500 mb-1 {{ $message->sender_id === auth()->id() ? 'text-right' : '' }}">{{ $message->sender->prenom }} {{ $message->sender->nom }}</p>
                        <div class="rounded-2xl px-4 py-3 {{ $message->sender_id === auth()->id() ? 'bg-[#008d36] text-white rounded-br-sm' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-sm' }}">
                            <p class="text-sm whitespace-pre-wrap break-words">{{ $message->message }}</p>
                            <p class="text-[11px] mt-2 {{ $message->sender_id === auth()->id() ? 'text-white/70' : 'text-gray-400' }}">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div id="discussion-empty" class="h-full flex items-center justify-center text-center text-gray-400">
                    <div><p class="font-semibold">Aucun message</p><p class="text-sm mt-1">Commencez la discussion.</p></div>
                </div>
                @endforelse
            </div>

            <form id="discussion-form" class="p-5 border-t border-gray-100 bg-white/80 backdrop-blur">
                @csrf
                <div class="flex items-end gap-3 rounded-[1.75rem] bg-white border-2 border-gray-100 px-5 py-3 shadow-sm focus-within:border-[#008d36] focus-within:ring-2 focus-within:ring-[#008d36]/25 focus-within:shadow-[0_0_0_4px_rgba(0,141,54,0.10)] transition-all duration-200">
                    <textarea
                        id="discussion-input"
                        name="message"
                        rows="1"
                        maxlength="2000"
                        required
                        placeholder="Écrire un message..."
                        class="flex-1 resize-none bg-transparent border-0 text-sm text-gray-700 placeholder:text-gray-400 focus:ring-0 focus:outline-none outline-none py-2.5 max-h-40 min-h-[44px] leading-relaxed"
                    ></textarea>
                    <button
                        id="discussion-submit"
                        type="submit"
                        class="shrink-0 h-11 w-11 rounded-full bg-[#008d36] text-white flex items-center justify-center shadow-md hover:bg-[#305327] hover:scale-105 active:scale-95 transition-all"
                        aria-label="Envoyer"
                    >
                        <svg class="h-5 w-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9-2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <p id="discussion-error" class="text-xs text-red-600 mt-2 hidden pl-1"></p>
            </form>
        </div>
    </main>

    <script>
        window.discussionConfig = {
            storeUrl: @json(route('discussion.messages.store')),
            latestUrl: @json(route('discussion.messages.latest')),
            userId: @json(auth()->id()),
            csrf: @json(csrf_token()),
        };
    </script>
    @vite('resources/js/discussion.js')
</body>
</html>
