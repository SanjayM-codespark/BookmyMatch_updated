<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
    <div class="container-fluid">
         
         <div class="row mt-3">
             <div class="col-md-12">
                 <div class="card p-4" style="overflow-x:auto;background: #fff;">

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

                     <form action="<?=current_url();?>" method="post">
                         
                        <div class="row mb-3">
                         <div class="col-md-2">
                         <label for="inputPassword6" class="col-form-label ">Tournament</label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control custom-select inputgroups" id="tournament" name="tournament" required>
                                <option value=""></option>
                                <?php
                                if(!empty($tournaments)){
                                    foreach ($tournaments as $mfr) { ?>
                                       <option value="<?=$mfr->listing_id;?>" <?php echo (!empty($rounddata->round_tournament) && $rounddata->round_tournament==$mfr->listing_id)?" selected=' selected'":""?>><?=ucfirst($mfr->listing_title??'');?></option>
                                <?php   } } ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.tournament') ?>
                            </div>
                        </div>
                               
                         </div>
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Event</label>
                               </div>
                               <div class="col-md-6">
                                     <select class="form-control custom-select inputgroups" id="event" name="event" required>
                                     <option value=""></option>
                                        <?php
                                        if(!empty($events)){
                                            foreach ($events as $mfr) { ?>
                                               <option value="<?=$mfr->listing_event_id;?>" <?php echo (!empty($rounddata->round_event) && $rounddata->round_event==$mfr->listing_event_id)?" selected=' selected'":""?>><?=ucfirst($mfr->event_name??'');?></option>
                                        <?php   } } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event') ?>
                                    </div>
                                </div>
                               
                         </div>
                         
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Group Name</label>
                               </div>
                               <div class="col-md-6">
                                     <select class="form-control custom-select inputgroups" id="group_name" name="group_name" required>
                                     <option value=""></option>
                                        <?php
                                        if(!empty($tblgroups)){
                                            foreach ($tblgroups as $mfr) { ?>
                                               <option value="<?=$mfr->grp_id;?>" <?php echo (!empty($rounddata->round_group) && $rounddata->round_group==$mfr->grp_id)?" selected=' selected'":""?>><?=ucfirst($mfr->grp_name);?></option>
                                        <?php   } } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event') ?>
                                    </div>
                                </div>
                               
                         </div>

                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Round Name</label>
                               </div>
                               <div class="col-md-6">
                                 <input type="text" id="round_name" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="round_name" value="<?=!empty($rounddata->round_name)?$rounddata->round_name:''?>" placeholder="Round name" required>
                                 <div class="text-danger">
                                    <?= session('errors.round_name') ?>
                                </div>
                               </div>
                         </div>

                         <div class="row">
                              <div class="col-md-2">
                                  </div>
                             <div class="col-md-6 text-left">
                                <input type="hidden" name="round_id" value="<?=!empty($rounddata->round_id)?$rounddata->round_id:''?>">
                                 <button type="submit" class="btn p-0" style="width: 320px;height: 36px;left: 720px;top: 751px;background: #E71A2D;border-radius: 4px;color: #FFFFFF;padding: 12px;">Submit</button>
                             </div>
                         </div>
                         
                     </form>
                       
                       
                 </div>
             </div>
         </div>
        
     </div>
    </section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>   
$(document).on('change','#tournament',function(){
   var cslug = $(this).val();
   $.ajax({
        type: "POST",
        data: {'tourney_id': cslug},
        url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_events",
        success: function( html ) {
            $('#event').html(html);
        }
    }); 
    
});

$(document).on('change','#event',function(){
   var cslug = $(this).val();
   $.ajax({
        type: "POST",
        data: {'event_id': cslug},
        url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_group",
        success: function( html ) {
            $('#group_name').html(html);
        }
    }); 
    
});

</script>
<?=$this->endSection();?>