$(function () {
	$(document).on('click', 'input[type=text]', function () {
		this.select();
	});
});

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	} else {
            // If the number field already has . then don't allow to enter . again.
            if (evt.target.value.search(/\./) > -1 && charCode == 46) {
            	return false;
            }
            return true;
        }
    }