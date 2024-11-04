class Modal {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Close modal when clicking outside
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.hide();
            }
        });

        // Close modal when pressing ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                this.hide();
            }
        });
    }

    show() {
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    hide() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    setTitle(title) {
        const titleElement = this.modal.querySelector('#modalTitle');
        if (titleElement) {
            titleElement.textContent = title;
        }
    }

    setContent(content) {
        const contentElement = this.modal.querySelector('.modal-content');
        if (contentElement) {
            contentElement.innerHTML = content;
        }
    }
}

// Initialize modals
document.addEventListener('DOMContentLoaded', () => {
    window.exhibitModal = new Modal('exhibitModal');
});