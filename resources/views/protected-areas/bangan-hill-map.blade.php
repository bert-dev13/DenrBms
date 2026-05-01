@extends('layouts.app')

@section('title', 'Bangan Hill National Park Map')
@section('header', 'Protected Area Boundary Map')

@section('head')
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    @vite(['resources/css/protected-area-map.css', 'resources/js/protected-area-map.js'])
@endsection

@section('content')
    <div class="map-viewer-page" id="map-viewer-page">
        <header class="map-viewer-header">
            <div>
                <h2 class="map-viewer-title">DENR BMS Map Viewer</h2>
                <p class="map-viewer-subtitle">Bangan Hill National Park - Municipality of Bayombong</p>
            </div>
            <button id="sidebar-toggle" class="map-ui-btn map-header-toggle" type="button" aria-label="Toggle map sidebar">
                <span>Panel</span>
            </button>
        </header>

        <div class="map-workspace">
            <aside class="map-control-sidebar" id="map-control-sidebar" aria-label="Map controls and details">
                <div class="map-sidebar-section">
                    <h3>Boundary Description</h3>
                    <p>
                        Bangan Hill National Park is located in the Municipality of Bayombong, Province of Nueva
                        Vizcaya and covers portions of Barangays Vista Alegre and Magsaysay, with an aggregate area of
                        12.77 hectares. It is bounded by Bayombong Cadastre in the south; NGO project lots on the
                        southwest and west; and surveyed parcels on the north and northeast. The area falls within
                        Timberland under LC Map No. 1137 and within the Magat River Forest Reserve under Proclamation
                        No. 573.
                    </p>
                </div>

                <div class="map-sidebar-section">
                    <h3>Protected Area Profile</h3>
                    <dl class="map-meta-list">
                        <div>
                            <dt>Name</dt>
                            <dd>Bangan Hill National Park</dd>
                        </div>
                        <div>
                            <dt>Province</dt>
                            <dd>Nueva Vizcaya</dd>
                        </div>
                        <div>
                            <dt>Municipality</dt>
                            <dd>Bayombong</dd>
                        </div>
                        <div>
                            <dt>Area</dt>
                            <dd>12.77 hectares</dd>
                        </div>
                    </dl>
                </div>

                <div class="map-sidebar-section">
                    <h3>Map Layers</h3>
                    <div class="map-layer-switch">
                        <button class="map-layer-btn active" type="button" data-layer="street">Street View</button>
                        <button class="map-layer-btn" type="button" data-layer="satellite">Satellite View</button>
                    </div>
                </div>
            </aside>

            <section class="map-main-panel" aria-label="Protected area map">
                <button id="mobile-sidebar-toggle" class="map-ui-btn map-mobile-toggle" type="button" aria-label="Open controls">
                    Controls
                </button>

                <div id="protected-area-map" class="protected-area-map"></div>

                <div class="map-floating-controls" id="map-floating-controls">
                    <button id="zoom-in-btn" class="map-ui-btn" type="button" title="Zoom in" aria-label="Zoom in">+</button>
                    <button id="zoom-out-btn" class="map-ui-btn" type="button" title="Zoom out" aria-label="Zoom out">-</button>
                    <button id="reset-view-btn" class="map-ui-btn" type="button" title="Reset view" aria-label="Reset view">Reset</button>
                    <button id="toggle-layer-menu-btn" class="map-ui-btn" type="button" title="Toggle layer options" aria-label="Toggle layer options">
                        Layers
                    </button>
                </div>
            </section>
        </div>
    </div>
@endsection

