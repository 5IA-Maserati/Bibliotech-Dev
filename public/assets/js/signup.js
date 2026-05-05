/* global FormValidator, alert */

/**
 * Registration Form Validation and Submission
 */

// Select the registration form and attach an event listener to it
document.getElementById('register-form').addEventListener('submit', async function (e) {
  // Prevent the form from submitting
  e.preventDefault()

  // Validate all fields
  const validation = FormValidator.validateForm('register-form')
  console.log('Validation result:', validation)

  if (!validation.valid) {
    console.error('Form validation failed:', validation.errors)
    // Errors are already displayed by markFieldError
    return
  }

  // Check if passwords match
  const password = document.getElementById('password').value
  const confirm = document.getElementById('confirm_password').value
  console.log('Password:', password, 'Confirm:', confirm)

  if (password !== confirm) {
    console.log('Passwords do not match')
    FormValidator.markFieldError('confirm_password', 'Le password non coincidono')
    return
  }

  // Sanitize all inputs
  const sanitizedData = FormValidator.sanitizeForm('register-form')
  delete sanitizedData.confirm_password

  try {
    const response = await fetch('/auth/register.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(sanitizedData)
    })

    const result = await response.json()
    if (!response.ok) {
      const message = result.error || 'Errore di registrazione'
      FormValidator.markFieldError('email', message)
      return
    }

    alert('Registrazione completata! Verrai reindirizzato al login.')
    document.getElementById('register-form').reset()
    document.querySelectorAll('#register-form input').forEach(input => {
      FormValidator.clearFieldError(input.id)
    })
    window.location.href = '/pages/login.php'
  } catch (error) {
    console.error('Registration request failed:', error)
    alert('Impossibile completare la registrazione. Riprovare più tardi.')
  }
})

// Enable real-time validation on blur
FormValidator.enableRealTimeValidation('register-form')
