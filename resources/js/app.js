import './bootstrap';
window.addEventListener('play-sound', event => {
    console.log('play sound', event);
    const sounds = {
        error: '/sounds/error.mp3',
        success: '/sounds/success.mp3',
    };

    const src = sounds[event.detail.type];
    if (src) {
        new Audio(src).play();
    }
});
