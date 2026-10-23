{{-- Hides the header, the sidebar and the controls when a timetable is printed (or saved as PDF). --}}
@push('styles')
    <style>
        @media print {
            .header, .sidebar, .copyright-footer, .d-print-none, .alert { display: none !important; }
            .page-wrapper { margin: 0 !important; padding: 0 !important; }
            .card { border: 0 !important; box-shadow: none !important; }
            .attendance-card { break-inside: avoid; }
        }
    </style>
@endpush
