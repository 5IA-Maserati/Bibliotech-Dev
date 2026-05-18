/* global window */
(function (window) {
  'use strict'

  const placeholderCoverUrl = '/assets/img/common/default_cover.png'

  async function loadGoogleBooksCoverImage (imgElement, isbn) {
    if (!imgElement || !isbn) {
      return
    }

    const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=isbn:${encodeURIComponent(isbn)}`

    try {
      const response = await fetch(apiUrl)
      if (!response.ok) {
        return
      }

      const data = await response.json()
      const imageLinks = data?.items?.[0]?.volumeInfo?.imageLinks
      if (!imageLinks) {
        return
      }

      const coverUrl = imageLinks.extraLarge || imageLinks.large || imageLinks.medium || imageLinks.thumbnail || imageLinks.smallThumbnail
      if (coverUrl) {
        imgElement.src = coverUrl.replace(/^http:/, 'https:')
      }
    } catch (error) {
      // Keep failure silent for UX, but log for debugging
      // eslint-disable-next-line no-console
      console.error('Google Books cover load failed:', error)
    }
  }

  window.BookCovers = window.BookCovers || {}
  window.BookCovers.loadGoogleBooksCoverImage = loadGoogleBooksCoverImage
  window.BookCovers.placeholderCoverUrl = placeholderCoverUrl
})(window)
