class ExhibitManager {
    constructor() {
        this.setupEventListeners();
        this.setupFormValidation();
    }

    setupEventListeners() {
        const form = document.getElementById('exhibitForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Preview uploaded images
        document.querySelectorAll('input[type="file"][accept^="image/"]').forEach(input => {
            input.addEventListener('change', (e) => this.previewImage(e.target));
        });

        // Preview uploaded audio
        document.querySelectorAll('input[type="file"][accept^="audio/"]').forEach(input => {
            input.addEventListener('change', (e) => this.previewAudio(e.target));
        });
    }

    setupFormValidation() {
        const rules = {
            'id': [
                validationRules.required,
                validationRules.pattern(/^[A-Za-z0-9-_]+$/, 'Only letters, numbers, hyphens, and underscores allowed')
            ],
            'description': [
                validationRules.required,
                validationRules.minLength(50)
            ]
        };

        new FormValidator('exhibitForm', rules);
    }

    async handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                this.showError(result.error || 'Error saving exhibit');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('Error saving exhibit');
        }
    }

    previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const previewId = input.getAttribute('data-preview');
            const preview = document.getElementById(previewId);

            reader.onload = (e) => {
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         class="max-w-full h-auto rounded shadow-sm">
                    <button type="button" 
                            onclick="exhibitManager.clearFile('${input.id}', '${previewId}')"
                            class="mt-2 text-red-600 hover:text-red-800">
                        Remove Image
                    </button>`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    previewAudio(input) {
        if (input.files && input.files[0]) {
            const previewId = input.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            
            preview.innerHTML = `
                <audio controls class="w-full">
                    <source src="${URL.createObjectURL(input.files[0])}" 
                            type="${input.files[0].type}">
                </audio>
                <button type="button" 
                        onclick="exhibitManager.clearFile('${input.id}', '${previewId}')"
                        class="mt-2 text-red-600 hover:text-red-800">
                    Remove Audio
                </button>`;
        }
    }

    clearFile(inputId, previewId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).innerHTML = '';
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.textContent = message;

        const form = document.getElementById('exhibitForm');
        form.insertBefore(errorDiv, form.firstChild);

        setTimeout(() => errorDiv.remove(), 5000);
    }
}

// Initialize exhibit manager
const exhibitManager = new ExhibitManager();

function showAddExhibitModal() {
    document.getElementById('modalTitle').textContent = 'Add New Exhibit';
    document.getElementById('formAction').value = 'add';
    document.getElementById('exhibitId').value = '';
    document.getElementById('exhibitForm').reset();
    
    // Clear all previews
    document.querySelectorAll('[id^="preview-"]').forEach(el => el.innerHTML = '');
    
    document.getElementById('exhibitModal').classList.remove('hidden');
}

async function editExhibit(id) {
    document.getElementById('modalTitle').textContent = 'Edit Exhibit';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('exhibitId').value = id;
    
    try {
        const response = await fetch(`handlers/exhibit_handler.php?action=get&id=${id}`);
        const data = await response.json();
        
        // Populate form fields
        document.querySelector('input[name="id"]').value = data.id;
        
        Object.keys(data.content).forEach(lang => {
            const content = data.content[lang];
            document.querySelector(`input[name="title_${lang}"]`).value = content.title;
            document.querySelector(`textarea[name="description_${lang}"]`).value = content.description;
            
            // Show existing media previews
            if (content.image_url) {
                document.getElementById(`preview-image-${lang}`).innerHTML = `
                    <img src="${content.image_url}" class="max-w-full h-auto rounded shadow-sm">`;
            }
            if (content.audio_url) {
                document.getElementById(`preview-audio-${lang}`).innerHTML = `
                    <audio controls class="w-full">
                        <source src="${content.audio_url}" type="audio/mpeg">
                    </audio>`;
            }
        });
        
        document.getElementById('exhibitModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading exhibit data');
    }
}

function deleteExhibit(id) {
    if (confirm('Are you sure you want to delete this exhibit? This action cannot be undone.')) {
        fetch('handlers/exhibit_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting exhibit');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting exhibit');
        });
    }
}

function closeModal() {
    document.getElementById('exhibitModal').classList.add('hidden');
}