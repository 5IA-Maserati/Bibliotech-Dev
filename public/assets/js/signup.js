// Select the registration form and attach an event listener to it
document.getElementById('register-form').addEventListener('submit', function (e) {
  // Prevent the form from submitting
  e.preventDefault()

  // Get the password and confirm password values from the form
  const password = document.getElementById('password').value
  const confirm = document.getElementById('confirm-password').value

  if (password !== confirm) {
    // If they don't match, display an alert and return to prevent further execution
    alert('Le password non coincidono')
    return
  }
  
  // If the passwords match, display a success message
  alert('Registrazione completata!')
})