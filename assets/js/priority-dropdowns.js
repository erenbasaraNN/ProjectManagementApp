export default function initializePriorityDropdowns() {
    // Function to update background color based on the priority
    function updatePriorityBackground(select) {
        const selectedPriority = select.value;
        let backgroundColor;

        switch (selectedPriority) {
            case 'Düşük':
                backgroundColor = '#007bff'; // Low: Blue
                break;
            case 'Orta':
                backgroundColor = '#6f42c1'; // Medium: Purple
                break;
            case 'Yüksek':
                backgroundColor = '#dc3545'; // High: Red
                break;
            default:
                backgroundColor = '#ffffff'; // Default white background
        }

        // Apply the background color to the dropdown
        select.style.backgroundColor = backgroundColor;
    }

    // Attach the change event listener to all .priority-dropdown elements
    document.querySelectorAll('.priority-dropdown').forEach(function(select) {
        // Initialize the correct background color on page load
        updatePriorityBackground(select);

        // Handle priority dropdown change
        select.addEventListener('change', function() {
            const issueId = this.dataset.id;
            const newPriority = this.value;

            // Send AJAX request to update priority in the database
            fetch(`/issue/${issueId}/edit-priority`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ priority: newPriority })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Priority updated successfully');
                        // Update the background color after successful priority update
                        updatePriorityBackground(select);
                    }
                }).catch(error => console.error('Error updating priority:', error));
        });
    });
}
