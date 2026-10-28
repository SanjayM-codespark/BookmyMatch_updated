<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
        <section>
    <div class="container-fluid">
         <div class="row">
             <div class="col-md-6 col-6">
                 <h4 class="text-black mt-3" >Edit Your Event</h4>
                 
             </div>
             
         </div>
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
                         <label for="inputPassword6" class="col-form-label ">Event Title</label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control custom-select inputgroups" id="title" name="title">
                                <option value=""></option>
                                <?php
                                if(!empty($event_list)){
                                    foreach ($event_list as $mfr) { ?>
                                       <option value="<?=$mfr->event_id;?>" <?php echo (set_value('title')==$mfr->event_id)?" selected=' selected'":""?>><?=ucfirst($mfr->event_name);?></option>
                                <?php   } } ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.title') ?>
                            </div>
                        </div>
                               
                         </div>
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Event Type</label>
                               </div>
                               <div class="col-md-6">
                                     <select class="form-control custom-select inputgroups" id="event_type" name="event_type">
                                     <option value=""></option>
                                        <option value="individual" <?php echo (set_value('event_type')=='individual')?" selected=' selected'":""?>>Individual</option>
                                        <option value="team" <?php echo (set_value('event_type')=='team')?" selected=' selected'":""?>>Team</option>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event_type') ?>
                                    </div>
                                </div>
                               
                         </div>
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Minimum Member</label>
                               </div>
                               <div class="col-md-6">
                                 <input type="number" id="inputPassword6" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="min_member" onchange="validateNegativeInput(this)" value="<?=safe_set_value('min_member')?>" placeholder="Choose the minimum members for your Event">
                                 <div class="text-danger">
                                    <?= session('errors.min_member') ?>
                                </div>
                               </div>
                               
                         </div>
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Match Type</label>
                               </div>
                               <div class="col-md-6">
                                 <input type="text" id="inputPassword6" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="type" value="<?=safe_set_value('type')?>" placeholder="Type your Match Type">
                                 <div class="text-danger">
                                    <?= session('errors.type') ?>
                                </div>
                               </div>
                               
                         </div>
                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Fee Per Head</label>
                               </div>
                               <div class="col-md-6">
                                 <input type="text" id="inputPassword6" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="fee" value="<?=safe_set_value('fee')?>" placeholder="Specify the Fees for your Event">
                                 <div class="text-danger">
                                    <?= session('errors.fee') ?>
                                </div>
                               </div>
                         </div>

                         <!-- <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Duration</label>
                               </div>
                               <div class="col-md-6">
                                 <input type="text" class="form-control inputgroups" aria-describedby="passwordHelpInline" id="duration" name="duration" value="<?=!empty($event->listing_event_duration)?$event->listing_event_duration:set_value('duration')?>" placeholder="Duration">
                                 <div class="text-danger">
                                    <?= session('errors.duration') ?>
                                </div>
                               </div>
                               
                         </div> -->


                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">Start Date</label>
                               </div>
                               <div class="col-md-6">
                                    <input type="datetime-local" id="start_date" name="event_start" value="<?=safe_set_value('event_start')?>" class="form-control inputgroups" placeholder="Start Date">
                                    <div class="text-danger">
                                        <?= session('errors.event_start') ?>
                                    </div>
                               </div>
                         </div>

                          <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="inputPassword6" class="col-form-label ">End Date</label>
                               </div>
                               <div class="col-md-6">
                                    <input type="datetime-local" id="end_date" name="event_end" value="<?=safe_set_value('event_end')?>" class="form-control inputgroups" placeholder="End Date">
                                    <div class="text-danger">
                                        <?= session('errors.event_end') ?>
                                    </div>
                               </div>
                         </div>

                         <div class="row mb-3">
                             <div class="col-md-2">
                                 <label for="matchmaking_date" class="col-form-label">Players Reporting Time</label>
                             </div>
                             <div class="col-md-6">
                                 <input type="datetime-local" id="matchmaking_date" name="matchmaking_date" value="<?=safe_set_value('matchmaking_date')?>" class="form-control inputgroups" placeholder="Players Reporting Time">
                                 <small class="text-muted">Date from which matchmaking can be triggered for team events</small>
                                 <div class="text-danger">
                                     <?= session('errors.matchmaking_date') ?>
                                 </div>
                             </div>
                         </div>

                         <div class="row mb-3">
                            <div class="col-md-2">
                               <label for="min_member" class="col-form-label ">IDs</label>
                            </div>
                            <div class="col-md-10">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="tnba_id" value="1">
                                  <label class="form-check-label" for="inlineCheckbox1">TNBA-ID</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="bai_id" value="1">
                                  <label class="form-check-label" for="inlineCheckbox2">BAI-ID</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" id="inlineCheckbox3" name="other_id" value="1">
                                  <label class="form-check-label" for="inlineCheckbox3">Other-ID</label>
                                </div>

                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" id="inlineCheckbox4" name="dob" value="1">
                                  <label class="form-check-label" for="inlineCheckbox4">DOB</label>
                                </div>

                            </div>
                        </div>
                        
                        
                         <div class="row">
                              <div class="col-md-2">
                                  </div>
                             <div class="col-md-6 text-left">
                                 <button type="submit" class="btn p-0" style="width: 320px;height: 36px;left: 720px;top: 751px;background: #E71A2D;border-radius: 4px;color: #FFFFFF;padding: 12px;">CREATE</button>
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
$(document).on('change','#country',function(){
   var cslug = $(this).val();
   $.ajax({
        type: "POST",
        data: { 'country': cslug},
        url: "<?php echo base_url(); ?>/Ajax/get_cities",
        success: function( html ) {
            $('#city').html(html);
        }
    }); 
    
});

