<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Contacts</h1>
        <a href="{{ url('/custom-fields') }}" class="btn btn-primary">
            Add Custom Field
        </a>
    </div>

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
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal" id="addBtn">Add
                New</button>
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
                            <input class="form-control" type="file" name="profile_image">
                        </div>
                        <div class="mb-2" id="additionalFile">
                            <label class="form-check-label" for="gMale">Upload Additional File</label>
                            <input class="form-control" type="file" name="additional_file">
                        </div>
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
                                        <textarea name="custom_fields[{{ $field->id }}]" class="form-control"
                                            rows="3"></textarea>
                                    @endif
                                    <div class="text-danger" id="error-custom_fields-{{ $field->id }}"></div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="saveBtn">Save</button>
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
                <div class="modal-header">
                    <h5 class="modal-title">Merge Contacts</h5>
                </div>
                <div class="modal-body">
                    <p>Master Contact Id: <strong id="masterId"></strong></p>
                    <p>Master Contact Name: <strong id="masterName"></strong></p>
                    <p>Select Secondary Contact Name:</p>
                    <select id="secondarySelect" class="form-select">
                        <option value="">-- Choose Contact --</option>
                        @foreach($allContacts as $co)
                            <option value="{{ $co->id }}">[ {{$co->id}} ] [ {{$co->name}} ] [ {{$co->email}} ] [
                                {{$co->phone}} ]</option>
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
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Merge</h5>
                </div>
                <div class="modal-body">
                    <p class="mb-3">You're about to merge the following contacts:</p>

                    <div class="row mb-3">
                        <!-- Master Contact Box -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="text-primary mb-2">Master Contact</h6>
                                <p><strong>Id:</strong> <span id="masterId2"></span></p>
                                <p><strong>Name:</strong> <span id="masterName2"></span></p>
                                <p><strong>Email:</strong> <span id="masterEmail2"></span></p>
                                <p><strong>Phone:</strong> <span id="masterPhone2"></span></p>
                                <div id="masterCustomFields" class="mt-3">
                                    <h6>----------------Custom Fields----------------</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Contact Box -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-warning-subtle">
                                <h6 class="text-danger mb-2">Secondary Contact</h6>
                                <p><strong>Id:</strong> <span id="secondaryId2"></span></p>
                                <p><strong>Name:</strong> <span id="secondaryName2"></span></p>
                                <p><strong>Email:</strong> <span id="secondaryEmail2"></span></p>
                                <p><strong>Phone:</strong> <span id="secondaryPhone2"></span></p>
                                <div id="secondaryCustomFields" class="mt-3">
                                    <h6>----------------Custom Fields----------------</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="confirmMergeBtn">Merge Now</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        let deleteId = null;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
     
        $(document).on('click', '.deleteBtn', function () {
            deleteId = $(this).data('id'); 
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        });

        $('#confirmDeleteBtn').click(function () {
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

        function loadContacts() {
            $('#tableWrapper').html('<div class="text-center py-3">Loading contacts...</div>');
            $.get('/get-contacts', {
                name: $('#searchName').val(),
                email: $('#searchEmail').val(),
                gender: $('#searchGender').val(),
                phone: $('#searchMobile').val()
            }, function (html) {
                $('#tableWrapper').html(html);
            });
        }

        $(document).ready(() => {
            loadContacts();

            $('#filterBtn').click(loadContacts);

            $('#addBtn').click(() => {
                resetModalUI();
                $('#modalTitle').text('Add Contact');
            });

            $(document).on('click', '#editBtn', function () {
                let id = $(this).data('id');

                $.get(`/contacts/${id}`, function (data) {
                    resetModalUI();
                    $('#contactId').val(data.id);
                    $('#modalTitle').text('Edit Contact');
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);

                    $('input[name="gender"]').prop('checked', false);
                    if (data.gender) {
                        $(`input[name="gender"][value="${data.gender}"]`).prop('checked', true);
                    }

                    if (data.custom_fields) {
                        for (const fieldId in data.custom_fields) {
                            const value = data.custom_fields[fieldId];
                            $(`[name="custom_fields[${fieldId}]"]`).val(value);
                        }
                    }

                    $('#deleteBtn').show();

                    let editModalEl = document.getElementById('editModal');
                    let modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                    modal.show();
                    loadContacts();

                });
            });

            $('#contactForm').submit(function (e) {
                e.preventDefault();

                let id = $('#contactId').val();
                let url = id ? `/contacts/${id}` : '/contacts';
                let type = id ? 'PUT' : 'POST';
                let formData = new FormData(this);
                formData.append('_method', type);

                $('.text-danger').text('');
                $('#saveBtn').prop('disabled', true).text('Saving...');

                $.ajax({
                    url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success() {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                        modal.hide();

                        // ✅ Show SweetAlert popup
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: id ? 'Contact updated successfully.' : 'Contact created successfully.',
                            confirmButtonColor: '#3085d6'
                        });

                        loadContacts();
                        $('#saveBtn').prop('disabled', false).text('Save');
                        $('#contactForm')[0].reset();
                    },
                    error: function (xhr) {
                        $('#saveBtn').prop('disabled', false).text('Save');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            for (let key in errors) {
                                const formattedKey = key.replace(/\./g, '-');
                                const errorId = `#error-${formattedKey}`;

                                if ($(errorId).length) {
                                    $(errorId).text(errors[key][0]);
                                } else {
                                    console.warn('Missing error display element for:', key);
                                }
                            }
                        }
                        else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.contactName', function (e) {
                e.preventDefault(); 
                let id = $(this).data('id');

                $.get(`/contacts/${id}`, function (data) {
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
                    $('#saveBtn').hide(); 

                    new bootstrap.Modal($('#editModal')).show();
                });
            });
            let masterId, masterName, secondaryId, secondaryName;

            $(document).on('click', '.mergeBtn', function () {
                masterId = $(this).data('id');
                masterName = $(this).data('name');
                masterEmail = $(this).data('email');
                masterPhone = $(this).data('phone');
                $('#masterId').text(masterId);
                $('#masterName').text(masterName);
                $('#mergeSelectModal').modal('show');
            });

            $('#openConfirmMerge').click(function () {
                secondaryId = $('#secondarySelect').val();
                if (!secondaryId) {
                    $('#mergeSelectModal').modal('hide');
                    return;
                }

                const selectedText = $('#secondarySelect option:selected').text();
                const match = selectedText.match(/\[\s*(\d+)\s*]\s*\[\s*(.*?)\s*]\s*\[\s*(.*?)\s*]\s*\[\s*(.*?)\s*]/);
                if (match) {
                    secondaryName = match[2];
                    $('#secondaryId2').text(match[1]);
                    $('#secondaryName2').text(match[2]);
                    $('#secondaryEmail2').text(match[3]);
                    $('#secondaryPhone2').text(match[4]);
                }
                $('#masterId2').text(masterId);
                $('#masterName2').text(masterName);
                $('#masterEmail2').text(masterEmail);
                $('#masterPhone2').text(masterPhone);

                $.get(`/contacts/${masterId}/diff/${secondaryId}`, function (data) {
                    $('#masterCustomFields, #secondaryCustomFields').find('div').remove();

                    const buildList = (fields) => {
                        const div = $('<div></div>');
                        fields.forEach(field => {
                            div.append(`<p class="list"><strong>${field.label}:</strong><span> ${field.value}</span></p>`);
                        });
                        return div;
                    };

                    $('#masterCustomFields').append(buildList(data.master.custom_fields));
                    $('#secondaryCustomFields').append(buildList(data.secondary.custom_fields));
                    $('#mergeConfirmModal').modal('show');
                });
            });

            $('#confirmMergeBtn').click(function () {
                $.post('/contacts/merge', {
                    master_id: masterId,
                    secondary_id: secondaryId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                    .done(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            confirmButtonColor: '#3085d6'
                        });
                        $('#mergeConfirmModal').modal('hide');
                        $('#mergeSelectModal').modal('hide');
                        loadContacts();
                    })
                    .fail((xhr) => {
                        let message = 'Failed to merge contacts. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Merge Failed',
                            text: message,
                            confirmButtonColor: '#d33'
                        });
                    });
            });

            function resetModalUI() {
                $('#contactForm')[0].reset();
                $('#contactId').val('');
                $('#name, #email, #phone').prop('disabled', false);
                $('input[name="gender"]').prop('disabled', false);
                $('[name^="custom_fields"]').prop('disabled', false);
                $('#profileImage').show();
                $('#additionalFile').show();
                $('#profileImageWrapper').hide();
                $('#additionalFileWrapper').hide();
                $('#saveBtn').show();
                $('.text-danger').text('');
            }
        });

    </script>