# Input Validation and Sanitization Documentation

## Overview
This document outlines the input validation and sanitization implementation across all forms in the Biblioteca Digitale project. The validation is performed client-side using the `FormValidator` utility to prevent invalid or malicious data submission.

## Files Involved

### Validation Library
- **`public/assets/js/form-validator.js`** - Core validation and sanitization utility

### Form Pages
- **`public/pages/login.php`** - Login form
- **`public/pages/signup.php`** - Registration form
- **`public/pages/search.php`** - Search form
- **`public/pages/booking.html`** - Booking form

### Form Scripts
- **`public/assets/js/login.js`** - Login form validation handler
- **`public/assets/js/signup.js`** - Registration form validation handler
- **`public/assets/js/search.js`** - Search form validation handler
- **`public/assets/js/booking.js`** - Booking form validation handler

### Form Input Component
- **`public/includes/form-input.php`** - Reusable form input component with accessibility features

---

## Validation Rules

### 1. Login Form

#### Username Field
- **Type:** Text
- **ID:** `username`
- **Requirements:**
  - Minimum length: 3 characters
  - Maximum length: 20 characters
  - Allowed characters: Letters (a-z, A-Z), numbers (0-9), underscore (_), hyphen (-)
  - Pattern: `^[a-zA-Z0-9_-]+$`
- **Error Message:** "Il nome utente dovrebbe contenere da 3 a 20 caratteri (alfanumerici, _, -)"

#### Password Field
- **Type:** Password
- **ID:** `password`
- **Requirements:**
  - Minimum length: 8 characters
  - Maximum length: 50 characters
  - Must contain at least one uppercase letter (A-Z)
  - Must contain at least one lowercase letter (a-z)
  - Must contain at least one digit (0-9)
  - Allowed special characters: @, $, !, %, *, ?, &
  - Pattern: `^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$`
- **Error Message:** "La password deve contenere almeno 8 caratteri, inclusi una maiuscola, una minuscola e un numero"

---

### 2. Registration Form

