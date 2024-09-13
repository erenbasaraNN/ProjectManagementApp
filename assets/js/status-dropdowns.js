export function initializeStatusDropdowns() {
    document.querySelectorAll('.status-tagify').forEach(function (input) {
        // Initialize Tagify for status fields with single tag selection mode
        var tagify = new Tagify(input, {
            whitelist: ["Başlanacak", "Aktif Durumda", "Tamamlandı", "İptal"],
            dropdown: {
                enabled: 1, // Enable dropdown suggestions
                maxItems: 4, // Only show these statuses
                closeOnSelect: true, // Close dropdown after selection
                position: 'all' // Dropdown below the input
            },
            enforceWhitelist: true,  // Force user to choose from the predefined statuses
            maxTags: 1,  // Limit to one tag (single selection)
            addTagOnBlur: false,  // Prevent adding tag when input loses focus
            editTags: false,  // Disable editing of the tags
            mode: 'select',  // Ensure only one can be selected
            userInput: false,  // Disable any manual input from the user
        });

        // If status is empty, default to 'Başlanacak'
        if (tagify.value.length === 0) {
            tagify.addTags([{ value: 'Başlanacak' }]);
            setStatusBackgroundColor('Başlanacak', tagify);
        } else {
            setStatusBackgroundColor(tagify.value[0].value, tagify);
        }

        // Listen to 'change' event to update the status and background color
        tagify.on('change', function (e) {
            const issueId = input.dataset.id;

            // Get the first selected value from Tagify's value
            const newStatus = tagify.value.length > 0 ? tagify.value[0].value : "Başlanacak";

            // If no status selected, set to 'Başlanacak'
            if (!newStatus) {
                console.error('No status selected');
                return;  // Prevent sending empty status to the database
            }

            // Send AJAX request to update the status in the database
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
                        // Set the background color after status is updated
                        setStatusBackgroundColor(newStatus, tagify);
                    }
                }).catch(error => console.error('Error updating status:', error));
        });

        // Listen to 'input' event to update background color dynamically
        tagify.on('input', function () {
            const selectedStatus = tagify.value.length > 0 ? tagify.value[0].value : "Başlanacak";
            setStatusBackgroundColor(selectedStatus, tagify);
        });
    });
}

// Function to set background color based on the selected status
function setStatusBackgroundColor(status, tagify) {
    let backgroundColor = '#ffffff';  // Default background color

    switch (status) {
        case 'Tamamlandı':
            backgroundColor = '#46c965';  // Completed (green)
            break;
        case 'Aktif Durumda':
            backgroundColor = '#e3d324';  // In Progress (yellow)
            break;
        case 'İptal':
            backgroundColor = '#f12436';  // Cancelled (red)
            break;
        case 'Başlanacak':
            backgroundColor = '#29a5d1';  // To Do (blue)
            break;
    }

    tagify.DOM.input.style.backgroundColor = backgroundColor;

    // Apply the background color to the tag (if a tag is selected)
    const tag = tagify.DOM.scope.querySelector('.tagify__tag');
    if (tag) {
        tag.style.backgroundColor = backgroundColor;
    }
}
