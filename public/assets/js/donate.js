/* global L */

document.addEventListener('DOMContentLoaded', function () {
  const mapContainer = document.getElementById('map')
  if (!mapContainer) return

  const lat = 44.98682625918026
  const lng = 8.997528028540428

  // Path corretto delle immagini Leaflet
  L.Icon.Default.imagePath = '../assets/js/libs/.dist/images/'

  // Impostazioni zoom e bounds: permettere di vedere almeno tutta la regione
  const minZoom = 8
  const maxZoom = 25

  const map = L.map('map', {
    center: [lat, lng],
    zoom: 20,
    minZoom,
    maxZoom,
    scrollWheelZoom: false,
    attributionControl: false
  })

  // Bounds più ampi per coprire l'intera regione (modifica +/- per stringere/allargare)
  const regionPadding = 1.5 // gradi ~ circa 100-150 km depending on latitude
  const bounds = L.latLngBounds(
    [lat - regionPadding, lng - regionPadding],
    [lat + regionPadding, lng + regionPadding]
  )

  // Imposta i limiti di pan e una viscosità che 'rimbalza' verso i bounds
  map.setMaxBounds(bounds)
  map.options.maxBoundsViscosity = 0.8

  // Carica i tiles rispettando i limiti di zoom
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom,
    maxZoom
  }).addTo(map)

  // Far partire la vista mostrando l'intera regione (ma l'utente può zoomare dentro)
  map.fitBounds(bounds, { padding: [20, 20] })

  // Aggiungi il tuo controllo
  L.control.attribution({
    prefix: false
  }).addTo(map)

  map.attributionControl.addAttribution(`
  <a class="gmaps-link" href="https://maps.app.goo.gl/fhTpU3kYApsbhmp49" target="_blank">
    <img src="../assets/img/icons/google-map-icon.png" alt="Google Maps" class="gmaps-icon">
    Apri su Google Maps
  </a>
`)

  L.marker([lat, lng])
    .addTo(map)
    .bindPopup('<strong>Biblioteca Scolastica</strong><br>Istituto Alfieri Maserati<br>Voghera')

  // Bottone per ri-centrare la mappa sul punto biblioteca
  const centerControl = L.control({ position: 'topleft' })
  centerControl.onAdd = function (map) {
    const div = L.DomUtil.create('div', 'leaflet-control leaflet-bar')
    const button = L.DomUtil.create('a', '', div)
    button.href = '#'
    button.title = 'Centra sulla biblioteca'
    button.innerHTML = '⊙'
    button.style.fontSize = '18px'
    button.style.width = '36px'
    button.style.height = '36px'
    button.style.lineHeight = '36px'
    button.style.textAlign = 'center'
    button.style.cursor = 'pointer'
    L.DomEvent.disableClickPropagation(button)
    button.addEventListener('click', function (e) {
      e.preventDefault()
      map.fitBounds(bounds, { padding: [20, 20] })
    })
    return div
  }
  centerControl.addTo(map)

  map.on('click', () => map.scrollWheelZoom.enable())
  map.on('mouseout', () => map.scrollWheelZoom.disable())
})
