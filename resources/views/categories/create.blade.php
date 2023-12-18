<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Create Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>

<body>

    <!-- Modal -->
    <div class="modal fade ajax-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="ajaxForm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="form-group mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <span id="nameError" class="text-danger error-msg"></span>
                        </div>
                        <div class="form-group mb-1">
                            <label for="">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option disabled selected>Choose Option</option>
                                <option value="electronics">Electronics</option>
                                <option value="homeappliance">Home Appliance</option>
                            </select>
                            <span id="typeError" class="text-danger error-msg"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBtn"></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6 offset-3" style="margin-top: 100px;">
            <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModal" id="add_category" style="margin-bottom: 30px;">Add Category</a>

            <table id="category-table" class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $("#category-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.index') }}",
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'type' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $("#modal-title").html('Create Category');
            $("#saveBtn").html('Save Category');

            var form = $("#ajaxForm")[0];

            $("#saveBtn").click(function() {
                $("#saveBtn").html('Saving...');
                $("#saveBtn").attr('disabled', true);
                $(".error-msg").html('');
                var formData = new FormData(form);

                $.ajax({
                    url: '{{ route("categories.store") }}',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,

                    success: function(response) {
                        //console.log(response.success);
                        table.draw();
                        $("#saveBtn").attr('disabled', false);
                        $("#saveBtn").html('Save Category');
                        $("#category_id").val('');
                        $(".ajax-modal").modal('hide');
                        $("#ajaxForm")[0].reset();

                        if (response) 
                        {
                            swal("Success!", response.success, "success");
                        }
                    },
                    error: function(error) {
                        $("#saveBtn").attr('disabled', false);
                        $("#saveBtn").html('Save Category');
                        if(error)
                        {
                            //console.log(error.responseJSON.errors.name);
                            $("#nameError").html(error.responseJSON.errors.name);
                            $("#typeError").html(error.responseJSON.errors.type);
                        }
                    }
                });
            });

            // Edit Button

            $("body").on('click', '.editBtn', function() {
                var id = $(this).data('id');
                //console.log(id);

                $.ajax({
                    url: '{{ url("categories", '') }}'+ '/' + id+ '/edit',
                    method: 'GET',
                    success: function(response) {
                        //console.log(response);
                        $(".ajax-modal").modal('show');
                        $("#modal-title").html('Edit Category');
                        $("#saveBtn").html('Update Category');

                        $("#category_id").val(response.id);
                        $("#name").val(response.name);
                        var capitalType = captializeFirstLetter(response.type);
                        $('#type').empty().append('<option selected value="'+response.type+'">'+ capitalType +'</option>');
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("body").on('click', '.deleteBtn', function() {
                var id = $(this).data('id');
                //console.log(id);

                if(confirm('Are you want to sure to delete?'))
                {
                    $.ajax({
                        url: '{{ url("categories/destroy", '') }}'+ '/' + id,
                        method: 'DELETE',
                        success: function(response) {
                            table.draw();
                            swal("Success!", response.success, "success");
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });

            $("#add_category").click(function(){
                $("#modal-title").html('Create Category');
                $("#name").val('');
                $("#type").val('');
                $("#saveBtn").html('Save Category');
                $('.error-msg').html('');
            });

            function captializeFirstLetter(string)
            {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

        });

    </script>

</body>

</html>