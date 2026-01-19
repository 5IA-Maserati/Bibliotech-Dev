// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    setupScrollAnimations();
    setupSmoothScroll();
});

// Initialize OpenLayers Map
function initializeMap() {
    // Coordinates for Istituto Alfieri Maserati, Voghera
    const longitude = 8.997333111340781;
    const latitude = 44.98707188423608;
    
    // Convert coordinates to Web Mercator projection
    const coordinate = ol.proj.fromLonLat([longitude, latitude]);
    
    // Create OpenStreetMap layer
    const osmLayer = new ol.layer.Tile({
        source: new ol.source.OSM()
    });
    
    // Create marker feature
    const markerFeature = new ol.Feature({
        geometry: new ol.geom.Point(coordinate)
    });
    
    // Create marker style with custom SVG icon
    const markerStyle = new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 1],
            src: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMiIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDMyIDQwIj48cGF0aCBmaWxsPSIjNWE3Yjk0IiBkPSJNMTYgMEMzLjEgMCAwIDEwIDAgMTZjMCA2IDE2IDI0IDE2IDI0czE2LTE4IDE2LTI0YzAtNi0zLjEtMTYtMTYtMTZ6Ii8+PGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz48L3N2Zz4=',
            scale: 1.5
        })
    });
    
    markerFeature.setStyle(markerStyle);
    
    // Create vector layer with marker
    const vectorLayer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [markerFeature]
        })
    });
    
    // Create map
    const map = new ol.Map({
        target: 'map',
        layers: [osmLayer, vectorLayer],
        view: new ol.View({
            center: coordinate,
            zoom: 16
        }),
        controls: []
    });
    
    // Add default controls
    map.addControl(new ol.control.Zoom());
    map.addControl(new ol.control.Attribution());
    
    // Add interaction for click on marker to show popup
    map.on('click', function(evt) {
        const features = map.getFeaturesAtPixel(evt.pixel);
        if (features.length > 0) {
            alert('📍 Istituto Alfieri Maserati\nVoghera\n\nOrari: Lunedì-Venerdì 09:00-13:00');
        }
    });
    
    // Change cursor on hover over marker
    map.on('pointermove', function(evt) {
        const isMarker = map.hasFeatureAtPixel(evt.pixel);
        map.getTargetElement().style.cursor = isMarker ? 'pointer' : 'grab';
    });
}

// Setup scroll animations for info boxes
function setupScrollAnimations() {
    const infoBoxes = document.querySelectorAll('.info-box');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    infoBoxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px)';
        box.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(box);
    });
}

// Setup smooth scroll for anchor links
function setupSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}