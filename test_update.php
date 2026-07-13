<?php
require 'vendor/autoload.php'; 
$app = require_once 'bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

try { 
    App\Models\KonfigurasiBiayaDinamis::updateOrCreate(
        ['id_item_produksi' => 1], 
        [
            'is_biaya_jarak_aktif' => false, 
            'tarif_per_km' => null, 
            'is_biaya_waktu_aktif' => true, 
            'batas_hari_zona_merah' => 1, 
            'batas_hari_zona_kuning' => 3, 
            'biaya_urgensi' => 50000
        ]
    ); 
    echo "OK\n"; 
} catch (\Throwable $e) { 
    echo $e->getMessage() . "\n"; 
}
