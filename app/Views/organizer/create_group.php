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
                                       <option value="<?=$mfr->listing_id;?>" <?php echo (!empty($groupdata->grp_tournament) && $groupdata->grp_tournament==$mfr->listing_id)?" selected=' selected'":""?>><?=ucfirst($mfr->listing_title);?></option>
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
                                               <option value="<?=$mfr->listing_event_id;?>" <?php echo (!empty($groupdata->grp_event) && $groupdata->grp_event==$mfr->listing_event_id)?" selected=' selected'":""?>><?=ucfirst($mfr->event_name??'');?></option>
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
                                 <input type="text" id="group_name" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="group_name" value="<?=!empty($groupdata->grp_name)?$groupdata->grp_name:''?>" placeholder="Group name" required>
                                 <div class="text-danger">
                                    <?= session('errors.group_name') ?>
                                </div>
                               </div>
                         </div>

                         <div class="row">
                              <div class="col-md-2">
                                  </div>
                             <div class="col-md-6 text-left">
                                <input type="hidden" name="grp_id" value="<?=!empty($groupdata->grp_id)?$groupdata->grp_id:''?>">
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

</script>
<?=$this->endSection();?>