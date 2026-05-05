/* eslint-env browser */
/* global FormValidator */

const profileForm = document.getElementById('change-password-form')

if (profileForm) {
  profileForm.addEventListener('submit', async function (e) {
    e.preventDefault()

    const validation = FormValidator.validateForm('change-password-form')
    if (!validation.valid) {
      console.error('Form validation failed:', validation.errors)
      return
    }

    const sanitizedData = FormValidator.sanitizeForm('change-password-form')
    if (sanitizedData.password !== sanitizedData.confirm_password) {
      FormValidator.markFieldError('confirm_password', 'Le password non coincidono')
      return
    }

    try {
      const response = await fetch('/auth/profile.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(sanitizedData)
      })

      const result = await response.json()
      if (!response.ok) {
        alert(result.error || 'Errore durante l’aggiornamento della password')
        return
      }

      alert('Password aggiornata con successo')
      profileForm.reset()
      document.querySelectorAll('#change-password-form input').forEach(input => {
        FormValidator.clearFieldError(input.id)
      })
    } catch (error) {
      console.error('Password update request failed:', error)
      alert('Impossibile aggiornare la password in questo momento. Riprovare più tardi.')
    }
  })

  FormValidator.enableRealTimeValidation('change-password-form')
}
