export default function initializeDatePicker() {
    document.querySelectorAll('.datepicker').forEach(function(input) {
        flatpickr(input, {
            dateFormat: "d M"
        });
    });
}
