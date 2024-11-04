// Exhibit content handler
class ExhibitHandler {
    constructor() {
        this.audioHandler = new AudioHandler();
        this.setupEventListeners();
    }

    setupEventListeners() {
        const exhibitForm = document.getElementById('exhibitForm');
        if (exhibitForm) {
            exhibitForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.loadExhibit(document.getElementById('exhibitId').value);
            });
        }
    }

    async loadExhibit(exhibitId) {
        try {
            const response = await fetch(`handlers/exhibit_content_handler.php?id=${exhibitId}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayExhibit(data.data);
            } else {
                this.showError('Exhibit not found');
            }
        } catch (error) {
            console.error('Error loading exhibit:', error);
            this.showError('Error loading exhibit');
        }
    }

    displayExhibit(exhibit) {
        const content = document.getElementById('exhibitContent');
        content.classList.remove('hidden');

        // Set title and description
        document.getElementById('exhibitTitle').textContent = exhibit.title;
        document.getElementById('exhibitDescription').innerHTML = exhibit.description;

        // Handle image
        const imageContainer = document.getElementById('exhibitImage');
        if (exhibit.image_url) {
            imageContainer.innerHTML = `
                <img src="${exhibit.image_url}" 
                     alt="${exhibit.title}" 
                     class="w-full h-auto rounded-lg shadow-md">`;
        } else {
            imageContainer.innerHTML = '';
        }

        // Handle audio
        if (exhibit.audio_url) {
            this.audioHandler.setAudioSource(exhibit.audio_url);
        }
    }

    showError(message) {
        const content = document.getElementById('exhibitContent');
        content.classList.remove('hidden');
        content.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                ${message}
            </div>`;
    }
}

// Initialize handlers when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new ExhibitHandler();
});