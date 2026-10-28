<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- Topbar header - style you can find in pages.scss -->
        <?php echo view('layouts/topbar_header') ?>
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <?php echo view('layouts/left_sidebar') ?>
        
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
               <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor">Sports</h4>
                    </div>
                   
                </div>
                <!-- ============================================================== -->
                <!-- Review -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                 <?php if (session()->has('success_message')) : ?>
                                    <div class="alert alert-success">
                                        <?= session('success_message') ?>
                                        <?php session()->remove('success_message') ?>
                                    </div>
                                <?php endif ?>
                                <?php if (session()->has('error')) : ?>
                                    <div class="alert alert-danger">
                                        <?= session('error') ?>
                                        <?php session()->remove('error') ?>
                                    </div>
                                <?php endif ?>
                                <div class="row">
                                <div class="col-lg-6 col-md-6">
                                     <h5 class="card-title">Add Sports</h5>
                                     <form id="add-cleaners" action="<?=current_url();?>" method="post" style="max-width: 400px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Sports</label>
                                            <input type="text" id="name" name="name" value="<?=set_value('name')?>" class="form-control" placeholder="">
                                            <div class="text-danger">
                                                <?= session('errors.name') ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                              
                                  <button type="submit" class="btn btn-info">Add</button>
                                  <!--<button type="reset" class="btn btn-danger">Clear</button>-->
                            </form>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                <?php 
                                if(!empty($sports)){
                                ?>
                                <div class="table-responsive m-t-40">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Manufacturer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i=1;
                                            foreach ($sports as $data) { ?>
                                            <tr>
                                                <td><?=$i;?></td>
                                                <td><?=!empty($data->sports_name)?$data->sports_name:'';?></td>
                                                <td><a href="<?=base_url()?>/admin/edit_manufaturer/<?= !empty($data->sports_id)?$data->sports_id:'';?>"  class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_manufaturer('<?= !empty($data->sports_id)?$data->sports_id:'';?>')" class="btn btn-sm btn-danger">Delete</a></td>
                                            </tr>
                                        <?php $i++;} ?>
                                          
                                            
                                        </tbody>
                                    </table>
                                </div>

                            <?php } ?>
                            </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                <!-- ============================================================== -->
                <!-- End Review -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Comment - chats -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- End Comment - chats -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- End Page Content -->
                <!-- ============================================================== -->
               
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer">
            © 2019 Eliteadmin by themedesigner.in
        </footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<!-- This is data table -->
    <script src="<?=base_url()?>/public/assets/node_modules/datatables/datatables.min.js"></script>
<script>
    $(function() {
        $('#myTable').DataTable();
    });
    function delete_manufaturer(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this manufaturer?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_manufaturer",
                method:"POST",
                data: {'mfr_id':id},
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