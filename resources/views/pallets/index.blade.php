@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Pallets</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pallets.qr-scanner') }}" class="btn btn-outline-success">
                            <i class="fas fa-qrcode"></i> Scan QR Code
                        </a>
                        <a href="{{ route('pallets.import') }}" class="btn btn-outline-primary">
                            Import Pallets
                        </a>
                        <a href="{{ route('pallets.create') }}" class="btn btn-primary">Add Pallet</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('pallets.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Pallet number, location, or lot">
                            </div>
                            <div class="col-md-3">
                                <label for="school_id" class="form-label">School</label>
                                <select class="form-select" id="school_id" name="school_id">
                                    <option value="">All Schools</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="packing" {{ request('status') === 'packing' ? 'selected' : '' }}>Packing</option>
                                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('pallets.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <div id="bulkActions" class="mb-3" style="display: none;">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span id="selectedCount">0</span> pallet(s) selected
                            <div>
                                <button class="btn btn-outline-success btn-sm" onclick="printSelectedQR()">
                                    <i class="fas fa-qrcode"></i> Print QR Codes
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                                    Clear Selection
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="testBulkFunction()">
                                    Test
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pallets Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Pallet #</th>
                                    <th>Status</th>
                                    <th>School</th>
                                    <th>Location</th>
                                    <th>Lot</th>
                                    <th>Created By</th>
                                    <th>Created</th>
                                    <th>Photos</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pallets as $pallet)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="pallet-checkbox" value="{{ $pallet->pallet_number }}" onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <a href="{{ route('pallets.show', $pallet) }}" class="text-decoration-none">
                                            {{ $pallet->pallet_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $pallet->status === 'delivered' ? 'success' : ($pallet->status === 'shipped' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($pallet->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pallet->school)
                                            <span class="badge bg-info">{{ $pallet->school->name }}</span>
                                        @else
                                            <span class="text-muted">No School</span>
                                        @endif
                                    </td>
                                    <td>{{ $pallet->location ?: '-' }}</td>
                                    <td>{{ $pallet->lot ?: '-' }}</td>
                                    <td>{{ $pallet->creator->name }}</td>
                                    <td>{{ $pallet->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $pallet->photos->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pallets.show', $pallet) }}" class="btn btn-outline-primary">View</a>
                                            <a href="{{ route('pallets.edit', $pallet) }}" class="btn btn-outline-secondary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No pallets found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $pallets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<!-- HTML5-QRCode Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<!-- QR Code Generation - Using reliable API approach -->
<script>
// Simple QR code generation using QR Server API
window.generateQRCode = function(text, size = 200) {
    return `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(text)}`;
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrcodeScanner = null;
    let scannedResult = null;

    // Initialize QR scanner when modal is shown
    document.getElementById('qrScannerModal').addEventListener('shown.bs.modal', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }

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
    });

    // Clean up when modal is hidden
    document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', function() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }
        // Reset UI
        document.getElementById('qr-reader-results').style.display = 'none';
        document.getElementById('goToPalletBtn').style.display = 'none';
        scannedResult = null;
    });

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`QR Code detected: ${decodedText}`);
        scannedResult = decodedText;
        
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

// Bulk selection functions
function toggleSelectAll() {
    console.log('toggleSelectAll called');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.pallet-checkbox');
    
    console.log('Select all checked:', selectAll.checked);
    console.log('Found checkboxes:', checkboxes.length);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    console.log('updateBulkActions called');
    const checkboxes = document.querySelectorAll('.pallet-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const selectAll = document.getElementById('selectAll');
    
    console.log('Found checked checkboxes:', checkboxes.length);
    console.log('Bulk actions element:', bulkActions);
    
    selectedCount.textContent = checkboxes.length;
    
    if (checkboxes.length > 0) {
        console.log('Showing bulk actions');
        bulkActions.style.display = 'block';
    } else {
        console.log('Hiding bulk actions');
        bulkActions.style.display = 'none';
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.pallet-checkbox');
    selectAll.checked = checkboxes.length === allCheckboxes.length;
    selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.pallet-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAll.checked = false;
    selectAll.indeterminate = false;
    
    updateBulkActions();
}

function testBulkFunction() {
    console.log('Test bulk function called');
    const checkboxes = document.querySelectorAll('.pallet-checkbox:checked');
    const palletNumbers = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('Test - Selected pallets:', palletNumbers);
    alert(`Test: ${palletNumbers.length} pallets selected: ${palletNumbers.join(', ')}`);
}

function printSelectedQR() {
    console.log('printSelectedQR called');
    const checkboxes = document.querySelectorAll('.pallet-checkbox:checked');
    const palletNumbers = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('Selected pallets:', palletNumbers);
    
    if (palletNumbers.length === 0) {
        alert('Please select at least one pallet.');
        return;
    }
    
    printQRCodes(palletNumbers);
}

function printQRCode(palletNumber) {
    printQRCodes([palletNumber]);
}

function printQRCodes(palletNumbers) {
    console.log('printQRCodes called with:', palletNumbers);
    console.log('Starting bulk QR code generation for pallets:', palletNumbers);
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    if (!printWindow) {
        alert('Popup blocked. Please allow popups for this site and try again.');
        return;
    }
    
    console.log('Print window opened successfully');
    
    // Generate QR codes for each pallet using reliable API
    const qrCodes = palletNumbers.map(palletNumber => {
        const url = `${window.location.origin}/pallets/${palletNumber}`;
        console.log('Generating QR code for URL:', url);
        return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
    });
    
    console.log('All QR codes generated successfully:', qrCodes.length);
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Pallet QR Codes</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .qr-container { 
                    display: inline-block; 
                    margin: 20px; 
                    text-align: center; 
                    page-break-inside: avoid;
                    border: 1px solid #ddd;
                    padding: 15px;
                    width: 250px;
                }
                .qr-title { 
                    font-weight: bold; 
                    font-size: 16px; 
                    margin-bottom: 10px;
                    color: #333;
                }
                .qr-code { 
                    margin: 10px 0; 
                }
                .qr-url { 
                    font-size: 12px; 
                    color: #666; 
                    word-break: break-all;
                    margin-top: 10px;
                }
                @media print {
                    body { margin: 0; }
                    .qr-container { margin: 10px; }
                }
            </style>
        </head>
        <body>
    `;
    
    palletNumbers.forEach((palletNumber, index) => {
        html += `
            <div class="qr-container">
                <div class="qr-title">DormCoPallet #${palletNumber}</div>
                <div class="qr-code">
                    <img src="${qrCodes[index]}" alt="QR Code for ${palletNumber}" onload="console.log('QR code ${index + 1} loaded')" onerror="console.error('Failed to load QR code ${index + 1}')">
                </div>
                <!-- <div class="qr-url">${window.location.origin}/pallets/${palletNumber}</div> -->
            </div>
        `;
    });
    
    html += `
        </body>
        </html>
    `;
    
    printWindow.document.write(html);
    printWindow.document.close();
    
    // Wait for images to load, then print
    printWindow.onload = function() {
        console.log('Print window loaded');
        setTimeout(() => {
            printWindow.print();
        }, 1000);
    };
}
</script>
@endpush
