<?php
if (isset($_POST['submit'])) {
    $post_image = $_FILES['image'];
    if (strlen($post_image['tmp_name']) > 1) {
        $image_alt = $_POST['alt'];
        $image = $functions->upload($post_image);
        $image = site_url . upload_folder . $image;
        $insert_post_query = "INSERT INTO `post`( `post_title`, `guid`, `post_type`, `mime_type`) 
    VALUES ('$image_alt','$image','attachment','media')";
        base::RunQuery($insert_post_query);
    }
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $file_path = base::FetchAssoc("SELECT `guid` FROM `post` WHERE `post_id` = $id")['guid'];
    unlink($file_path);
    base::RunQuery("DELETE FROM `post` WHERE `post_id` = " . $id);
}
?>
<link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
<div class="row">
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h4 class="card-title mb-0">افزودن</h4>
            </div>
            <div class="card-body p-3">
                <form class="theme-form row" action="" method="post" enctype="multipart/form-data">
                    <div class="form-group col-12">
                        <label class="form-label">اسم فایل</label>
                        <input required name="alt" class="form-control" type="text">
                    </div>
                    <div class="form-group col-12">
                        <h2 class="form-label">فایل</h2>
                        <input onchange="loadFile(this, event)" name="image" type="file" class="form-control">
                        <img width="100%" class="mt-3" id="image" src="">
                    </div>
                    <div class="card-footer col-12">
                        <button type="submit" name="submit" class="btn btn-primary btn-pill"> ذخیره کنید </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card">
            <form class="theme-form" action="" method="post">
                <div class="card-body">
                    <div class="card-datatable text-nowrap overflow-auto">
                        <table class="datatables-posts table table-bordered">
                            <thead>
                                <tr>
                                    <th class="all">آیدی</th>
                                    <th class="all">نام</th>
                                    <th data-sortable="false" class="all">عملیات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/vendor/libs/datatables/jquery.dataTables.js"></script>
<script src="assets/vendor/libs/datatables/i18n/fa.js"></script>
<script src="assets/js/tables-datatables-advanced.js"></script>
<script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="assets/vendor/libs/datatables-responsive/datatables.responsive.js"></script>
<script src="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js"></script>
<script src="assets/vendor/libs/select2/select2.js"></script>
<script>
    function copy(text) {
        navigator.clipboard.writeText(text);
    }
    function group_check(source) {
        checkboxes = document.getElementsByName('ids[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function imageReplace(name, event) {
        var output = document.getElementById(name);
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    };
    var loadFile = function(elem, event) {
        var index = elem.name;
        imageReplace(index, event);
    };
</script>
<script>
    $(window).resize(function() {
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }
    });
    $(window).ready(function() {
        const select2 = $('.select2');
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'انتخاب',
                    dropdownParent: $this.parent()
                });
            });
        }
        if ($(window).width() < 560) {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '12px',
                'padding': '15px'
            });
        } else {
            $('.datatables-posts tbody td,.modal-body tbody td').css({
                'font-size': '14px',
                'padding': '0.625rem 1.5rem'
            });
        }
        // Variable declaration for table
        var dt_user_table = $('.datatables-posts');

        // Users datatable
        if (dt_user_table.length) {
            var dt_user = dt_user_table.DataTable({
                processing: true,
                serverSide: true,
                select: true,
                ajax: {
                    url: 'API/v1/getuploads.php'
                },
                columns: [
                    // columns according to JSON
                    {
                        data: 'post_id'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'op'
                    }
                ],
                columnDefs: [{
                        // For Responsive
                        // className: 'control',
                        targets: 0,
                        render: function(data, type, full, meta) {
                            var $id = full['post_id'];
                            return "<span class='text-truncate d-flex align-items-center'>" + $id + '</span>';
                        }
                    },
                    {
                        // User full name 
                        targets: 1,
                        responsivePriority: 4,
                        render: function(data, type, full, meta) {
                            var $name = full['title']
                            var $row_output = $name;
                            return $row_output;
                        }
                    },
                    {
                        // op
                        targets: 2,
                        responsivePriority: 2,
                        title: 'عملیات',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var $op = full['op'];
                            return $op;
                        }
                    }
                ],
                order: [
                    [0, 'DESC']
                ],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'جزئیات کاربر ' + data['name'];
                            }
                        }),
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ?
                                    '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><""t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
            });
        }
    })
</script>