// Script della Pagina Donazione

document.addEventListener('DOMContentLoaded', function() {
  // Inizializza Mappa Leaflet
  initializeMap();

  // Effetto scroll fluido
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // Aggiungi animazione alle info box durante lo scroll
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1
  });

  document.querySelectorAll('.info-box').forEach(box => {
    box.style.opacity = '0';
    box.style.transform = 'translateY(20px)';
    box.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(box);
  });

  console.log('Pagina donazione caricata con successo');
});

// Initialize Leaflet Map
function initializeMap() {
  // Coordinate Istituto Alfieri Maserati, Voghera
  const latitude = 44.98707188423608;
  const longitude = 8.997333111340781;

  // Crea mappa
  const map = L.map('map').setView([latitude, longitude], 16);

  // Aggiungi tile layer OpenStreetMap 
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);

  // Aggiungi marker
  const marker = L.marker([latitude, longitude]).addTo(map);
  marker.bindPopup('<strong>Istituto Alfieri Maserati</strong><br>Voghera (PV)<br><br>Biblioteca Scolastica').openPopup();

  // Aggiungi cerchio attorno alla posizione
  L.circle([latitude, longitude], {
    color: '#5a7b94',
    fillColor: '#6995ad',
    fillOpacity: 0.1,
    radius: 100
  }).addTo(map);
}
