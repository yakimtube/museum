class AudioHandler {
    constructor() {
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.audioElement = null;
        this.headphonesConnected = false;
    }

    async init() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            this.headphonesConnected = devices.some(device => 
                device.kind === 'audiooutput' && 
                device.label.toLowerCase().includes('headphone')
            );
            
            this.updateAudioInterface();
        } catch (error) {
            console.error('Error initializing audio:', error);
        }
    }

    updateAudioInterface() {
        const audioSection = document.getElementById('audioSection');
        const warningSection = document.getElementById('noHeadphonesWarning');
        
        if (this.headphonesConnected) {
            audioSection.classList.remove('hidden');
            warningSection.classList.add('hidden');
        } else {
            audioSection.classList.add('hidden');
            warningSection.classList.remove('hidden');
        }
    }

    async setAudioSource(url) {
        if (!this.headphonesConnected) {
            return;
        }

        this.audioElement = document.getElementById('exhibitAudio');
        this.audioElement.src = url;
    }
}

const audioHandler = new AudioHandler();
document.addEventListener('DOMContentLoaded', () => audioHandler.init());