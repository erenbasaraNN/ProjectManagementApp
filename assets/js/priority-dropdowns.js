export function initializePriorityDropdowns(input) {
    const inputs = input ? [input] : Array.from(document.querySelectorAll('.priority-tagify, .priority-dropdown'));

    if (inputs.length === 0) {
        console.warn('No priority dropdown elements found on the page');
        return;
    }

    inputs.forEach(function (input) {
        if (!input || !(input instanceof Element)) {
            console.warn('Invalid input element', input);
            return;
        }

        if (input.classList.contains('tagify-initialized')) {
            return;
        }

        var tagify = new Tagify(input, {
            whitelist: ["Düşük", "Orta", "Yüksek"],
            dropdown: {
                enabled: 1,
                maxItems: 3,
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
            tagify.addTags([{ value: 'Düşük' }]);
            setPriorityBackgroundColor('Düşük', tagify);
        } else {
            setPriorityBackgroundColor(tagify.value[0].value, tagify);
        }

        tagify.on('change', function () {
            const issueId = input.dataset.id;
            const newPriority = tagify.value.length > 0 ? tagify.value[0].value : "Düşük";

            if (!newPriority) {
                console.error('No priority selected');
                return;
            }

            fetch(`/issue/${issueId}/edit-priority`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ priority: newPriority })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Priority updated successfully');
                        setPriorityBackgroundColor(newPriority, tagify);
                    }
                })
                .catch(error => console.error('Error updating priority:', error));
        });

        tagify.on('input', function () {
            const selectedPriority = tagify.value.length > 0 ? tagify.value[0].value : "Düşük";
            setPriorityBackgroundColor(selectedPriority, tagify);
        });
    });
}

function setPriorityBackgroundColor(priority, tagify) {
    let backgroundColor = '#ffffff';

    switch (priority) {
        case 'Düşük':
            backgroundColor = '#007bff';
            break;
        case 'Orta':
            backgroundColor = '#6f42c1';
            break;
        case 'Yüksek':
            backgroundColor = '#dc3545';
            break;
    }

    tagify.DOM.input.style.backgroundColor = backgroundColor;
    const tag = tagify.DOM.scope.querySelector('.tagify__tag');
    if (tag) {
        tag.style.backgroundColor = backgroundColor;
    }
}