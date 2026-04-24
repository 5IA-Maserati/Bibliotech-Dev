/* eslint-env browser */
// /* global FormValidator */

const reserveButton = document.getElementById('reserve-button')
const favoriteButton = document.getElementById('favorite-button')

if (reserveButton) {
  reserveButton.addEventListener('click', function () {
    alert('Richiesta di prenotazione inviata.')
    // TODO: have to integrate with reservation API
  })
}

if (favoriteButton) {
  favoriteButton.addEventListener('click', function () {
    alert('Libro aggiunto ai preferiti.')
    // TODO: have to integrate with favorite API
  })
}
