<table class="table table-bordered table-hover mt-2">

  <thead class="table-light">
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Gender</th>
      <th colspan="2">Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($contacts as $contact)
    <tr>
    <tr>
      <td>
      <a href="#" class="contactName" data-id="{{ $contact->id }}">{{ $contact->id }}</a>
      </td>
      <td>
      <span>{{ $contact->name }}</span>
      @if($contact->is_merged)
      <br>
      <span class="badge bg-secondary">Merged → #{{ $contact->merged_into }}</span>
    @endif
      </td>
      <td>{{ $contact->email }}</td>
      <td>{{ $contact->phone }}</td>
      <td>{{ $contact->gender }}</td>
      <td>
      <div style="margin-bottom: 5px;">
        <button class="btn btn-sm btn-info editBtn" id="editBtn" data-id="{{ $contact->id }}">Edit</button>
      </div>
      <div>
        <button class="btn btn-sm btn-secondary contactName" data-id="{{ $contact->id }}">Show</button>
      </div>
      </td>
      <td>
      <div style="margin-bottom: 5px;">
        <button class="btn btn-sm btn-warning mergeBtn" data-id="{{ $contact->id }}" data-name="{{ $contact->name }}"
        data-email="{{ $contact->email }}" data-phone="{{ $contact->phone }}">
        Merge
        </button>
      </div>
      <div>
        <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $contact->id }}">
        Delete
        </button>
      </div>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>