#### First Name (Nome)
- **Type:** Text
- **ID:** `nome`
- **Requirements:**
  - Minimum length: 2 characters
  - Maximum length: 50 characters
  - Allowed characters: Letters (including Italian accents: à, è, é, ì, ò, ù), spaces, apostrophe ('), hyphen (-)
  - Pattern: `^[a-zA-Zàèéìòù\s'-]+$`
- **Error Message:** "Il nome deve contenere solo lettere, spazi e al massimo un trattino"
- **Required:** Yes

#### Last Name (Cognome)
- **Type:** Text
- **ID:** `cognome`
- **Requirements:**
  - Minimum length: 2 characters
  - Maximum length: 50 characters
  - Allowed characters: Letters (including Italian accents), spaces, apostrophe ('), hyphen (-)
  - Pattern: `^[a-zA-Zàèéìòù\s'-]+$`
- **Error Message:** "Il cognome deve contenere solo lettere, spazi e al massimo un trattino"
- **Required:** Yes

#### Date of Birth (Data di Nascita)
- **Type:** Date
- **ID:** `data-nascita`
- **Requirements:**
  - Format: YYYY-MM-DD (ISO 8601)
  - Minimum length: 10 characters
  - Maximum length: 10 characters
  - Pattern: `^\d{4}-\d{2}-\d{2}$`
- **Error Message:** "Inserisci una data valida nel formato GG-MM-AAAA"
- **Required:** Yes

#### Email
- **Type:** Email
- **ID:** `email`
- **Requirements:**
  - Must be a valid email format
  - Pattern: `^[^\s@]+@[^\s@]+\.[^\s@]+$`
- **Error Message:** "Inserisci un indirizzo email valido"
- **Required:** Yes

#### Password
- **Type:** Password
- **ID:** `password`
- **Requirements:** Same as login password (see Login Form section)
- **Required:** Yes

#### Confirm Password
- **Type:** Password
- **ID:** `confirm-password`
- **Requirements:**
  - Must match the Password field exactly
- **Error Message:** "Le password non coincidono"
- **Required:** Yes

---

### 3. Search Form

#### Search Query
- **Type:** Text
- **ID:** `q`
- **Requirements:**
  - Maximum length: 255 characters
  - Allowed characters: Letters (including Italian accents), numbers, spaces, hyphen (-), apostrophe ('), comma (,), period (.), ampersand (&), parentheses ( )
  - Pattern: `^[a-zA-Z0-9àèéìòù\s\-',.&()]*$`
- **Error Message:** "La ricerca contiene caratteri non validi"
- **Required:** Yes (validation requires non-empty input)

---

### 4. Booking Form

#### Book Title
- **Type:** Text
- **ID:** `book-title`
- **Requirements:**
  - Minimum length: 1 character
  - Maximum length: 255 characters
  - Allowed characters: Letters (including Italian accents), numbers, spaces, hyphen (-), apostrophe ('), comma (,), period (.), ampersand (&), colon (:), parentheses ( )
  - Pattern: `^[a-zA-Z0-9àèéìòù\s\-',.&:()]+$`
- **Error Message:** "Il titolo del libro contiene caratteri non validi"
- **Required:** Yes

#### Student Name
- **Type:** Text
- **ID:** `student-name`
- **Requirements:**
  - Minimum length: 2 characters
  - Maximum length: 50 characters
  - Allowed characters: Letters (including Italian accents), spaces, apostrophe ('), hyphen (-)
  - Pattern: `^[a-zA-Zàèéìòù\s'-]+$`
- **Error Message:** "Il nome deve contenere solo lettere e spazi"
- **Required:** Yes

#### Student Email
- **Type:** Email
- **ID:** `student-email`
- **Requirements:**
  - Must be a valid email format
  - Pattern: `^[^\s@]+@[^\s@]+\.[^\s@]+$`
- **Error Message:** "Inserisci un indirizzo email valido"
- **Required:** Yes

#### Booking Date
- **Type:** Date
- **ID:** `booking-date`
- **Requirements:**
  - Format: YYYY-MM-DD (ISO 8601)
  - Minimum length: 10 characters
  - Maximum length: 10 characters
  - Pattern: `^\d{4}-\d{2}-\d{2}$`
- **Error Message:** "Inserisci una data valida"
- **Required:** Yes

---

## FormValidator API Reference

### Public Methods

#### `validateField(fieldId)`
Validates a single input field.
- **Parameters:** `fieldId` (string) - The ID of the input field
- **Returns:** Object with `{ valid: boolean, message: string }`
- **Usage:**
  ```javascript
  const result = FormValidator.validateField('username');
  if (!result.valid) {
    console.error(result.message);
  }
  ```

#### `validateForm(formId)`
Validates all input fields in a form.
- **Parameters:** `formId` (string) - The ID of the form element
- **Returns:** Object with `{ valid: boolean, errors: Array }`
- **Usage:**
  ```javascript
  const result = FormValidator.validateForm('login-form');
  if (!result.valid) {
    result.errors.forEach(error => console.error(error.fieldId, error.message));
  }
  ```

#### `sanitize(value)`
Removes or escapes potentially harmful characters from input.
- **Parameters:** `value` (string) - The string to sanitize
- **Returns:** Sanitized string
- **Usage:**
  ```javascript
  const cleanInput = FormValidator.sanitize(userInput);
  ```

#### `sanitizeForm(formId)`
Sanitizes all input values in a form.
- **Parameters:** `formId` (string) - The ID of the form element
- **Returns:** Object with `{ fieldId: sanitizedValue }`
- **Usage:**
  ```javascript
  const sanitized = FormValidator.sanitizeForm('login-form');
  console.log(sanitized.username); // Cleaned username value
  ```

#### `markFieldError(fieldId, message)`
Displays an error message for a field.
- **Parameters:**
  - `fieldId` (string) - The ID of the input field
  - `message` (string) - The error message to display
- **Usage:**
  ```javascript
  FormValidator.markFieldError('email', 'Invalid email format');
  ```

#### `clearFieldError(fieldId)`
Removes error styling and messages from a field.
- **Parameters:** `fieldId` (string) - The ID of the input field
- **Usage:**
  ```javascript
  FormValidator.clearFieldError('email');
  ```

#### `enableRealTimeValidation(formId)`
Enables real-time validation on blur and clears errors on input.
- **Parameters:** `formId` (string) - The ID of the form element
- **Usage:**
  ```javascript
  FormValidator.enableRealTimeValidation('login-form');
  ```

---

## Security Features

### Input Sanitization
- **XSS Prevention:** All text inputs are sanitized to prevent Cross-Site Scripting (XSS) attacks by escaping HTML special characters
- **Method:** Text content is set via `textContent` property to prevent HTML injection

### Validation Rules
- **Pattern Matching:** Regular expressions enforce allowed character sets
- **Length Constraints:** Minimum and maximum length validation prevents buffer overflow attacks
- **Type-Specific Rules:** Email, date, and password fields have specific validation patterns

### Client-Side vs Server-Side
- **Current Implementation:** Client-side validation only (improves UX)
- **Recommendation:** Implement server-side validation in PHP endpoints for security:
  - Never trust client-side validation alone
  - Duplicate validation rules on the server
  - Validate and sanitize data before database insertion

---

## Implementation in Forms

### Basic Implementation Pattern

Each form follows this pattern:

```javascript
// 1. Get form reference
const form = document.getElementById('form-id');

// 2. Add submit handler
form.addEventListener('submit', function (e) {
  e.preventDefault();

  // 3. Validate all fields
  const validation = FormValidator.validateForm('form-id');
  if (!validation.valid) return;

  // 4. Sanitize inputs
  const sanitized = FormValidator.sanitizeForm('form-id');

  // 5. Process sanitized data
  console.log('Clean data:', sanitized);
  // TODO: Send to server
});

// 6. Enable real-time validation
FormValidator.enableRealTimeValidation('form-id');
```

### Error Display
- Errors appear inline next to the field with visual styling
- `aria-invalid="true"` attribute added for accessibility
- Error messages are displayed in `<span class="error-message" role="alert">`

### Real-Time Validation
- Fields are validated on blur event
- Errors clear when user starts typing again
- Provides immediate feedback to users

---

## Testing Validation

### Valid Inputs to Test
- **Username:** "user_name-123"
- **Password:** "Password123"
- **Email:** "user@example.com"
- **Name:** "Giovanni"
- **Date:** "2000-01-15"
- **Search:** "Harry Potter & The Philosopher's Stone"

### Invalid Inputs to Test (XSS Prevention)
- **Script Injection:** `<script>alert('XSS')</script>`
- **HTML Injection:** `<img src=x onerror=alert('XSS')>`
- **SQL Injection:** `'; DROP TABLE users; --`
- **Special Characters:** `!@#$%^*()=+[]{}|;:`

All these should be either rejected or sanitized by the validation system.

---

## Future Enhancements

1. **Server-Side Validation:** Implement duplicate validation rules in PHP
2. **CSRF Protection:** Add CSRF tokens to forms
3. **Rate Limiting:** Implement login attempt rate limiting
4. **Password Strength Meter:** Add visual indicator for password complexity
5. **Real-Time Availability Checks:** Check username/email availability while typing
6. **Accessibility:** Enhanced keyboard navigation and screen reader support
7. **Internationalization:** Support for multiple languages in error messages

---

## References

- OWASP Top 10: https://owasp.org/www-project-top-ten/
- XSS Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
- Input Validation Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html
- MDN Web Docs - Form Validation: https://developer.mozilla.org/en-US/docs/Learn/Forms/Form_validation
