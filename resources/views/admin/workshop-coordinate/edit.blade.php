@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-map-marker-alt mr-2"></i> Pengaturan Koordinat Workshop
    </h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Lokasi Workshop (Titik 0 Km)</h6>
        </div>
        <div class="card-body">
            <p>Geser marker pada peta di bawah ini untuk menentukan titik koordinat workshop Anda. Koordinat ini akan digunakan sebagai pusat (Titik 0 Km) dalam perhitungan jarak pengiriman pesanan.</p>
            
            <form action="{{ route('admin.workshop-coordinate.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $coordinate->latitude) }}" readonly required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $coordinate->longitude) }}" readonly required>
                    </div>
                </div>

                <div class="search-map-container" style="position: relative; margin-bottom: 10px;">
                    <input type="text" id="searchAlamat" class="form-control" placeholder="Cari daerah / jalan untuk lokasi workshop...">
                    <button type="button" id="btnSearchAlamat" style="position: absolute; right: 0; top: 0; height: 100%; border: none; background: #4e73df; color: white; padding: 0 15px; border-radius: 0 4px 4px 0;"><i class="fas fa-search"></i></button>
                    <div id="search-suggestions" style="position: absolute; top: 100%; left: 0; width: 100%; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 0 0 8px 8px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); max-height: 250px; overflow-y: auto; z-index: 9999; margin-top: 4px; padding: 0; display: none;"></div>
                </div>

                <div id="map-admin" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;"></div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Load Leaflet & Fullscreen Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.js"></script>

<style>
    #search-suggestions button {
        display: block;
        width: 100%;
        text-align: left;
        padding: 10px 15px;
        background: transparent;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }
    #search-suggestions button:last-child {
        border-bottom: none;
    }
    #search-suggestions button:hover {
        background-color: #f8f9fa;
        color: #4e73df;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        
        let initialLat = parseFloat(latInput.value) || -3.2994;
        let initialLng = parseFloat(lngInput.value) || 114.5933;

        const mapAdmin = L.map('map-admin', {
            fullscreenControl: true,
            fullscreenControlOptions: { position: 'topleft' }
        }).setView([initialLat, initialLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapAdmin);

        let marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(mapAdmin);

        function updateInputsAndMap(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            mapAdmin.setView([lat, lng], 15);
        }

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);
            mapAdmin.panTo(position);
        });
        
        mapAdmin.on('click', function(e) {
            const position = e.latlng;
            marker.setLatLng(position);
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);
        });

        // Search Feature Logic
        let searchTimeoutId;
        const searchInput = document.getElementById('searchAlamat');
        const suggestionsBox = document.getElementById('search-suggestions');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeoutId);
            const query = this.value;
            
            if(!query) {
                suggestionsBox.style.display = 'none';
                return;
            }

            searchTimeoutId = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=5`)
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if(data.length > 0) {
                        data.forEach(item => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = item.display_name;
                            btn.addEventListener('click', function() {
                                searchInput.value = item.display_name;
                                suggestionsBox.style.display = 'none';
                                const lat = parseFloat(item.lat);
                                const lon = parseFloat(item.lon);
                                marker.setLatLng([lat, lon]);
                                updateInputsAndMap(lat, lon);
                            });
                            suggestionsBox.appendChild(btn);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                });
            }, 500);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });

        document.getElementById('btnSearchAlamat').addEventListener('click', function() {
            const query = searchInput.value;
            if(!query) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=1`)
            .then(response => response.json())
            .then(data => {
                if(data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    marker.setLatLng([lat, lon]);
                    updateInputsAndMap(lat, lon);
                    suggestionsBox.style.display = 'none';
                } else {
                    alert('Alamat tidak ditemukan');
                }
            });
        });

        // Fix map rendering issues when entering/exiting fullscreen
        mapAdmin.on('enterFullscreen', function(){
            setTimeout(function(){
                mapAdmin.invalidateSize();
            }, 200);
        });

        mapAdmin.on('exitFullscreen', function(){
            setTimeout(function(){
                mapAdmin.invalidateSize();
            }, 200);
        });
    });
</script>
@endsection
