{{-- Shows only the streams of the chosen class in a lesson form. The server checks it again. --}}
@push('page-scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-timetable-form]').forEach(function (form) {
                var cls = form.querySelector('[name=school_class_id]');
                var stream = form.querySelector('[name=stream_id]');
                if (!cls || !stream) { return; }

                function sync() {
                    Array.prototype.forEach.call(stream.options, function (option) {
                        var show = !option.value || option.dataset.class === cls.value;
                        option.hidden = !show;
                        option.disabled = !show;
                    });
                    if (stream.selectedOptions[0] && stream.selectedOptions[0].disabled) { stream.value = ''; }
                }

                cls.addEventListener('change', sync);
                sync();
            });
        })();
    </script>
@endpush
