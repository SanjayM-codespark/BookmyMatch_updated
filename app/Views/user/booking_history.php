<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="" style="background: #10141E;min-height: 70vh;">
    <div class="container">
	
      <div class="card profile-head1 text-center selectioncard" style="border-radius: 15px;bordere position: relative;">
         <div class="profile-head2"><br><br>
				   <h5>Booking History</h5>
				   </div><br>
		<div class="card-body">
        <?php 
        if(!empty($bookings)){
        ?>
        <div class="table-responsive">
            <table id="myTable" class="table pay-table table-bordered table-striped text-white">
                <thead>
                    <tr>
                        <th>ID</th>
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
                foreach ($bookings as $data) {

                ?>
                    <tr>
                        <td>
                            <?php
                             if(!empty($data->book_id)){
                                 echo $data->book_id;
                             }
                            ?>
                            
                        </td>
                        <td>
                            <?php
                             if(!empty($data->listing_title)){
                                 echo $data->listing_title;
                             }
                            ?>
                            
                        </td>
                        <td>
                            <?php 
                             $results = $commonModel->select('events','*',array('event_id'=>$data->listing_event_title));
                             if(!empty($results)){
                                    echo $results->event_name;
                             
                             }
                            ?>
                        </td>
                        <td><?php 
                        $book_id = $data->book_id;
                        $res = $commonModel->select('participants','*',array('participant_book_id'=>$book_id),true);
                        if(!empty($res)){
                            foreach ($res as $row) {
                                echo $row->participant_name.'<br/>';
                            }
                        }

                        ?></td>
                        <td><?=!empty($data->payment_txn_id)?$data->payment_txn_id:'';?></td>
                        <td><?=!empty($data->payment_amount)?$data->payment_amount:'';?></td>
                        <td><?=!empty($data->book_created_at)?date('d-m-Y',strtotime($data->book_created_at)):'';?></td>
                        
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
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>

</script>
<?=$this->endSection();?>