import { playAlertSound } from './alert-sound';

const config = window.discussionConfig;
const messagesContainer = document.getElementById('discussion-messages');
const form = document.getElementById('discussion-form');
const input = document.getElementById('discussion-input');
const submit = document.getElementById('discussion-submit');
const error = document.getElementById('discussion-error');

function getInitials(name = '') {
    return name.split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase() || '?';
}

function appendMessage(message) {
    if (!messagesContainer || document.querySelector(`[data-message-id="${message.id}"]`)) return;

    document.getElementById('discussion-empty')?.remove();
    const own = Number(message.sender_id) === Number(config.userId);
    const wrapper = document.createElement('div');
    wrapper.className = `flex ${own ? 'justify-end' : 'justify-start'} msg-appear`;
    wrapper.dataset.messageId = message.id;

    const initials = getInitials(message.sender_name);
    const label = own ? 'Vous' : escapeHtml(message.sender_name);

    if (own) {
        wrapper.innerHTML = `
            <div class="flex items-end gap-3 max-w-[85%] sm:max-w-[65%] flex-row-reverse">
                <div class="h-10 w-10 rounded-full bg-[#008d36] text-white flex items-center justify-center text-sm font-bold shadow-sm">${initials}</div>
                <div>
                    <p class="text-xs text-gray-500 mb-1 mr-1 text-right">${label}</p>
                    <div class="rounded-2xl rounded-br-none px-5 py-3 bg-gradient-to-br from-[#008d36] to-[#305327] text-white shadow-lg">
                        <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(message.message)}</p>
                        <p class="text-[11px] text-white/70 mt-2 text-right">${escapeHtml(message.created_at)}</p>
                    </div>
                </div>
            </div>`;
    } else {
        wrapper.innerHTML = `
            <div class="flex items-end gap-3 max-w-[85%] sm:max-w-[65%]">
                <div class="h-10 w-10 rounded-full bg-[#d1fae5] text-[#008d36] flex items-center justify-center text-sm font-bold shadow-sm">${initials}</div>
                <div>
                    <p class="text-xs text-gray-500 mb-1 ml-1">${label}</p>
                    <div class="rounded-2xl rounded-bl-none px-5 py-3 bg-white text-gray-800 shadow-md border border-gray-100">
                        <p class="text-sm whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(message.message)}</p>
                        <p class="text-[11px] text-gray-400 mt-2 text-right">${escapeHtml(message.created_at)}</p>
                    </div>
                </div>
            </div>`;
    }

    messagesContainer.appendChild(wrapper);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    submit.disabled = true;
    error.classList.add('hidden');
    try {
        const response = await fetch(config.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': config.csrf,
            },
            body: JSON.stringify({ message }),
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message ?? 'Impossible d’envoyer le message.');
        appendMessage(data.message);
        input.value = '';
        resizeTextarea();
    } catch (exception) {
        error.textContent = exception.message;
        error.classList.remove('hidden');
    } finally {
        submit.disabled = false;
        input.focus();
    }
});

function resizeTextarea() {
    if (!input) return;
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
}

input?.addEventListener('input', resizeTextarea);

input?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        form.requestSubmit();
    }
});

if (window.Echo) {
    window.Echo.private('discussion.admin-comptable').listen('.discussion.message.sent', (message) => {
        playAlertSound('message');
        appendMessage(message);
    });
}

async function syncMessages() {
    if (!config?.latestUrl) return;
    const messageElements = messagesContainer?.querySelectorAll('[data-message-id]') ?? [];
    const lastMessage = messageElements[messageElements.length - 1];
    const after = lastMessage?.dataset.messageId ?? 0;

    try {
        const response = await fetch(`${config.latestUrl}?after=${after}`, {
            headers: { Accept: 'application/json' },
        });
        if (!response.ok) return;
        const data = await response.json();
        data.messages?.forEach((message) => {
            if (Number(message.sender_id) !== Number(config.userId)) playAlertSound('message');
            appendMessage(message);
        });
    } catch (exception) {
        console.debug('Synchronisation de la discussion indisponible.', exception);
    }
}

messagesContainer?.scrollTo(0, messagesContainer.scrollHeight);
window.setInterval(syncMessages, 3000);
