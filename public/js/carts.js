$(document).on('click', '.openSlotNumber', function(){
  var slotNumber = $(this).data('slot-number');
  var slotId = $(this).data('slot-id');
  var slotAssetTag = $(this).data('slot-assettag');
  var slotAssignedAssetId = $(this).data('slot-assetid');
  var slotAssignedPerson = $(this).data('slot-assignedpersonid');
  var isFinished = $(this).data('is-finished');
  var isNeedsRepair = $(this).data('needs-repair');
  var notes = $(this).data('notes');

  // Change HTML elements on page
  $('#form_inputPersonName').attr('selected', slotAssignedPerson);
  $('#slotModalTitle').text(slotNumber);
  $('#form_inputAssetTag').attr('value', slotAssetTag);
  $('#form_inputAssetTag').focus();
  $('#form_slot_id').val(slotId);
  $('#form_clear').attr('data-slotid', slotId);
  $('#form_inputAssetNotes').val(notes);

  if (isFinished == 1) {
    $('#form_slotFinished').prop("checked", true);
  } else if (isFinished == 0) {
    $('#form_slotFinished').prop("checked", false);
  }

  if (isNeedsRepair == 1) {
    $('#form_needsRepair').prop("checked", true);
  } else if (isNeedsRepair == 0){
    $('#form_needsRepair').prop("checked", false);
  }

  // When the Generate button is pressed, change the value for the AssetTag
  $('#form_inputNumberGenerator').on( "click", function(event) {
    $('#form_inputAssetTag').attr('value', generateRandomNumber(7));
  });

  // Make people SELECT searchable
  if (slotAssignedPerson == -1) {
    $("#form_inputPersonName").select2({
      dropdownParent: $('#slotModal'),
      width: '100%',
    });
  } else {
    $("#form_inputPersonName").select2({
      dropdownParent: $('#slotModal'),
      width: '100%'
    }).val(slotAssignedPerson).trigger("change");
  }
});

// Generate random number
// https://stackoverflow.com/posts/39774334/revisions
function generateRandomNumber(n) {
  var add = 1, max = 12 - add;

  if ( n > max ) {
    return (generateRandomNumber(max) + generateRandomNumber(n - max));
  }

  max = Math.pow(10, n+add);
  var min = max/10 // Math.pow(10,n) basically
  var number = Math.floor( Math.random() * (max - min + 1) ) + min;
  
  // document.getElementById('form_inputAssetTag_' + slotId).value = ("" + number).substring(add);
  return ("" + number).substring(add);
}

// This reloads the container for the cart views
var timeout = setInterval(reloadCarts, 1500);
function reloadCarts() {
    $('#cbcontainer').load(window.location.href + " #cbcontainer");
}
