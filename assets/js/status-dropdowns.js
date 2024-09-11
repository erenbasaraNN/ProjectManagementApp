export default function initializeStatusDropdowns() {
    // Function to update background color based on the status
    function updateStatusBackground(select) {
        const selectedStatus = select.value;
        let backgroundColor;

        switch (selectedStatus) {
            case 'Completed':
                backgroundColor = '#DAF7A6'; // Completed
                break;
            case 'In Progress':
                backgroundColor = '#FFC300'; // In Progress
                break;
            case 'Cancelled':
                backgroundColor = '#900C3F'; // Blocked
                break;
            case 'To DO':
                backgroundColor = '#29a5d1'; // Not Started
                break;
            default:
                backgroundColor = '#ffffff'; // Default white background
        }

        // Apply the background color to the dropdown
        select.style.backgroundColor = backgroundColor;
    }

    // Attach the change event listener to all .status-dropdown elements
    document.querySelectorAll('.status-dropdown').forEach(function(select) {
        // Initialize the correct background color on page load
        updateStatusBackground(select);

        // Handle status dropdown change
        select.addEventListener('change', function() {
            const issueId = this.dataset.id;
            const newStatus = this.value;

            // Send AJAX request to update status in the database
            fetch(`/issue/${issueId}/edit-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status: newStatus })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Status updated successfully');
                        // Update the background color after successful status update
                        updateStatusBackground(select);
                    }
                }).catch(error => console.error('Error updating status:', error));
        });
    });
}
