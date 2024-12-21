// Function to open the date picker modal
function openDatePicker(itemid,notes) {
    const modal = document.getElementById('datePickerModal');
    modal.style.display = 'flex'; // Show the modal
    const dateItemId = document.getElementById('dateItemId');
    dateItemId.value = itemid;
    
    if (notes) {
        const noteDiv = document.getElementById('multiple-notes');
        noteDiv.style.display = 'block';
        const noteText = document.getElementById('notes-text');
        noteText.innerText = notes;
    }
}

// Function to open the item modal
function openItemWindow(itemid, recurring) {
    const modal = document.getElementById('itemModal');
    modal.style.display = 'flex'; // Show the modal

    if (itemid) {
        const desc = document.getElementById('item'+itemid).children[0].innerHTML;
        const linkElements = document.getElementById('item'+itemid).getElementsByTagName('a');
        link = '';
        if (linkElements.length > 0) link = linkElements[0].href;

        document.getElementById('editItemId').value = itemid;
        document.getElementById('desc').value = desc;
        document.getElementById('link').value = link;
        document.getElementById('recurring').checked = recurring;
    }
}

function closeItemModal() {
    const modal = document.getElementById('itemModal');
    modal.style.display = 'none'; // Hide the modal
}

function closeDateModal() {
    const modal = document.getElementById('datePickerModal');
    modal.style.display = 'none'; // Hide the modal
}

// add close event to item modal close button
const itemCloseButton = document.getElementById('closeItemForm');
itemCloseButton.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the form's default submit behavior
    closeItemModal();
});

// add close event to date modal close button
const dateCloseButton = document.getElementById('closeDateForm');
dateCloseButton.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the form's default submit behavior
    closeDateModal();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') { // Check if the Escape key was pressed
        closeDateModal();
        closeItemModal();
    }
});
