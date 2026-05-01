document.addEventListener('DOMContentLoaded', () => {
    const mapViewerPage = document.getElementById('map-viewer-page');
    const mapElement = document.getElementById('protected-area-map');
    const sidebarToggleButton = document.getElementById('sidebar-toggle');
    const mobileSidebarToggleButton = document.getElementById('mobile-sidebar-toggle');
    const zoomInButton = document.getElementById('zoom-in-btn');
    const zoomOutButton = document.getElementById('zoom-out-btn');
    const resetViewButton = document.getElementById('reset-view-btn');
    const toggleLayerMenuButton = document.getElementById('toggle-layer-menu-btn');
    const layerButtons = document.querySelectorAll('.map-layer-btn');

    if (!mapElement) {
        return;
    }

    const coordinates = {
        northwest: { lat: 16.48069, lng: 121.13415 },
        southeast: { lat: 16.47600, lng: 121.14033 },
    };

    const rectanglePolygon = [
        [coordinates.northwest.lat, coordinates.northwest.lng], // NW
        [coordinates.northwest.lat, coordinates.southeast.lng], // NE
        [coordinates.southeast.lat, coordinates.southeast.lng], // SE
        [coordinates.southeast.lat, coordinates.northwest.lng], // SW
    ];

    const map = L.map('protected-area-map', {
        zoomControl: false,
    });

    const osmStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; OpenStreetMap contributors',
    });

    const esriSatelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 20,
            attribution: 'Tiles &copy; Esri',
        }
    );

    osmStreetLayer.addTo(map);

    const protectedAreaPolygon = L.polygon(rectanglePolygon, {
        color: '#2e7d32',
        weight: 3,
        fillColor: '#66bb6a',
        fillOpacity: 0.4,
    }).addTo(map);

    protectedAreaPolygon.bindPopup(`
        <div class="protected-map-popup">
            <h4>Bangan Hill National Park</h4>
            <p><strong>Province:</strong> Nueva Vizcaya</p>
            <p><strong>Municipality:</strong> Bayombong</p>
            <p><strong>Area:</strong> 12.77 hectares</p>
        </div>
    `);

    const polygonBounds = protectedAreaPolygon.getBounds();
    const fitToProtectedArea = () => {
        map.fitBounds(polygonBounds, {
            padding: [35, 35],
            maxZoom: 17,
        });
    };

    fitToProtectedArea();
    mapElement.classList.add('map-ready');

    const polygonCenter = polygonBounds.getCenter();
    L.marker(polygonCenter)
        .addTo(map)
        .bindTooltip('Bangan Hill National Park', { permanent: false, direction: 'top' });

    let activeBaseLayer = 'street';
    const setActiveLayer = (layerName) => {
        if (layerName === activeBaseLayer) {
            return;
        }

        if (layerName === 'street') {
            map.removeLayer(esriSatelliteLayer);
            map.addLayer(osmStreetLayer);
        } else {
            map.removeLayer(osmStreetLayer);
            map.addLayer(esriSatelliteLayer);
        }

        activeBaseLayer = layerName;
        layerButtons.forEach((button) => {
            const isActive = button.dataset.layer === layerName;
            button.classList.toggle('active', isActive);
        });
    };

    layerButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setActiveLayer(button.dataset.layer);
        });
    });

    zoomInButton?.addEventListener('click', () => map.zoomIn());
    zoomOutButton?.addEventListener('click', () => map.zoomOut());
    resetViewButton?.addEventListener('click', () => fitToProtectedArea());

    toggleLayerMenuButton?.addEventListener('click', () => {
        const firstLayerButton = document.querySelector('.map-layer-btn');
        firstLayerButton?.focus();
    });

    sidebarToggleButton?.addEventListener('click', () => {
        mapViewerPage?.classList.toggle('sidebar-collapsed');
        window.setTimeout(() => map.invalidateSize(), 340);
    });

    mobileSidebarToggleButton?.addEventListener('click', () => {
        mapViewerPage?.classList.toggle('sidebar-open-mobile');
        window.setTimeout(() => map.invalidateSize(), 340);
    });

    map.on('click', () => {
        if (window.innerWidth <= 1100) {
            mapViewerPage?.classList.remove('sidebar-open-mobile');
        }
    });

    window.addEventListener('resize', () => {
        map.invalidateSize();
        if (window.innerWidth > 1100) {
            mapViewerPage?.classList.remove('sidebar-open-mobile');
        }
    });

    map.whenReady(() => {
        window.setTimeout(() => {
            map.invalidateSize();
            fitToProtectedArea();
        }, 120);
    });

    L.control
        .layers(
            {
                Street: osmStreetLayer,
                Satellite: esriSatelliteLayer,
            },
            {
                'Protected Area Boundary': protectedAreaPolygon,
            },
            { collapsed: true, position: 'bottomright' }
        )
        .addTo(map);
});
