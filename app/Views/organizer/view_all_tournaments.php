<?= $this->extend('base'); ?>
<?= $this->section('pageCss'); ?>
<style>
	.wrap-thumb {
		position: relative;
		height: 140px;
		width: 100%;
	}

	.wrap-img img,
	.wrap-thumb img {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		margin: auto;
		max-height: 100%;
		max-width: 100%;
	}
</style>

<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>

<div class="container">
	<p><b>Organization</b></p>
	<p><?= !empty($organizer_details->user_organization_name) ? $organizer_details->user_organization_name : ''; ?></p>
	<p><?= !empty($organizer_details->user_mobile) ? $organizer_details->user_mobile : ''; ?></p>
	<p><?= !empty($organizer_details->user_email) ? $organizer_details->user_email : ''; ?></p>
	<p><b>Tournaments</b></p>

	<div class="row">

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 px-0">
			<div class="list-group">
				<?php
				if (!empty($tournaments)) {
					$i = 0;
					foreach ($tournaments as $data) {
						//print_array($data);die();
						if ((($i % 5) == 0) && ($i > 0)) { ?>
							<!---- Banner ads ------------------>
							<div class="list-group-item list-group-item-action">

								<div class="row">
									<?php  //echo $this->advertise->advertise('banner');
									?>
								</div>
							</div>
							<!---- End Banner ads ------------------>
						<?php	} ?>
						<a href="<?= base_url(); ?>/view_tournament_details/<?= $data->listing_id; ?>" class="list-group-item list-group-item-action text-body">
							<div class="row">

								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 d-flex">
									<div class="wrap-thumb">
										<img src="<?= base_url(); ?>/public/uploads/listings/<?= $data->user_id; ?>/<?= $data->listing_image; ?>" alt="Cinque Terre">
									</div>
								</div>
								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
									<div class="add-details">
										<?php
										$now = strtotime(date('Y-m-d H:i:s'));
										$startdate = strtotime($data->listing_start_date);
										$enddate = strtotime($data->listing_end_date);
										if ($startdate < $now) {
											$match_status = "Completed";
										} else {
											$match_status = "Upcoming";
										}
										if ($startdate < $now && $enddate > $now) {
											$match_status = "Live";
										}
										?>
										<span class="badge badge-warning p-1"><?= $match_status; ?></span>
										<span class="badge badge-primary p-1"><?= $data->sports_name; ?></span>
										<div class="mb-1 text-uppercase"><?= $data->listing_title; ?></div>
										<div class="mb-1"><?= date('D, dS M h:i a', strtotime($data->listing_start_date)); ?> - <?= date('D, dS M h:i a', strtotime($data->listing_end_date)); ?> </div>
										<div><?= $data->listing_address; ?></div>
									</div>
								</div>
							</div>
						</a>

					<?php $i++;
					}
				} else { ?>
					<div class="list-group-item list-group-item-action" style="min-height:100px;">
						<div class="text-center">
							<h5 class="display-4 text-center"><i class="fa fa-ban" aria-hidden="true"></i>
							</h5>
							<h3>Opps!</h3>
							<h5>Tournaments not found.</h5>
						</div>
					</div>

				<?php } ?>
			</div>
		</div>
	</div>

</div>
<p class="">
	<?php
	$pager = service('pager');
	echo $pager->makeLinks($page, $perPage, $total, 'tourney_default_full');
	?>
</p> <!-- Pagination -->




