console.log('Start of script');

// Initialize the modules
import initializeTagify from './tagify';
import initializeStatusDropdowns from './status-dropdowns';
import initializePriorityDropdowns from './priority-dropdowns';
import initializeDatePicker from './datepicker';
import handleIssueNameEdit from './edit-issue-name';

// Run the initialization functions
initializeTagify();
console.log('tagify.js loaded');

initializeStatusDropdowns();
console.log('status-dropdowns.js loaded');

initializePriorityDropdowns();
console.log('priority-dropdowns.js loaded');

initializeDatePicker();
console.log('datepicker.js loaded');

handleIssueNameEdit();
console.log('edit-issue-name.js loaded');

console.log('End of script');
