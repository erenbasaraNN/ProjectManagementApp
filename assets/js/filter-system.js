// Initialize Tagify instances and filters
let statusTagify, priorityTagify, assigneeTagify;

export function initializeFilterSystem() {
    initializeFilterDropdowns();

    const applyFiltersButton = document.getElementById('apply-filters');
    const clearFiltersButton = document.getElementById('clear-filters');

    if (applyFiltersButton) {
        applyFiltersButton.addEventListener('click', applyFilters);
    }

    if (clearFiltersButton) {
        clearFiltersButton.addEventListener('click', clearFilters);
    }
}

function initializeFilterDropdowns() {
    // Initialize Tagify for each dropdown
    statusTagify = initializeTagifyDropdown('status-filter', ['Başlanacak', 'Aktif Durumda', 'Tamamlandı', 'İptal'], setStatusBackgroundColor);
    priorityTagify = initializeTagifyDropdown('priority-filter', ['Yüksek', 'Orta', 'Düşük'], setPriorityBackgroundColor);
    assigneeTagify = initializeAssigneeDropdown('assignee-filter');
}

function initializeTagifyDropdown(elementId, whitelist, colorCallback) {
    const input = document.getElementById(elementId);
    if (input && !input.classList.contains('tagify-initialized')) {
        const tagify = new Tagify(input, {
            whitelist: whitelist,
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
            placeholder: 'Select an option'  // Add placeholder here
        });

        input.classList.add('tagify-initialized');

        // No default value or tag added
        tagify.on('change', function () {
            const newValue = tagify.value.length > 0 ? tagify.value[0].value : '';
            colorCallback(newValue, tagify);
        });

        tagify.on('input', function () {
            const selectedValue = tagify.value.length > 0 ? tagify.value[0].value : '';
            colorCallback(selectedValue, tagify);
        });

        return tagify; // Return the Tagify instance
    }
}

function initializeAssigneeDropdown(elementId) {
    const input = document.getElementById(elementId);
    if (input && !input.classList.contains('tagify-initialized')) {
        const tagify = new Tagify(input, {
            whitelist: getUniqueAssignees(),
            dropdown: {
                enabled: 1,
                closeOnSelect: true,
                position: 'all'
            },
            enforceWhitelist: true,
            maxTags: 1,
            addTagOnBlur: false,
            editTags: false,
            mode: 'select',
            userInput: false,
            placeholder: 'Select an assignee'  // Add placeholder here
        });

        input.classList.add('tagify-initialized');

        return tagify; // Return the Tagify instance
    }
}


function getUniqueAssignees() {
    const assignees = new Set();
    document.querySelectorAll('.tagify-input[name="assignees"]').forEach(input => {
        input.value.split(',').forEach(assignee => assignees.add(assignee.trim()));
    });
    return Array.from(assignees).map(name => ({ value: name }));
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

function setPriorityBackgroundColor(priority, tagify) {
    let backgroundColor = '#ffffff';  // Default background color

    switch (priority) {
        case 'Yüksek':
            backgroundColor = '#f12436';  // High (red)
            break;
        case 'Orta':
            backgroundColor = '#e3d324';  // Medium (yellow)
            break;
        case 'Düşük':
            backgroundColor = '#46c965';  // Low (green)
            break;
    }

    tagify.DOM.input.style.backgroundColor = backgroundColor;
    const tag = tagify.DOM.scope.querySelector('.tagify__tag');
    if (tag) {
        tag.style.backgroundColor = backgroundColor;
    }
}

function applyFilters() {
    const statusFilter = statusTagify?.value.length > 0 ? statusTagify.value[0].value : '';
    const priorityFilter = priorityTagify?.value.length > 0 ? priorityTagify.value[0].value : '';
    const assigneeFilter = assigneeTagify?.value.length > 0 ? assigneeTagify.value[0].value : '';

    console.log('Filters:');
    console.log('Status Filter:', statusFilter);
    console.log('Priority Filter:', priorityFilter);
    console.log('Assignee Filter:', assigneeFilter);

    document.querySelectorAll('.issue-table tbody tr:not(.add-issue-row)').forEach(row => {
        const statusElement = row.querySelector('.status-tagify-container input');
        const priorityElement = row.querySelector('.priority-tagify-container input');
        const assigneeElement = row.querySelector('.tagify-input[name="assignees"]');

        // Correctly parse values from the input elements
        const status = statusElement ? JSON.parse(statusElement.value)[0]?.value : '';
        const priority = priorityElement ? JSON.parse(priorityElement.value)[0]?.value : '';
        const assignees = assigneeElement ? assigneeElement.value.split(',').map(a => a.trim()) : [];

        console.log('Issue Row:', row);
        console.log('Status:', status);
        console.log('Priority:', priority);
        console.log('Assignees:', assignees);

        // Check if filters match
        const statusMatch = !statusFilter || (status && status.toLowerCase() === statusFilter.toLowerCase());
        const priorityMatch = !priorityFilter || (priority && priority.toLowerCase() === priorityFilter.toLowerCase());
        const assigneeMatch = !assigneeFilter || assignees.some(assignee => assignee.toLowerCase().includes(assigneeFilter.toLowerCase()));

        console.log('Matches:');
        console.log('Status Match:', statusMatch);
        console.log('Priority Match:', priorityMatch);
        console.log('Assignee Match:', assigneeMatch);

        if (statusMatch && priorityMatch && assigneeMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    if (statusTagify) statusTagify.removeAllTags();
    if (priorityTagify) priorityTagify.removeAllTags();
    if (assigneeTagify) assigneeTagify.removeAllTags();

    document.querySelectorAll('.issue-table tbody tr').forEach(row => {
        row.style.display = '';
    });
}
