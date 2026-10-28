<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url();?>/public/assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?=base_url();?>/public/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6">
                    <h4 class="p-3  dash-head1 mb-0"><?=$page_name;?></h4>
            </div>
        </div>
    </div>
</section>
<section class="p-4">

<div class="row">
    <div class="col-12">
        <?php if (session()->has('success_message')) : ?>
            <div class="alert alert-success">
                <?= session('success_message') ?>
                <?php session()->remove('success_message') ?>
            </div>
        <?php endif ?>
        <?php if (session()->has('error_message')) : ?>
            <div class="alert alert-danger">
                <?= session('error_message') ?>
                <?php session()->remove('error_message') ?>
            </div>
        <?php endif ?>
        <?php if (session()->has('errors')) : ?>
            <ul class="alert alert-danger">
            <?php foreach (session('errors') as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="<?=base_url('admin/add_notification')?>" class="btn btn-primary btn-md float-right"><i class="fa fa-plus"></i> Add Notification</a>
            </div>
            <div class="card-body">
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <div class="table-responsive">
                    <table id="customers">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
<!-- end row -->
</section>
<?=$this->endSection();?>
<?=$this->section('pageScript');?>

<script src="<?=base_url();?>/public/assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>/public/assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<script>

$(document).ready(function () {
    var table = $('#customers').DataTable({
        ordering:false,
        serverSide: true,
        language: {
            paginate: {
              next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
              previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
            }
        },
        lengthMenu: [25,50,100],
        ajax: {
            url: "<?=base_url();?>/admin/notifications",
            type: 'POST',
            data: function (res) {
                res.<?= csrf_token() ?> = $('#csrftoken').val();
            }
        },
        columns: [
           { data: 'id'},
         { data: 'date',width:70},
           { data: 'title'},
           { data: 'desc'},
           { data: 'image'},
           { data: 'action'}
       ],
       fnDrawCallback: function(result) { 
            $('#csrftoken').val(result.json.csrf_token);
        }
    });
});

function delete_notification(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this notification?") == true) {
             $.ajax({
                url:"<?=base_url();?>/admin/delete_notification",
                method:"POST",
                data: {'id':id,'<?= csrf_token()?>':$('#csrftoken').val()},
                success:function(response)
                {

                    var obje = jQuery.parseJSON(response);
                        if(obje.success == 'success'){     
                          alert(obje['message']);
                          window.location.href = currenturl;
                        }else if(obje['failed'] == 'error'){
                           alert(obje['message']);
                        }
                
                },
                error: function (response) {
                    alert('Something went wrong. try again'); 
                }
             });
        }
    }
</script>
<?=$this->endSection();?>