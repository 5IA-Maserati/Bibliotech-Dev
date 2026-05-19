/* global window */
(function (window) {
  'use strict'

  const placeholderCoverUrl = '/assets/img/common/default_cover.png'
  const API_TIMEOUT = 5000 // 5 second timeout
  const cache = {} // Cache results to avoid redundant API calls
  const requestQueue = []
  const MAX_CONCURRENT_REQUESTS = 3
  const REQUEST_DELAY_MS = 150
  let activeRequests = 0

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

  function getOpenLibraryCoverUrl (isbn, size = 'L') {
    return `https://covers.openlibrary.org/b/isbn/${encodeURIComponent(isbn)}-${size}.jpg?default=false`
  }

  async function getGoogleBooksCoverUrl (isbn) {
    const apiUrl = `https://www.googleapis.com/books/v1/volumes?q=isbn:${encodeURIComponent(isbn)}`

    try {
      const response = await fetchWithTimeout(apiUrl)
      if (!response.ok) {
        return ''
      }

      const data = await response.json()
      const imageLinks = data?.items?.[0]?.volumeInfo?.imageLinks
      if (!imageLinks) {
        return ''
      }

      const coverUrl = imageLinks.extraLarge || imageLinks.large || imageLinks.medium || imageLinks.thumbnail || imageLinks.smallThumbnail
      return coverUrl ? coverUrl.replace(/^http:/, 'https:') : ''
    } catch (error) {
      // Keep failure silent for UX, but log for debugging
      // eslint-disable-next-line no-console
      console.debug('Google Books cover fetch failed:', error, isbn)
      return ''
    }
  }

  async function getArchiveCoverUrl (isbn) {
    const searchUrl = `https://archive.org/advancedsearch.php?q=isbn:${encodeURIComponent(isbn)}&fl=identifier&rows=1&page=1&output=json`

    try {
      const response = await fetchWithTimeout(searchUrl)
      if (!response.ok) {
        return ''
      }

      const data = await response.json()
      const identifier = data?.response?.docs?.[0]?.identifier
      return identifier ? `https://archive.org/services/img/${encodeURIComponent(identifier)}` : ''
    } catch (error) {
      // Keep failure silent for UX, but log for debugging
      // eslint-disable-next-line no-console
      console.debug('Internet Archive cover fetch failed:', error, isbn)
      return ''
    }
  }

  async function loadBookCoverImage (imgElement, isbn) {
    if (!imgElement || !isbn || !isbn.trim()) {
      return
    }
    const isbnTrimmed = isbn

    if (cache[isbnTrimmed] === null) {
      imgElement.src = placeholderCoverUrl
      return
    }

    if (cache[isbnTrimmed]) {
      imgElement.src = cache[isbnTrimmed]
      return
    }
    queueCoverLoad(imgElement, isbnTrimmed)
  }

  function queueCoverLoad (imgElement, isbn) {
    requestQueue.push({ imgElement, isbn })
    processQueue()
  }

  function processQueue () {
    if (activeRequests >= MAX_CONCURRENT_REQUESTS || requestQueue.length === 0) {
      return
    }

    const task = requestQueue.shift()
    activeRequests += 1
    loadCoverTask(task.imgElement, task.isbn)
      .catch(() => {
        // ignore per-image task errors; fallback will show placeholder
      })
      .finally(() => {
        activeRequests -= 1
        setTimeout(processQueue, REQUEST_DELAY_MS)
      })
  }

  async function loadCoverTask (imgElement, isbnTrimmed) {
    if (!imgElement || !isbnTrimmed) {
      return
    }

    const openLibraryUrl = getOpenLibraryCoverUrl(isbnTrimmed)
    let fallbackStage = 0

    const onErrorHandler = async function () {
      this.onerror = null
      fallbackStage += 1

      if (fallbackStage === 1) {
        const googleUrl = await getGoogleBooksCoverUrl(isbnTrimmed)
        if (googleUrl) {
          cache[isbnTrimmed] = googleUrl
          this.src = googleUrl
          this.onerror = onErrorHandler
          return
        }
      }

      if (fallbackStage <= 2) {
        const archiveUrl = await getArchiveCoverUrl(isbnTrimmed)
        if (archiveUrl) {
          cache[isbnTrimmed] = archiveUrl
          this.src = archiveUrl
          this.onerror = onErrorHandler
          return
        }
      }

      cache[isbnTrimmed] = null
      this.src = placeholderCoverUrl
    }

    const onLoadHandler = function () {
      cache[isbnTrimmed] = openLibraryUrl
      this.onload = null
    }

    imgElement.onerror = onErrorHandler
    imgElement.onload = onLoadHandler
    imgElement.src = openLibraryUrl
  }

  window.BookCovers = window.BookCovers || {}
  window.BookCovers.loadBookCoverImage = loadBookCoverImage
  window.BookCovers.loadGoogleBooksCoverImage = loadBookCoverImage
  window.BookCovers.placeholderCoverUrl = placeholderCoverUrl
})(window)
