export function initializeStatusDropdowns(input) {
    const inputs = input ? [input] : document.querySelectorAll('.status-tagify');
    inputs.forEach(function (input) {
        if (!input.classList.contains('tagify-initialized')) {
            var tagify = new Tagify(input, {
                whitelist: ["Başlanacak", "Aktif Durumda", "Tamamlandı", "İptal"],
                dropdown: {
                    enabled: 1,
                    maxItems: 4,
                    closeOnSelect: true,
                    position: 'all'
                },
                enforceWhitelist: true,
                maxTags: 1,
                addTagOnBlur: false,
                editTags: false,
                mode: 'select',
                userInput: false,
            });

            input.classList.add('tagify-initialized');

            if (tagify.value.length === 0) {
                tagify.addTags([{ value: 'Başlanacak' }]);
                setStatusBackgroundColor('Başlanacak', tagify);
            } else {
                setStatusBackgroundColor(tagify.value[0].value, tagify);
            }

            tagify.on('change', function () {
                const issueId = input.dataset.id;
                const newStatus = tagify.value.length > 0 ? tagify.value[0].value : "Başlanacak";

                if (!newStatus) {
                    console.error('No status selected');
                    return;
                }

                fetch(`/issue/${issueId}/edit-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            console.log('Status updated successfully');
                            setStatusBackgroundColor(newStatus, tagify);
                        }
                    })
                    .catch(error => console.error('Error updating status:', error));
            });

            tagify.on('input', function () {
                const selectedStatus = tagify.value.length > 0 ? tagify.value[0].value : "Başlanacak";
                setStatusBackgroundColor(selectedStatus, tagify);
            });
        }
    });
}

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
    const tag = tagify.DOM.scope.querySelector('.tagify__tag');
    if (tag) {
        tag.style.backgroundColor = backgroundColor;
    }
}
