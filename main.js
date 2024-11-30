// Function to open the date picker modal
function openDatePicker(itemid) {
    const modal = document.getElementById('datePickerModal');
    modal.style.display = 'flex'; // Show the modal
    const dateItemId = document.getElementById('dateItemId');
    // Store the action URL in a data attribute for later submission
    dateItemId.value = itemid;
}

