export default function initializeDatePicker() {
    document.querySelectorAll('.datepicker').forEach(function(input) {
        flatpickr(input, {
            dateFormat: "d M Y", // Adjusted for year
            onChange: function(selectedDates, dateStr, instance) {
                const issueId = input.dataset.id;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Send AJAX request to update the end date in the database
                fetch(`/issue/${issueId}/edit-date`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token // Add CSRF token here
                    },
                    body: JSON.stringify({ endDate: dateStr }) // Send the selected date as JSON
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
    });
}
