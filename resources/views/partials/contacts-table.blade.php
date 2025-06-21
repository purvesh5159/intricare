<table class="table table-bordered table-hover mt-2">

  <thead class="table-light">
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Gender</th>
      <th>Actions</th>
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
          <button class="btn btn-sm btn-warning mergeBtn" data-id="{{ $contact->id }}" data-name="{{ $contact->name }}">Merge</button>
          <button class="btn btn-sm btn-info editBtn" id="editBtn" data-id="{{ $contact->id }}">Edit</button>
          <button class="btn btn-sm btn-danger deleteBtn" id="deleteBtn" data-id="{{ $contact->id }}">Delete</button>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
