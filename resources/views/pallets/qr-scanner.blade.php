@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Scan QR Code</h3>
                    <a href="{{ route('pallets.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Pallets
                    </a>
                </div>

                <div class="card-body">
                    <div class="text-center">
                        <div id="qr-reader" style="width: 100%; max-width: 100%; margin: 0 auto; min-height: 400px;"></div>
                        <div id="qr-reader-results" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <strong>QR Code Detected:</strong>
                                <div id="qr-result"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- HTML5-QRCode Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrcodeScanner = null;

    // Initialize QR scanner
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 300, height: 300 },
            aspectRatio: 1.0,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        },
        /* verbose= */ false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`QR Code detected: ${decodedText}`);
        
        // Stop scanning
        html5QrcodeScanner.clear();
        
        // Check if it's a pallet URL or pallet number
        let palletNumber = null;
        
        // Check if it's a full URL
        if (decodedText.includes('/pallets/')) {
            const urlParts = decodedText.split('/pallets/');
            if (urlParts.length > 1) {
                palletNumber = urlParts[1].split('?')[0].split('#')[0];
            }
        } else if (decodedText.match(/^[A-Z0-9-]+$/)) {
            // Check if it's just a pallet number (alphanumeric with dashes)
            palletNumber = decodedText;
        }
        
        if (palletNumber) {
            // Automatically redirect to pallet
            window.location.href = `/pallets/${palletNumber}`;
        } else {
            // Show error message for invalid QR codes
            document.getElementById('qr-result').textContent = 'Invalid QR code - not a pallet';
            document.getElementById('qr-reader-results').style.display = 'block';
        }
    }

    function onScanFailure(error) {
        // Handle scan failure, usually ignored
        console.log('QR Code scan failed:', error);
    }
});
</script>
@endpush
