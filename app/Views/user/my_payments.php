<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="" style="background: #10141E;min-height: 70vh;">
    <div class="container">
	
      <div class="card profile-head1 text-center selectioncard" style="border-radius: 15px;bordere position: relative;">
         <div class="profile-head2"><br><br>
				   <h5>My Payments</h5>
				   </div><br>
		<div class="card-body">
        <?php 
        if(!empty($payments)){
        ?>
        <div class="table-responsive">
            <table id="myTable" class="table pay-table table-bordered table-striped text-white">
                <thead>
                    <tr>
                        <th>Tournament Name</th>
                        <th>Event Name</th>
                        <th>Team</th>
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
                            foreach ($res as $row) {
                                echo $row->participant_name.'<br/>';
                            }
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
        <?php }else{ ?>
        <div class="jumbotron text-center">
          <h5>No data found</h5>
        </div>
        <?php } ?>
        </div>
      </div>  
    </div>
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>

</script>
<?=$this->endSection();?>