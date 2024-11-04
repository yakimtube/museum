class FormValidator {
    constructor(formId, rules) {
        this.form = document.getElementById(formId);
        this.rules = rules;
        this.setupValidation();
    }

    setupValidation() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Real-time validation
        this.form.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    }

    validateForm() {
        let isValid = true;
        this.form.querySelectorAll('input, textarea, select').forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

    validateField(input) {
        const rules = this.rules[input.name];
        if (!rules) return true;

        let isValid = true;
        let errorMessage = '';

        rules.forEach(rule => {
            if (!rule.validate(input.value)) {
                isValid = false;
                errorMessage = rule.message;
            }
        });

        this.showFieldError(input, errorMessage);
        return isValid;
    }

    showFieldError(input, message) {
        const errorElement = input.parentElement.querySelector('.error-message');
        if (message) {
            if (!errorElement) {
                const error = document.createElement('p');
                error.className = 'error-message text-red-600 text-sm mt-1';
                error.textContent = message;
                input.parentElement.appendChild(error);
            } else {
                errorElement.textContent = message;
            }
            input.classList.add('border-red-500');
        } else {
            if (errorElement) {
                errorElement.remove();
            }
            input.classList.remove('border-red-500');
        }
    }
}

// Validation rules
const validationRules = {
    required: {
        validate: value => value.trim() !== '',
        message: 'This field is required'
    },
    minLength: (length) => ({
        validate: value => value.length >= length,
        message: `Minimum length is ${length} characters`
    }),
    maxLength: (length) => ({
        validate: value => value.length <= length,
        message: `Maximum length is ${length} characters`
    }),
    pattern: (regex, message) => ({
        validate: value => regex.test(value),
        message: message
    })
};