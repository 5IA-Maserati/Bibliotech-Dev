document.getElementById('register-form').addEventListener('submit', function (e) {
  e.preventDefault()

  const password = document.getElementById('password').value
  const confirm = document.getElementById('confirm-password').value

  if (password !== confirm) {
    // eslint-disable-next-line no-undef
    alert('Le password non coincidono')
    return
  }
  // eslint-disable-next-line no-undef
  alert('Registrazione completata!')
})
