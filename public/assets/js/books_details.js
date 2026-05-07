/* eslint-env browser */
// /* global FormValidator */

const reserveButton = document.getElementById('reserve-button')
const favoriteButton = document.getElementById('favorite-button')

function getBookId() {
  const bookIdFromAttr = favoriteButton?.dataset.bookId
  if (bookIdFromAttr) {
    return bookIdFromAttr
  }

  const params = new URLSearchParams(window.location.search)
  return params.get('id')
}

if (reserveButton) {
  reserveButton.addEventListener('click', function () {
    const bookId = getBookId()
    if (!bookId) {
      alert('ID del libro non trovato.')
      return
    }
    window.location.href = `/pages/booking.php?book_id=${bookId}`
  })
}

if (favoriteButton) {
  favoriteButton.addEventListener('click', async function () {
    const bookId = getBookId()
    if (!bookId) {
      alert('ID del libro non trovato.')
      return
    }

    try {
      const response = await fetch('/auth/favorite.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ book_id: bookId })
      })

      const result = await response.json()
      if (!response.ok) {
        if (response.status === 401) {
          alert('Effettua il login per aggiungere ai preferiti.')
          return
        }
        alert(result.error || 'Errore aggiungendo ai preferiti.')
        return
      }

      favoriteButton.textContent = 'Preferito'
      favoriteButton.disabled = true
      alert(result.message || 'Libro aggiunto ai preferiti.')
    } catch (error) {
      console.error('Favorite request failed:', error)
      alert('Impossibile aggiungere ai preferiti in questo momento.')
    }
  })
}
