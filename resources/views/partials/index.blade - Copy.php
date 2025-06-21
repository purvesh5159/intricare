<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<input type="text" id="searchName">
<input type="text" id="searchEmail">
<select id="searchGender">
  <option value="">Any</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
</select>
<button id="filterBtn">Search</button>

<form id="contactForm" enctype="multipart/form-data">
    @csrf
    <input type="text" name="name" placeholder="Name">
    <input type="text" name="email" placeholder="Email">
    <input type="text" name="phone" placeholder="Phone">

    <label>Gender:</label>
    <input type="radio" name="gender" value="Male"> Male
    <input type="radio" name="gender" value="Female"> Female

    <input type="file" name="profile_image">
    <input type="file" name="additional_file">

    @foreach ($customFields as $field)
        <label>{{ $field->name }}</label>
        <input type="{{ $field->type }}" name="custom_fields[{{ $field->id }}]">
    @endforeach

    <button type="submit">Save</button>
</form>

<div id="contactsTable">
    <!-- AJAX results go here -->
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#contactForm').on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: '/contacts',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res){
            alert(res.message);
            loadContacts();
        }
    });
});

function loadContacts() {
    $.get('/get-contacts', {
        name: $('#filterName').val(),
        email: $('#filterEmail').val(),
        gender: $('#filterGender').val()
    }, function(data) {
        $('#contactsTable').html(data);
    });
}

$('#filterBtn').on('click', function() {
    let name = $('#searchName').val();
    let email = $('#searchEmail').val();
    let gender = $('#searchGender').val();

    $.get('/contacts', { name, email, gender }, function(data) {
        let html = '';
        data.forEach(c => {
            html += `<div>${c.name} - ${c.email}</div>`;
        });
        $('#contactsTable').html(html);
    });
});

</script>
