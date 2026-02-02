document.addEventListener('DOMContentLoaded', function () {
  const mapContainer = document.getElementById('map')
  if (!mapContainer) return

  const lat = 44.98682625918026
  const lng = 8.997528028540428

  // Path corretto delle immagini Leaflet
  L.Icon.Default.imagePath = '../assets/js/libs/.dist/images/'

  const map = L.map('map', {
    center: [lat, lng],
    zoom: 25,
    scrollWheelZoom: false,
    attributionControl: false
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map)

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

  map.on('click', () => map.scrollWheelZoom.enable())
  map.on('mouseout', () => map.scrollWheelZoom.disable())
})
