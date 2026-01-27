document.addEventListener("DOMContentLoaded", function () {
  const mapContainer = document.getElementById("map");
  if (!mapContainer) return;

  // Coordinate Voghera (centro città)
  const lat = 44.9913;
  const lng = 9.0095;

  // Fix IMPORTANTISSIMO per Leaflet locale
  L.Icon.Default.imagePath = "/libs/dist/images/";

  // Inizializza la mappa
  const map = L.map("map", {
    center: [lat, lng],
    zoom: 15,
    scrollWheelZoom: false
  });

  // Layer OpenStreetMap (serve internet)
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors"
  }).addTo(map);

  // Marker
  L.marker([lat, lng])
    .addTo(map)
    .bindPopup(
      "<strong>Biblioteca Scolastica</strong><br>Istituto Alfieri Maserati<br>Voghera"
    );

  // UX: abilita zoom solo dopo click
  map.on("click", () => map.scrollWheelZoom.enable());
  map.on("mouseout", () => map.scrollWheelZoom.disable());
});