<!-- The Modal -->
<div class="modal" id="myModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header border-0 p-1 m-0">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<div class="modal-body mt-0 pt-0">
				<h6 class="modal-title text-center" style="position: relative;top: -15px;">POPULAR CITIES IN INDIA</h6>
				<?php
				if (isset($famous_region) && !empty($famous_region)) {
					$rows = ceil(count($famous_region) / 3);
					$all_items = array_chunk($famous_region, $rows);
					echo '<div class="row">';
					foreach ($all_items as $div_item) {
						echo '<div class="col-md-4">';
						foreach ($div_item as $data) {
							echo '<div class="py-1"><a href="javascript:void(0);" data-city-id="' . $data->city_id . '" class="select-city text-body">' . $data->city_name . '</a></div>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
				?>
				<p class="text-center">
				<ul class="list-inline text-center">
					<li class="list-inline-item text-center"><a href="javascript:void(0)" data-country-id="1" class="text-info select-all-ind">Rest of India</a></li>
					<li class="list-inline-item  text-center"><a href="javascript:void(0)" class="text-info select-global">Global tournaments</a></li>
				</ul>
				</p>
			</div>

		</div>
	</div>
</div>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>
	var current_url = "<?= current_url() ?>";

	$(document).on("change", "#sports", function() {
		var sport = $(this).val();
		var tstat = $("#tstatus option:selected").val();

		$.ajax({
			type: "POST",
			data: {
				'sports': sport,
				'tstatus': tstat
			},
			url: "<?php echo base_url(); ?>/Ajax/search_tournment",
			success: function(html) {
				window.location.href = html;
			}
		});
	});

	$(document).on("change", "#tstatus", function() {
		var tstat = $(this).val();
		var sport = $("#sports option:selected").val();

		$.ajax({
			type: "POST",
			data: {
				'sports': sport,
				'tstatus': tstat
			},
			url: "<?php echo base_url(); ?>/Ajax/search_tournment",
			success: function(html) {
				window.location.href = html;
			}
		});
	});

	$(document).on('click', '.select-city', function() {
		var cid = $(this).attr('data-city-id');
		var cname = $(this).text();
		$('#rInput').val(cname);
		$('.region-name').text(cname);
		$.ajax({
			type: "POST",
			data: {
				cityid: cid,
				cityname: cname
			},
			url: "<?php echo base_url(); ?>/Ajax/set_city",
			success: function(html) {
				window.location.href = current_url;
			}
		});

	});
	$(document).on('click', '.select-all-ind', function() {
		var cid = $(this).attr('data-country-id');
		$.ajax({
			type: "POST",
			data: {
				'countryid': cid
			},
			url: "<?php echo base_url(); ?>/Ajax/all_ind",
			success: function(html) {
				window.location.href = current_url;
			}
		});

	});

	$(document).on('click', '.select-global', function() {
		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>/Ajax/set_global",
			success: function(html) {
				window.location.href = current_url;
			}
		});

	});



	$(document).on('keyup', '#search-input', function(event) {
		var stext = $(this).val();
		if (stext == '') {
			$('#search-list').removeClass('show');
		} else {
			$.ajax({
				type: "POST",
				data: {
					'q': stext
				},
				url: "<?php echo base_url(); ?>/Ajax/search_list",
				success: function(html) {
					$('#search-list').addClass('show');
					$('#search-list').html(html);
				}
			});
		}
	});

	$(document).on("click", ".search-droplist", function() {
		var tstat = $("#tstatus option:selected").val();
		var sport = $("#sports option:selected").val();
		var search = $(this).text();
		$.ajax({
			type: "POST",
			data: {
				'sports': sport,
				'tstatus': tstat,
				'q': search
			},
			url: "<?php echo base_url(); ?>/Ajax/search_tournment",
			success: function(html) {
				window.location.href = html;
			}
		});
	});

	$(document).on('click', '#search', function(event) {
		var tstat = $("#tstatus option:selected").val();
		var sport = $("#sports option:selected").val();
		var search = $('#search-input').val().trim();
		if (search != '') {
			$.ajax({
				type: "POST",
				data: {
					'sports': sport,
					'tstatus': tstat,
					'q': search
				},
				url: "<?php echo base_url(); ?>/Ajax/search_tournment",
				success: function(html) {
					window.location.href = html;
				}
			});
		}

	});
	$('#search-input').keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if (keycode == '13') {
			$('#search').click();
		}
	});
</script>
<?= $this->endSection(); ?>