$(document).on('change','#event_type',function(){
   var cslug = $(this).val();
   if(cslug == 'team'){
    $('.fee-label').html('Fee per team');
   }else{
    $('.fee-label').html('Fee per head');
   }
    
});   
</script>
<script>
function validateNegativeInput(white) { 
    var keyValue = white.value;
    if(keyValue <= 0){
        white.value = '';
    }
}
</script>
<script>
// Keep event dates inside the tournament start / end date range
var TOURNEY_START = '<?= !empty($listing) && !empty($listing->listing_start_date) ? date('Y-m-d\TH:i', strtotime($listing->listing_start_date)) : '' ?>';
var TOURNEY_END   = '<?= !empty($listing) && !empty($listing->listing_end_date) ? date('Y-m-d\TH:i', strtotime($listing->listing_end_date)) : '' ?>';

function clearOutOfRange($el) {
    var v = $el.val();
    if (!v) return;
    var min = $el.attr('min'), max = $el.attr('max');
    if (min && v < min) $el.val('');
    if (max && v > max) $el.val('');
}

function applyDateLimits() {
    var $start = $('#start_date'), $end = $('#end_date'), $match = $('#matchmaking_date');
    var sVal = $start.val(), eVal = $end.val();

    // Event start: must be inside tournament range
    if (TOURNEY_START) { $start.attr('min', TOURNEY_START); } else { $start.removeAttr('min'); }
    if (TOURNEY_END)   { $start.attr('max', TOURNEY_END); }   else { $start.removeAttr('max'); }

    // Event end: >= event start (else tournament start), <= tournament end
    var endMin = sVal || TOURNEY_START;
    if (endMin) { $end.attr('min', endMin); } else { $end.removeAttr('min'); }
    if (TOURNEY_END) { $end.attr('max', TOURNEY_END); } else { $end.removeAttr('max'); }

    // Reporting time: between event start and event end
    if (sVal) { $match.attr('min', sVal); } else { $match.removeAttr('min'); }
    if (eVal) { $match.attr('max', eVal); } else { $match.removeAttr('max'); }

    clearOutOfRange($start);
    clearOutOfRange($end);
    clearOutOfRange($match);
}

$(function() {
    applyDateLimits();
    $(document).on('change', '#start_date, #end_date, #matchmaking_date', applyDateLimits);

    $('form').on('submit', function(e) {
        var s = $('#start_date').val(), en = $('#end_date').val();
        if (TOURNEY_START && s && s < TOURNEY_START) {
            alert('Start Date must be on or after the tournament start date.');
            e.preventDefault(); return false;
        }
        if (TOURNEY_END && s && s > TOURNEY_END) {
            alert('Start Date must be on or before the tournament end date.');
            e.preventDefault(); return false;
        }
        if (TOURNEY_START && en && en < TOURNEY_START) {
            alert('End Date must be on or after the tournament start date.');
            e.preventDefault(); return false;
        }
        if (TOURNEY_END && en && en > TOURNEY_END) {
            alert('End Date must be on or before the tournament end date.');
            e.preventDefault(); return false;
        }
        if (s && en && en < s) {
            alert('End Date must be on or after the Start Date.');
            e.preventDefault(); return false;
        }
    });
});
</script>
<?=$this->endSection();?>