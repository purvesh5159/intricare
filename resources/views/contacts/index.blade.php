<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-4">
  <h1>Contacts</h1>

  <!-- Filter Section -->
  <div class="row g-2 mt-2">
    <div class="col"><input class="form-control" id="searchName" placeholder="Search Name"></div>
    <div class="col"><input class="form-control" id="searchEmail" placeholder="Search Email"></div>
    <div class="col"><input class="form-control" id="searchMobile" placeholder="Search Mobile No"></div>
    <div class="col">
      <select class="form-select" id="searchGender">
        <option value="">Any</option>
        <option>Male</option>
        <option>Female</option>
      </select>
    </div>
    <div class="col">
      <button class="btn btn-primary" id="filterBtn">Search</button>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal" id="addBtn">Add New</button>
    </div>
  </div>


  <div id="tableWrapper"></div>


<!-- ✏️ Add/Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="contactForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" id="contactId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><input class="form-control" name="name" id="name" placeholder="Name">
          <div class="text-danger" id="error-name"></div>
        </div>
          <div class="mb-2"><input class="form-control" name="email" id="email" placeholder="Email">
          <div class="text-danger" id="error-email"></div>
        </div>
          <div class="mb-2"><input class="form-control" name="phone" id="phone" placeholder="Phone">
          <div class="text-danger" id="error-phone"></div>
        </div>
          <div class="mb-2">
            <label>Gender:</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" value="Male" id="gMale">
              <label class="form-check-label" for="gMale">Male</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" value="Female" id="gFemale">
              <label class="form-check-label" for="gFemale">Female</label>
            </div>
              <div class="text-danger" id="error-gender"></div>
          </div>
          <div class="mb-2" id="profileImageWrapper" style="display:none;">
            <label>Profile Image:</label><br>
            <img id="viewProfileImage" src="" alt="Profile" class="img-thumbnail" width="120">
        </div>
          <div class="mb-2" id="additionalFileWrapper" style="display:none;">
            <label>Additional File:</label><br>
            <a id="viewadditionalFile" src="" alt="Additional File" class="file">link</a>
        </div>

          <div class="mb-2" id="profileImage">
            <label class="form-check-label" for="gMale">Upload Profile Image</label>
            <input class="form-control" type="file" name="profile_image"></div>
          <div class="mb-2" id="additionalFile">
            <label class="form-check-label" for="gMale">Upload Additional File</label>
            <input class="form-control" type="file" name="additional_file"></div>
          <div class="mb-2" id="additional">
           @foreach ($customFields as $field)
            <div class="mb-3">
                <label>{{ $field->name }}</label>

                @if ($field->type == 'text')
                    <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control">
                @elseif ($field->type == 'number')
                    <input type="number" name="custom_fields[{{ $field->id }}]" class="form-control">
                @elseif ($field->type == 'date')
                    <input type="date" name="custom_fields[{{ $field->id }}]" class="form-control">
                @elseif ($field->type == 'textarea') 
                    <textarea name="custom_fields[{{ $field->id }}]" class="form-control" rows="3"></textarea>
                @endif
            </div>
        @endforeach

          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" id="saveBtn" >Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- 🔴 Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this contact?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mergeSelectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Merge Contacts</h5></div>
      <div class="modal-body">
        <p>Master: <strong id="masterName"></strong></p>
        <p>Select Secondary Contact:</p>
        <select id="secondarySelect" class="form-select">
          <option value="">-- Choose Contact --</option>
          @foreach($allContacts as $co)
            <option value="{{ $co->id }}">{{ $co->name }} ({{ $co->email }})</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-warning" id="openConfirmMerge">Next →</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mergeConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Merge</h5></div>
      <div class="modal-body">
        <p>You're merging <strong id="masterName2"></strong> <em>with</em> <strong id="secondaryName2"></strong>.</p>
        <div id="mergeDiffs"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="confirmMergeBtn">Merge Now</button>
      </div>
    </div>
  </div>
</div>


<script>
let deleteId = null; // store which contact to delete

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});


// On click "Delete" button beside each contact
$(document).on('click', '.deleteBtn', function() {
  deleteId = $(this).data('id'); // store contact ID
  new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
});

// On confirm "Yes" in delete modal
$('#confirmDeleteBtn').click(function() {
  if (!deleteId) return;

  $.ajax({
    url: `/contacts/${deleteId}`,
    type: 'POST',
    data: {
      _method: 'DELETE',
      _token: $('meta[name="csrf-token"]').attr('content')
    },
    success() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
      modal.hide();
      loadContacts();
    },
    error(xhr) {
      alert('Failed to delete contact');
      console.error(xhr.responseText);
    }
  });
});

function loadContacts(){
  $('#tableWrapper').html('<div class="text-center py-3">Loading contacts...</div>');
  $.get('/get-contacts', {
    name: $('#searchName').val(),
    email: $('#searchEmail').val(),
    gender: $('#searchGender').val(),
    phone: $('#searchMobile').val()
  }, function(html) {
    $('#tableWrapper').html(html); // Replaces entire table
  });
}


