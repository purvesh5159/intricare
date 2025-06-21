<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Manage Custom Fields</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form to add a custom field -->
            <form action="/custom-fields" method="POST" class="row g-3 align-items-end mb-4">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Field Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter field name" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Field Type</label>
                    <select name="type" class="form-select">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="textarea">Textarea</option> 
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Add Field</button>
                </div>
            </form>

            <!-- Fields table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fields as $field)
                            <tr>
                                <td>{{ $field->name }}</td>
                                <td class="text-capitalize">{{ $field->type }}</td>
                                <td>
                                    <form action="/custom-fields/{{ $field->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this field?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No custom fields found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
