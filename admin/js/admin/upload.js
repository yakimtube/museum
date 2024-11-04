class FileUploader {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            maxSize: options.maxSize || 5242880, // 5MB default
            allowedTypes: options.allowedTypes || [],
            onProgress: options.onProgress || (() => {}),
            onSuccess: options.onSuccess || (() => {}),
            onError: options.onError || (() => {})
        };
        
        this.setupListeners();
    }

    setupListeners() {
        this.input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.validateAndUpload(file);
            }
        });
    }

    validateAndUpload(file) {
        // Check file size
        if (file.size > this.options.maxSize) {
            this.options.onError('File size exceeds limit');
            return;
        }

        // Check file type
        if (this.options.allowedTypes.length > 0 && 
            !this.options.allowedTypes.includes(file.type)) {
            this.options.onError('File type not allowed');
            return;
        }

        this.uploadFile(file);
    }

    uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const progress = (e.loaded / e.total) * 100;
                this.options.onProgress(progress);
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    this.options.onSuccess(response);
                } catch (e) {
                    this.options.onError('Invalid server response');
                }
            } else {
                this.options.onError('Upload failed');
            }
        });

        xhr.addEventListener('error', () => {
            this.options.onError('Upload failed');
        });

        xhr.open('POST', this.input.getAttribute('data-upload-url'));
        xhr.send(formData);
    }
}