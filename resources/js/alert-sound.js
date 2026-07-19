let audioContext;

const soundFiles = {
    notification: '/sounds/notification.mp3',
    message: '/sounds/message.mp3',
};

const sounds = {};
const ready = {};

Object.entries(soundFiles).forEach(([kind, src]) => {
    const audio = new Audio(src);
    audio.preload = 'auto';
    ready[kind] = false;

    audio.addEventListener('canplaythrough', () => {
        ready[kind] = true;
    }, { once: true });

    audio.addEventListener('error', () => {
        console.debug(`Fichier audio ${src} indisponible, on utilisera le bip généré.`);
        ready[kind] = false;
    }, { once: true });

    audio.load();
    sounds[kind] = audio;
});

async function playGeneratedSound(kind = 'notification') {
    try {
        audioContext ??= new (window.AudioContext || window.webkitAudioContext)();
        if (audioContext.state === 'suspended') {
            await audioContext.resume();
        }

        const now = audioContext.currentTime + 0.01;
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        const frequency = kind === 'message' ? 660 : 880;

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(frequency, now);
        oscillator.frequency.setValueAtTime(frequency * 1.2, now + 0.08);
        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.16, now + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.22);
        oscillator.connect(gain);
        gain.connect(audioContext.destination);
        oscillator.start(now);
        oscillator.stop(now + 0.24);
    } catch (error) {
        console.debug('Son généré indisponible.', error);
    }
}

export async function playAlertSound(kind = 'notification') {
    const audio = sounds[kind] || sounds.notification;

    if (!audio) {
        playGeneratedSound(kind);
        return;
    }

    try {
        audio.pause();
        audio.currentTime = 0;
        await audio.play();
    } catch (error) {
        console.debug(`Lecture du fichier ${kind} impossible, bip généré.`, error);
        playGeneratedSound(kind);
    }
}
