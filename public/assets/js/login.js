/* eslint-env browser */
/* global FormValidator */

/**
 * Login Form Validation and Submission
 */

const loginForm = document.getElementById('login-form')

if (loginForm) {
  loginForm.addEventListener('submit', async function (e) {
    e.preventDefault()

    const validation = FormValidator.validateForm('login-form')
    if (!validation.valid) {
      console.error('Form validation failed:', validation.errors)
      return
    }

    const sanitizedData = FormValidator.sanitizeForm('login-form')
    console.log('Sanitized form data:', sanitizedData)

    try {
      const response = await fetch('/auth/login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(sanitizedData)
      })

      const result = await response.json()
      if (!response.ok) {
        const message = result.error || 'Errore di login'
        FormValidator.markFieldError('email', message)
        return
      }

      alert('Login completato!')
      loginForm.reset()
      document.querySelectorAll('#login-form input').forEach(input => {
        FormValidator.clearFieldError(input.id)
      })
      window.location.href = '/index.php'
    } catch (error) {
      console.error('Login request failed:', error)
      alert('Impossibile effettuare il login. Riprovare più tardi.')
    }
  })

  FormValidator.enableRealTimeValidation('login-form')
}
