export function initializePriorityDropdowns() {
    document.querySelectorAll('.priority-tagify').forEach(function (input) {
        // Initialize Tagify for priority fields with single tag selection mode
        var tagify = new Tagify(input, {
            whitelist: ["Düşük", "Orta", "Yüksek"],
            dropdown: {
                enabled: 1, // Enable dropdown suggestions
                maxItems: 3, // Show the three priority options
                closeOnSelect: true, // Close dropdown after selection
                position: 'all' // Dropdown below the input
            },
            enforceWhitelist: true,  // Force user to choose from the predefined priorities
            maxTags: 1,  // Limit to one tag (single selection)
            addTagOnBlur: false,  // Prevent adding tag when input loses focus
            editTags: false,  // Disable editing of the tags
            mode: 'select',  // Ensure only one can be selected
            userInput: false,  // Disable any manual input from the user
        });

        // If priority is empty, default to 'Düşük'
        if (tagify.value.length === 0) {
            tagify.addTags([{ value: 'Düşük' }]);
            setPriorityBackgroundColor('Düşük', tagify);
        } else {
            setPriorityBackgroundColor(tagify.value[0].value, tagify);
        }

        // Listen to 'change' event to update the priority and background color
        tagify.on('change', function (e) {
            const issueId = input.dataset.id;

            // Get the first selected value from Tagify's value
            const newPriority = tagify.value.length > 0 ? tagify.value[0].value : "Düşük";

            if (!newPriority) {
                console.error('No priority selected');
                return;  // Prevent sending empty priority to the database
            }

            // Send AJAX request to update the priority in the database
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
                        // Set the background color after priority is updated
                        setPriorityBackgroundColor(newPriority, tagify);
                    }
                }).catch(error => console.error('Error updating priority:', error));
        });

        // Listen to 'input' event to update background color dynamically
        tagify.on('input', function () {
            const selectedPriority = tagify.value.length > 0 ? tagify.value[0].value : "Düşük";
            setPriorityBackgroundColor(selectedPriority, tagify);
        });
    });
}

// Function to set background color based on the selected priority
function setPriorityBackgroundColor(priority, tagify) {
    let backgroundColor = '#ffffff';  // Default background color

    switch (priority) {
        case 'Düşük':
            backgroundColor = '#007bff';  // Low (blue)
            break;
        case 'Orta':
            backgroundColor = '#6f42c1';  // Medium (purple)
            break;
        case 'Yüksek':
            backgroundColor = '#dc3545';  // High (red)
            break;
    }

    // Apply the background color to the input
    tagify.DOM.input.style.backgroundColor = backgroundColor;

    // Apply the background color to the tag (if a tag is selected)
    const tag = tagify.DOM.scope.querySelector('.tagify__tag');
    if (tag) {
        tag.style.backgroundColor = backgroundColor;
    }
}
