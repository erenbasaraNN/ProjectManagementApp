export default function initializeDatePicker(input) {
    const inputs = input ? [input] : document.querySelectorAll('.datepicker');
    inputs.forEach(function(input) {
        if (!input.classList.contains('flatpickr-initialized')) {
            flatpickr(input, {
                dateFormat: "d M Y",
                onChange: function(selectedDates, dateStr, instance) {
                    const issueId = input.dataset.id;
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(`/issue/${issueId}/edit-date`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ endDate: dateStr })
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    }).then(data => {
                        if (data.status === 'success') {
                            console.log('Date updated successfully');
                        } else {
                            console.error('Error updating date:', data.message);
                        }
                    }).catch(error => console.error('Error updating date:', error));
                }
            });
            input.classList.add('flatpickr-initialized');
        }
    });
}