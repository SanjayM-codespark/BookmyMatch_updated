<?=$this->extend('base');?>
<?=$this->section('pageCss');?>
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section class="" style="background:#10141E;padding-top:30px;min-height: 70vh;color:#ffffff;">
<div class="container">
    <h2 class="text-white text-center">FAQs</h2>
    <p>
      <div class="accordion" id="accordionExample">
         <?php if(isset($faq)){
              $i=1;
              foreach($faq as $faqs){
          ?>
          <div class="card"  style="border:1px solid #f11616;">
            <div class="card-header" id="heading<?=$i;?>" style="background: #202739;">
              <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left <?=($i==1)?'':'collapsed';?>" type="button" data-toggle="collapse" data-target="#collapse<?=$i;?>" aria-expanded="true" aria-controls="collapse<?=$i;?>" style="color:#fff;">
                  <?php echo $faqs->faq_question; ?>
                </button>
              </h2>
            </div>
        
            <div id="collapse<?=$i;?>" class="collapse <?=($i==1)?'show':'';?>" aria-labelledby="heading<?=$i;?>" data-parent="#accordionExample" style="background:#10141E;">
              <div class="card-body">
                <?php echo $faqs->faq_answer; ?>
              </div>
            </div>
          </div>
          
          <?php 
            $i++;  }
          }
          ?>
          
        </div>
    </p>
</div>



</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<?=$this->endSection();?>