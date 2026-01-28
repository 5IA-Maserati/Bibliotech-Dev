import L from 'leaflet'

document.addEventListener('DOMContentLoaded', function () {
  const mapContainer = document.getElementById('map')
  if (!mapContainer) return

  // Coordinate Voghera (centro città)
  const lat = 44.9913
  const lng = 9.0095

  // Imposta path corretto delle immagini di Leaflet
  L.Icon.Default.imagePath = '../assets/libs/leaflet/images/'

  // Inizializza la mappa
  const map = L.map('map', {
    center: [lat, lng],
    zoom: 15,
    scrollWheelZoom: false
  })

  // Layer OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map)

  // Marker
  L.marker([lat, lng])
    .addTo(map)
    .bindPopup('<strong>Biblioteca Scolastica</strong><br>Istituto Alfieri Maserati<br>Voghera')

  // UX: abilita zoom solo dopo click
  map.on('click', () => map.scrollWheelZoom.enable())
  map.on('mouseout', () => map.scrollWheelZoom.disable())
})
