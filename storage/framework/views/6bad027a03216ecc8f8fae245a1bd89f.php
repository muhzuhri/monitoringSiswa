<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dibatalkan - Keamanan Diperlukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full glass rounded-3xl shadow-2xl p-8 text-center animate-fade-in-up">
        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shield-alt fa-3x"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Koneksi Tidak Aman!</h1>
        <p class="text-gray-600 mb-6 font-medium">Sistem memerlukan koneksi <strong>HTTPS</strong> untuk melindungi data lokasi dan aktivitas Anda.</p>
        
        <div class="bg-red-50 border-l-4 border-red-500 p-4 text-left mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        Browser memblokir fitur Geolocation (GPS) jika tidak menggunakan protokol keamanan SSL/HTTPS.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <a href="https://<?php echo e(request()->getHttpHost()); ?><?php echo e(request()->getRequestUri()); ?>" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 transform hover:scale-[1.02]">
                <i class="fas fa-lock me-2"></i> Akses via HTTPS
            </a>
            
            <p class="text-xs text-gray-400 mt-4 italic">
                *Jika Anda masih melihat pesan ini setelah klik tombol di atas, hubungi administrator untuk pemasangan sertifikat SSL.
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/errors/https-required.blade.php ENDPATH**/ ?>