$(document).ready(()=>{
  loadContacts();

  $('#filterBtn').click(loadContacts);

  $('#addBtn').click(()=>{
    $('#contactForm')[0].reset(); $('#contactId').val('');
  });

$(document).on('click', '#editBtn', function() {
  let id = $(this).data('id');
  
  $.get(`/contacts/${id}`, function(data) {
    // Fill form fields with existing data
    $('#contactId').val(data.id);
    $('#modalTitle').text('Edit Contact');
    $('#name').val(data.name);
    $('#email').val(data.email);
    $('#phone').val(data.phone);

    // Set gender radio button based on data.gender (Male/Female)
    $('input[name="gender"]').prop('checked', false); // reset first
    if(data.gender) {
      $(`input[name="gender"][value="${data.gender}"]`).prop('checked', true);
    }

       if (data.custom_fields) {
        for (const fieldId in data.custom_fields) {
            const value = data.custom_fields[fieldId];
            $(`[name="custom_fields[${fieldId}]"]`).val(value);
        }
    }
    
    // Show delete button inside modal (optional)
    $('#deleteBtn').show();

    // Show modal
    let editModalEl = document.getElementById('editModal');
    let modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
    modal.show();
    loadContacts();
    
  });
});

  $('#contactForm').submit(function(e){
    e.preventDefault();
    let id = $('#contactId').val();
    let url = id ? `/contacts/${id}` : '/contacts';
    let type = id ? 'PUT' : 'POST';
    let formData = new FormData(this);
    formData.append('_method', type);
      $('.text-danger').text('');
    $.ajax({
      url, type: 'POST',
      data: formData,
      contentType:false,
      processData:false,
      success(){
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
        loadContacts();
      }
      ,
        error: function (xhr) {
            $('#saveBtn').prop('disabled', false).text('Save');

            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Display each error
                for (let key in errors) {
                    const errorId = `#error-${key.replace('.', '-')}`;
                    $(errorId).text(errors[key][0]);
                }
            } else {
                alert('An error occurred. Please try again.');
            }
        }
    });
  });


  $(document).on('click', '.contactName', function(e) {
  e.preventDefault(); // prevent page jump on href="#"
  let id = $(this).data('id');

  $.get(`/contacts/${id}`, function(data) {
    // Assuming you want to show the data in your existing modal:
    $('#contactId').val(data.id);
    $('#modalTitle').text('View Contact');
    $('#name').val(data.name).prop('disabled', true);
    $('#email').val(data.email).prop('disabled', true);
    $('#phone').val(data.phone).prop('disabled', true);
    $(`#g${data.gender}`).prop('checked', true).prop('disabled', true);
    
       if (data.custom_fields) {
        for (const fieldId in data.custom_fields) {
            const value = data.custom_fields[fieldId];
            $(`[name="custom_fields[${fieldId}]"]`).val(value).prop('disabled', true);
        }
    }

    // Disable the radio buttons, too

    if (data.profile_image_url) {
        $('#viewProfileImage').attr('src', data.profile_image_url);
        $('#profileImageWrapper').show();
    } else {
        $('#profileImageWrapper').hide();
    }

      if (data.additional_file_url) {
        $('#viewadditionalFile').attr('href', data.additional_file_url);
        $('#additionalFileWrapper').show();
    } else {
        $('#additionalFileWrapper').hide();
    }

    $('#profileImage').hide();
    $('#additionalFile').hide();
    $('#saveBtn').hide(); // hide save button
    

    new bootstrap.Modal($('#editModal')).show();
  });
});
let masterId, masterName, secondaryId, secondaryName;

$(document).on('click', '.mergeBtn', function() {
  masterId = $(this).data('id');
  masterName = $(this).data('name');
  $('#masterName').text(masterName);
  $('#mergeSelectModal').modal('show');
});

$('#openConfirmMerge').click(function() {
  secondaryId = $('#secondarySelect').val();
  if (!secondaryId) { alert('Select a contact!'); return; }
  secondaryName = $('#secondarySelect option:selected').text();
  $('#masterName2').text(masterName);
  $('#secondaryName2').text(secondaryName);
  $('#mergeSelectModal').modal('hide');

  // Load data diff
  $.get(`/contacts/${masterId}/diff/${secondaryId}`, function(diffHtml) {
    $('#mergeDiffs').html(diffHtml);
    $('#mergeConfirmModal').modal('show');
  });
});

$('#confirmMergeBtn').click(function() {
  $.post('/contacts/merge', {
    master_id: masterId,
    secondary_id: secondaryId,
    _token: $('meta[name="csrf-token"]').attr('content')
  }).done(res => {
    alert(res.message);
    $('#mergeConfirmModal').modal('hide');
    loadContacts();
  }).fail(() => alert('Failed!'));
});

});
</script>
