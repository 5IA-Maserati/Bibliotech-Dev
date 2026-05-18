/* eslint-env browser */
// /* global FormValidator */

const reserveButton = document.getElementById('reserve-button')
const favoriteButton = document.getElementById('favorite-button')
const placeholderRelatedCoverUrl = (window.BookCovers && window.BookCovers.placeholderCoverUrl) || '/assets/img/common/default_cover.png'

const loadBookCoverImage = (imgElement, isbn) => {
  if (window.BookCovers && typeof window.BookCovers.loadBookCoverImage === 'function') {
    return window.BookCovers.loadBookCoverImage(imgElement, isbn)
  }
  return Promise.resolve()
}

function getBookId () {
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

// Load related books
async function loadRelatedBooks () {
  const bookId = getBookId()
  if (!bookId) {
    return
  }

  const relatedBooksList = document.getElementById('related-books-list')
  if (!relatedBooksList) {
    return
  }

  try {
    const response = await fetch(`/api/books/related.php?id=${bookId}`)
    const data = await response.json()

    if (!data.success || !data.books || data.books.length === 0) {
      relatedBooksList.innerHTML = '<p class="no-related-books">Nessun libro correlato trovato.</p>'
      return
    }

    const booksHtml = data.books.map(book => {
      const coverUrl = placeholderRelatedCoverUrl
      return `
        <div class="related-book-card" data-id="${book.id}">
          <img class="related-book-cover" data-isbn="${book.isbn || ''}" src="${coverUrl}" alt="Cover of ${book.title}">
          <div class="related-book-info">
            <h4 class="related-book-title">${book.title}</h4>
            <p class="related-book-author">di ${book.author}</p>
            <p class="related-book-year">${book.publication_year || 'N/A'}</p>
          </div>
        </div>
      `
    }).join('')

    relatedBooksList.innerHTML = booksHtml
    relatedBooksList.querySelectorAll('.related-book-cover[data-isbn]').forEach(img => {
      const isbn = img.dataset.isbn
      if (isbn) {
        loadBookCoverImage(img, isbn)
      }
    })

    // Add click handlers
    document.querySelectorAll('.related-book-card').forEach(card => {
      card.addEventListener('click', () => {
        const bookId = card.dataset.id
        window.location.href = `/pages/books_details.php?id=${bookId}`
      })
    })
  } catch (error) {
    console.error('Error loading related books:', error)
    relatedBooksList.innerHTML = '<p class="no-related-books">Errore nel caricamento dei libri correlati.</p>'
  }
}

// Load book cover image from data attribute
function loadBookCover () {
  const bookCoverImg = document.getElementById('book-cover')
  if (!bookCoverImg) {
    return
  }

  const isbn = bookCoverImg.dataset.isbn
  if (isbn) {
    loadBookCoverImage(bookCoverImg, isbn)
    return
  }

  const coverUrl = bookCoverImg.dataset.cover
  if (coverUrl && coverUrl.trim() !== '') {
    bookCoverImg.src = coverUrl
    // Add error handler to fall back to placeholder if image fails to load
    bookCoverImg.onerror = function () {
      this.onerror = null
      this.src = '/assets/img/common/default_cover.png'
    }
  }
}

// Load related books when page loads
document.addEventListener('DOMContentLoaded', () => {
  loadBookCover()
  loadRelatedBooks()
})
