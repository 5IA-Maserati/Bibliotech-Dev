/**
 * Input Validation and Sanitization Utility
 * Provides consistent validation and sanitization across all forms
 */

const FormValidator = {
  /**
   * Validation rules for different field types
   */
  rules: {
    username: {
      minLength: 3,
      maxLength: 20,
      pattern: /^[a-zA-Z0-9_-]+$/, // Alphanumeric, underscore, hyphen only
      message: 'Il nome utente dovrebbe contenere da 3 a 20 caratteri (alfanumerici, _, -)'
    },
    password: {
      minLength: 8,
      maxLength: 50,
      pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&()_+\-=\[\]{}|;:,.<>?]{8,}$/, // At least 1 uppercase, 1 lowercase, 1 digit
      message: 'La password deve contenere almeno 8 caratteri, inclusi una maiuscola, una minuscola e un numero'
    },
    confirm_password: {
  message: 'Le password non corrispondono'
    },
    email: {
      pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: 'Inserisci un indirizzo email valido'
    },
    name: {
      minLength: 2,
      maxLength: 50,
      pattern: /^[a-zA-Zàèéìòù\s-'-]+$/,
      message: 'Il nome deve contenere solo lettere, spazi e al massimo un trattino'
    },
    surname: {
      minLength: 2,
      maxLength: 50,
      pattern: /^[a-zA-Zàèéìòù\s-'-]+$/,
      message: 'Il cognome deve contenere solo lettere, spazi e al massimo un trattino'
    },
    birthdate: {
      minLength: 10,
      maxLength: 10,
      pattern: /^\d{4}-\d{2}-\d{2}$/,
      message: 'Inserisci una data valida'
    },
    search: {
      maxLength: 255,
      pattern: /^[a-zA-Z0-9àèéìòù\s\-',.&()]*$/,
      message: 'La ricerca contiene caratteri non validi'
    },
    book_title: {
      minLength: 1,
      maxLength: 255,
      pattern: /^[a-zA-Z0-9àèéìòù\s\-',.&:()]+$/,
      message: 'Il titolo del libro contiene caratteri non validi'
    },
    student_name: {
      minLength: 2,
      maxLength: 50,
      pattern: /^[a-zA-Zàèéìòù\s'-]+$/,
      message: 'Il nome deve contenere solo lettere e spazi'
    },
    student_email: {
      pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: 'Inserisci un indirizzo email valido'
    },
    booking_date: {
      minLength: 10,
      maxLength: 10,
      pattern: /^\d{4}-\d{2}-\d{2}$/,
      message: 'Inserisci una data valida'
    }
  },

  /**
   * Check if a field is required
   */
  isRequired: function (fieldId) {
    const field = document.getElementById(fieldId)
    return field && field.hasAttribute('richiesto')
  },

  /**
   * Validate a single field
   */
  validateField: function (fieldId) {
    const field = document.getElementById(fieldId)
    if (!field) return { valid: false, message: 'campo non trovato' }

    const value = field.value.trim()
    const rule = this.rules[fieldId]

    // Check if field is empty
    if (value === '' && this.isRequired(fieldId)) {
      return { valid: false, message: 'Questo campo è obbligatorio' }
    }

    // Skip validation if empty and not required
    if (value === '' && !this.isRequired(fieldId)) {
      return { valid: true, message: '' }
    }
    

    // Apply validation rules if they exist
    if (rule) {
      if (rule.minLength && value.length < rule.minLength) {
        return { valid: false, message: rule.message }
      }
      if (rule.maxLength && value.length > rule.maxLength) {
        return { valid: false, message: rule.message }
      }
      if (rule.pattern && !rule.pattern.test(value)) {
        return { valid: false, message: rule.message }
      }
    }

    // Logica specifica per il confronto password
    if (fieldId === 'confirm_password') {
      const passwordField = document.getElementById('password');
      if (passwordField && value !== passwordField.value) {
        return { valid: false, message: this.rules.confirm_password.message };
      }
    }

    return { valid: true, message: '' }
  },

  /**
   * Validate an entire form
   */
  validateForm: function (formId) {
    const form = document.getElementById(formId)
    if (!form) return { valid: false, errors: ['not found'] }

    const inputs = form.querySelectorAll('input:not([type="submit"]):not([type="button"])')
    const errors = []
    let isValid = true

    inputs.forEach(input => {
      const validation = this.validateField(input.id)
      if (!validation.valid) {
        isValid = false
        errors.push({
          fieldId: input.id,
          message: validation.message
        })
        this.markFieldError(input.id, validation.message)
      } else {
        this.clearFieldError(input.id)
      }
    })

    return { valid: isValid, errors }
  },

  /**
   * Sanitize input to prevent XSS attacks
   */
  sanitize: function (value) {
    if (typeof value !== 'string') return value

    const div = document.createElement('div')
    div.textContent = value
    return div.innerHTML
  },

  /**
   * Sanitize all form inputs
   */
  sanitizeForm: function (formId) {
    const form = document.getElementById(formId)
    if (!form) return null

    const inputs = form.querySelectorAll('input:not([type="submit"]):not([type="button"])')
    const sanitizedData = {}

    inputs.forEach(input => {
      sanitizedData[input.id] = this.sanitize(input.value.trim())
    })

    return sanitizedData
  },

  /**
   * Display error message for a field
   */
  markFieldError: function (fieldId, message) {
    const field = document.getElementById(fieldId)
    if (!field) return

    field.classList.add('error')
    field.setAttribute('aria-invalid', 'true')

    // Remove existing error message if any
    const existingError = field.parentElement.querySelector('.error-message')
    if (existingError) existingError.remove()

    // Add error message
    const errorMsg = document.createElement('span')
    errorMsg.className = 'error-message'
    errorMsg.setAttribute('role', 'alert')
    errorMsg.textContent = message
    field.parentElement.appendChild(errorMsg)
  },

  /**
   * Clear error message for a field
   */
  clearFieldError: function (fieldId) {
    const field = document.getElementById(fieldId)
    if (!field) return

    field.classList.remove('error')
    field.setAttribute('aria-invalid', 'false')

    const errorMsg = field.parentElement.querySelector('.error-message')
    if (errorMsg) errorMsg.remove()
  },

  /**
   * Real-time validation on input
   */
  enableRealTimeValidation: function (formId) {
    const form = document.getElementById(formId)
    if (!form) return

    const inputs = form.querySelectorAll('input:not([type="submit"]):not([type="button"])')
    inputs.forEach(input => {
      input.addEventListener('blur', () => {
        const validation = this.validateField(input.id)
        if (!validation.valid) {
          this.markFieldError(input.id, validation.message)
        } else {
          this.clearFieldError(input.id)
        }
      })

      // Clear error on input
      input.addEventListener('input', () => {
        if (input.classList.contains('error')) {
          this.clearFieldError(input.id)
        }
      })
    })
  }
}

// Export for use in other modules if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = FormValidator
}
