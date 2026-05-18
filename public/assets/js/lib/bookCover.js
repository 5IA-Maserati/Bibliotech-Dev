/* global window */
(function (window) {
  'use strict'

  const placeholderCoverUrl = '/assets/img/common/default_cover.png'
  const API_TIMEOUT = 5000 // 5 second timeout
  const cache = {} // Cache results to avoid redundant API calls

  async function fetchWithTimeout (url, timeout = API_TIMEOUT) {
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), timeout)

    try {
      const response = await fetch(url, { signal: controller.signal })
      clearTimeout(timeoutId)
      return response
    } catch (error) {
      clearTimeout(timeoutId)
      throw error
    }
  }

  async function loadGoogleBooksCoverImage (imgElement, isbn) {
    if (!imgElement || !isbn || !isbn.trim()) {
      return
    }

    const isbnTrimmed = isbn.trim()

    // Check cache first
    if (cache[isbnTrimmed]) {
      if (cache[isbnTrimmed].url) {
        imgElement.src = cache[isbnTrimmed].url
      }
      return
    }

    const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=isbn:${encodeURIComponent(isbnTrimmed)}`

    try {
      const response = await fetchWithTimeout(apiUrl)
      if (!response.ok) {
        cache[isbnTrimmed] = { url: null }
        return
      }

      const data = await response.json()
      const imageLinks = data?.items?.[0]?.volumeInfo?.imageLinks
      if (!imageLinks) {
        cache[isbnTrimmed] = { url: null }
        return
      }

      const coverUrl = imageLinks.extraLarge || imageLinks.large || imageLinks.medium || imageLinks.thumbnail || imageLinks.smallThumbnail
      if (coverUrl) {
        const httpsUrl = coverUrl.replace(/^http:/, 'https:')
        cache[isbnTrimmed] = { url: httpsUrl }
        imgElement.src = httpsUrl
      } else {
        cache[isbnTrimmed] = { url: null }
      }
    } catch (error) {
      // Cache the failure to avoid repeated attempts
      cache[isbnTrimmed] = { url: null }
      // Keep failure silent for UX, but log for debugging
      // eslint-disable-next-line no-console
      console.debug('Google Books cover load failed:', error, isbn)
    }
  }

  window.BookCovers = window.BookCovers || {}
  window.BookCovers.loadGoogleBooksCoverImage = loadGoogleBooksCoverImage
  window.BookCovers.placeholderCoverUrl = placeholderCoverUrl
})(window)
