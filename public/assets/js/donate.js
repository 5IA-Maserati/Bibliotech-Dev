/* global L */

document.addEventListener('DOMContentLoaded', () => {
  const mapContainer = document.getElementById('map')
  if (!mapContainer) return

  // Coordinates of the library
  const lat = 44.98682625918026
  const lng = 8.997528028540428

  // Correct path for Leaflet marker icons
  L.Icon.Default.imagePath = '/assets/js/libs/.dist/images/'

  // Zoom configuration
  const minZoom = 8
  const maxZoom = 25
  const initialZoom = 20

  // Create the map
  const map = L.map('map', {
    center: [lat, lng],
    zoom: initialZoom,
    minZoom,
    maxZoom,
    scrollWheelZoom: false,
    attributionControl: false
  })

  // Define the bounds for the entire region (modify regionPadding to adjust coverage)
  const regionPadding = 1.5 // degrees, ~100-150 km depending on latitude
  const bounds = L.latLngBounds(
    [lat - regionPadding, lng - regionPadding],
    [lat + regionPadding, lng + regionPadding]
  )

  // Set map pan limits and smooth 'bounce' when reaching the bounds
  map.setMaxBounds(bounds)
  map.options.maxBoundsViscosity = 0.8

  // Load OpenStreetMap tiles within zoom limits
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom,
    maxZoom
  }).addTo(map)

  // Fit the map view to show the entire region initially
  map.fitBounds(bounds, { padding: [20, 20] })

  // Add attribution control
  L.control.attribution({ prefix: false }).addTo(map)
  map.attributionControl.addAttribution(`
    <a class="gmaps-link" href="https://maps.app.goo.gl/fhTpU3kYApsbhmp49" target="_blank">
      <img src="/assets/img/icons/google-map-icon.png" alt="Google Maps" class="gmaps-icon">
      Open in Google Maps
    </a>
  `)

  // Add a marker for the library with popup info
  L.marker([lat, lng])
    .addTo(map)
    .bindPopup('<strong>School Library</strong><br>Alfieri Maserati Institute<br>Voghera')

  // Add a custom control button to recenter the map on the library
  const centerControl = L.control({ position: 'topleft' })
  centerControl.onAdd = (map) => {
    const div = L.DomUtil.create('div', 'leaflet-control leaflet-bar')
    const button = L.DomUtil.create('a', '', div)
    button.href = '#'
    button.title = 'Center on library'
    button.innerHTML = '⊙'

    // Style the button
    Object.assign(button.style, {
      fontSize: '18px',
      width: '36px',
      height: '36px',
      lineHeight: '36px',
      textAlign: 'center',
      cursor: 'pointer'
    })

    // Prevent map click propagation
    L.DomEvent.disableClickPropagation(button)

    // Recenter map on click
    button.addEventListener('click', (e) => {
      e.preventDefault()
      map.fitBounds(bounds, { padding: [20, 20] })
    })

    return div
  }
  centerControl.addTo(map)

  // Enable scroll zoom on map click and disable on mouse out
  map.on('click', () => map.scrollWheelZoom.enable())
  map.on('mouseout', () => map.scrollWheelZoom.disable())
})
