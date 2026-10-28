<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
<?php <?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
        <ul class="alert alert-danger">
        <?php foreach (<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
<?php <?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
        <ul class="alert alert-danger">
        <?php foreach (session('errors') as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>_errors = flash_errors(); ?>        <ul class="alert alert-danger">
        <?php foreach (session('errors') as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>_errors as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>_errors = flash_errors(); ?>        <ul class="alert alert-danger">
        <?php foreach (<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
<?php <?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
      <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
        <ul class="alert alert-danger">
        <?php foreach (session('errors') as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>_errors = flash_errors(); ?>        <ul class="alert alert-danger">
        <?php foreach (session('errors') as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>_errors as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endif  ?>
        <div class="row">
          <div class="col-md-6 col-6">
            <h4 class="text-black mt-3">List Of Payments</h4>
          </div>

        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card p-1" style="overflow-x:auto;">

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide"  id="tournaments" name="tournaments" required>
                                <option value="">Select Tournament</option>
                                <?php if(!empty($tournaments)){
                                    foreach ($tournaments as $data) { ?>
                                        <option value="<?=$data->listing_id;?>" <?=!empty($_GET['tournaments']) && ($_GET['tournaments']==$data->listing_id)?'selected':'';?>><?=ucfirst($data->listing_title);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_community') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           
                            <select class="form-control wide" id="events" name="events" required>
                                <option value="">Select Events</option>
                                <?php if(!empty($events)){
                                    foreach ($events as $data) { ?>
                                        <option value="<?=$data->listing_event_id;?>" <?=!empty($_GET['events']) && ($_GET['events']==$data->listing_event_id)?'selected':'';?>><?=ucfirst($data->event_name);?></option>
                                <?php }}?>
                            </select>
                            <div class="text-danger"><?= session('errors.bed_property') ?></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" class="btn btn-info d-block btn-md filter-report">Fitler</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <?php 
                if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <th>Team Member Count</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                        <?php
                        foreach ($payments as $data) {
                        ?>
                            <tr>
                                <td><?=$data->payment_id;?></td>
                                <td>
                                    <?php $listing_id = $data->payment_tournament_id;
                                     $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                     if(!empty($res)){
                                         echo $res->listing_title;
                                     }
                                    ?>
                                    
                                </td>
                                <td>
                                    <?php $event_id = $data->payment_event_id;
                                     $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                     if(!empty($res)){
                                     $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                     if(!empty($results)){
                                            echo $results->event_name;
                                     
                                     }
                                     }
                                    ?>
                                </td>
                                <td><?php 
                                $pay_id = $data->payment_id;
                                $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                                if(!empty($res)){
                                    echo count($res);
                                }

                                ?></td>
                                <td><?=$data->payment_txn_id;?></td>
                                <td><?=$data->payment_amount;?></td>
                                <td><?=$data->payment_date;?></td>
                                
                            </tr>
                            <?php }?>
                        </tbody>
                    
                    </table>

                </div>
                <div>
                    <?php
                        $pager = service('pager');
                        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
                    ?>
                </div>
                <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
                <?php } ?>

            </div>
          </div>
        </div>

      </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script type="text/javascript">
     $(document).on('change', '#tournaments', function(){
  var cmty_id = $(this).val();
  get_events(cmty_id);
});

function get_events(listing_id){
  var href = '<?=base_url('admin/get_events') ?>';
    $.ajax({
       url: href,
       type: 'POST',
       data: {'listing_id': listing_id,'<?= csrf_token()?>':$('#csrftoken').val()},
       success: function(res) {
       var obj = $.parseJSON(res);
       $('#csrftoken').val(obj.csrf_token)
        if (obj.success == 1) {
          $('#events').empty();
          $('#events').append('<option value="0">Select Events</option>');
          $.each(obj.msg,function(key, value)
           {
                $("#events").append('<option value='+value['listing_event_id']+'>'+value['event_name']+'</option>');
           });                           
       }else{
         $("#events").empty();
         $("#events").append('<option value="">No data found</option>');
       }
    }
   })


}
 $('.filter-report').click(function () {
        var tournaments = $('#tournaments').val();
        var events = $('#events').val();
       
        if(! tournaments){
            tournaments="";
        }
        if(! events){
            events="";
        }
        var url = "<?php echo base_url(); ?>/organizer/my_payments?tournaments="+tournaments+"&events="+events;
        window.open(url);
    });
 </script>
<?=$this->endSection();?>
