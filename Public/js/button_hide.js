function edit(transactionId) {
	// Hide labels and show inputs/selects
	document.getElementById('defaultAmount' + transactionId).hidden = true;
	document.getElementById('amount-' + transactionId).hidden = false;
	document.getElementById('defaultCategory' + transactionId).hidden = true;
	document.getElementById('categoryId-' + transactionId).hidden = false;
	document.getElementById('defaultRemarks' + transactionId).hidden = true;
	document.getElementById('remarks-' + transactionId).hidden = false;
	document.getElementById('update-' + transactionId).hidden = false;
	document.getElementById('back-' + transactionId).hidden = false;
	document.getElementById('edit-' + transactionId).hidden = true;
	document.getElementById('delete-' + transactionId).hidden = true;
}

function back(transactionId) {
	// Revert to default view (hide inputs/selects and show labels)
	document.getElementById('defaultAmount' + transactionId).hidden = false;
	document.getElementById('amount-' + transactionId).hidden = true;
	document.getElementById('defaultCategory' + transactionId).hidden = false;
	document.getElementById('categoryId-' + transactionId).hidden = true;
	document.getElementById('defaultRemarks' + transactionId).hidden = false;
	document.getElementById('remarks-' + transactionId).hidden = true;
	document.getElementById('update-' + transactionId).hidden = true;
	document.getElementById('back-' + transactionId).hidden = true;
	document.getElementById('edit-' + transactionId).hidden = false;
	document.getElementById('delete-' + transactionId).hidden = false;

}
