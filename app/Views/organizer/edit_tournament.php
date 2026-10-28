<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/plugins/minimal_rich_editor/richtext.min.css">
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>
        <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-4" style="overflow-x:auto;background: #fff;">
                        <?php if (session()->has('success_message')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session('success_message') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php session()->remove('success_message') ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->has('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php session()->remove('error') ?>
                            </div>
                        <?php endif; ?>
                      
                        <?php if (!empty($listings)) { ?>
                            <form id="edit-tournament-form" action="<?= current_url(); ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field(); ?>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Title <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="title" name="title" value="<?= old('title', $listings->listing_title); ?>" class="form-control inputgroups" placeholder="Tournament Title" required>
                                        <div class="text-danger">
                                            <?= session('errors.title') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Choose your Game <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control custom-select inputgroups" id="sports" name="sports" required>
                                            <option value="">--Select Sports--</option>
                                            <?php if (!empty($sports)) {
                                                foreach ($sports as $vt) { ?>
                                                    <option value="<?= $vt->sports_id; ?>" <?= (!empty($listings->listing_sports_id) && $vt->sports_id == $listings->listing_sports_id) ? 'selected' : '' ?>><?= ucfirst($vt->sports_name); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div class="text-danger">
                                            <?= session('errors.sports') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $date1 = !empty($listings->listing_start_date) ? date("Y-m-d\TH:i", strtotime($listings->listing_start_date)) : ''; ?>
                                        <input type="datetime-local" id="start_date" name="start_date" value="<?= $date1; ?>" class="form-control inputgroups" required>
                                        <div class="text-danger">
                                            <?= session('errors.start_date') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $date2 = !empty($listings->listing_end_date) ? date("Y-m-d\TH:i", strtotime($listings->listing_end_date)) : ''; ?>
                                        <input type="datetime-local" id="end_date" name="end_date" value="<?= $date2; ?>" class="form-control inputgroups" required>
                                        <div class="text-danger">
                                            <?= session('errors.end_date') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Last Booking Date</label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $date3 = !empty($listings->listing_lastbook_date) ? date("Y-m-d\TH:i", strtotime($listings->listing_lastbook_date)) : ''; ?>
                                        <input type="datetime-local" id="lastbook_date" name="lastbook_date" value="<?= $date3; ?>" class="form-control inputgroups">
                                        <small class="text-muted" style="font-size:11px;">Must be on or before the Start Date</small>
                                        <div class="text-danger">
                                            <?= session('errors.lastbook_date') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Country <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control custom-select inputgroups" id="country" name="country" required>
                                            <option value="">--Select Country--</option>
                                            <?php if (!empty($countries)) {
                                                foreach ($countries as $vt) { ?>
                                                    <option value="<?= $vt->country_id; ?>" <?= (!empty($listings->listing_country_id) && $vt->country_id == $listings->listing_country_id) ? 'selected' : '' ?>><?= ucfirst($vt->country_name); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div class="text-danger">
                                            <?= session('errors.country') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">State <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control custom-select inputgroups" id="state" name="state" required>
                                            <option value="">--Select State--</option>
                                            <?php if (!empty($states)) {
                                                foreach ($states as $vt) { ?>
                                                    <option value="<?= $vt->state_id; ?>" <?= (!empty($listings->listing_state_id) && $vt->state_id == $listings->listing_state_id) ? 'selected' : '' ?>><?= ucfirst($vt->state_name); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div class="text-danger">
                                            <?= session('errors.state') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">District <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control custom-select inputgroups" id="district" name="district" required>
                                            <option value="">--Select District--</option>
                                            <?php if (!empty($district)) {
                                                foreach ($district as $vt) { ?>
                                                    <option value="<?= $vt->district_id; ?>" <?= (!empty($listings->listing_district_id) && $vt->district_id == $listings->listing_district_id) ? 'selected' : '' ?>><?= ucfirst($vt->district_name); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div class="text-danger">
                                            <?= session('errors.district') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">City <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control custom-select inputgroups" id="city" name="city" required>
                                            <option value="">--Select City--</option>
                                            <?php if (!empty($cities)) {
                                                foreach ($cities as $vt) { ?>
                                                    <option value="<?= $vt->city_id; ?>" <?= (!empty($listings->listing_city_id) && $vt->city_id == $listings->listing_city_id) ? 'selected' : '' ?>><?= ucfirst($vt->city_name); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <div class="text-danger">
                                            <?= session('errors.city') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Venue <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <textarea class="form-control address inputgroups" id="address" name="address" rows="3" required><?= old('address', $listings->listing_address); ?></textarea>
                                        <div class="text-danger">
                                            <?= session('errors.address') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6">
                                        <textarea class="form-control details inputgroups" id="details" name="details" rows="5" required><?= old('details', $listings->listing_details); ?></textarea>
                                        <div class="text-danger">
                                            <?= session('errors.details') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Upload Banner</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <?php if (!empty($listings->listing_image) && file_exists(FCPATH . "public/uploads/listings/images/{$listings->listing_user_id}/{$listings->listing_image}")) { ?>
                                                <div class="img-div mb-2">
                                                    <img src="<?= base_url() . "/public/uploads/listings/images/{$listings->listing_user_id}/{$listings->listing_image}" ?>" width="200" height="100" class="img-thumbnail">
                                                    <button type="button" class="btn btn-sm btn-danger remove-image" data-type="banner">Remove</button>
                                                </div>
                                            <?php } ?>
                                            <input type="file" name="listing_image" class="form-control-file" accept="image/*">
                                            <input type="hidden" class="form-control photo" name="photo" value="<?= $listings->listing_image; ?>">
                                            <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
                                            <div class="text-danger">
                                                <?= session('errors.listing_image') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Rules & Regulations</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <textarea class="form-control todolist" name="rules"><?= old('rules', $listings->listing_rules); ?></textarea>
                                            <div class="text-danger">
                                                <?= session('errors.rules') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="col-form-label">Upload Rules File</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <?php if (!empty($listings->listing_rules_file) && file_exists(FCPATH . "public/uploads/listings/rules/{$listings->listing_user_id}/{$listings->listing_rules_file}")) { ?>
                                                <div class="mb-2">
                                                    <a href="<?= base_url() . "/public/uploads/listings/rules/{$listings->listing_user_id}/{$listings->listing_rules_file}" ?>" target="_blank" class="btn btn-sm btn-info">View Current Rules File</a>
                                                    <button type="button" class="btn btn-sm btn-danger remove-rules-file">Remove</button>
                                                </div>
                                            <?php } ?>
                                            <input type="file" name="listing_rules_file" class="form-control-file">
                                            <small class="form-text text-muted">Upload PDF, DOC, or TXT files</small>
                                            <div class="text-danger">
                                                <?= session('errors.listing_rules_file') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-danger btn-lg" style="width: 100%;">Update Tournament</button>
                                    </div>
                                </div>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-warning text-center">
                                <h5>Tournament not found!</h5>
                                <a href="<?= base_url('organizer'); ?>" class="btn btn-primary mt-2">Go Back</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Modal for user activation -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Activation Required</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please activate your account before updating tournaments.</p>
                <p>Contact administrator for assistance.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="<?= base_url('user/profile'); ?>" class="btn btn-primary">Complete Profile</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->Section('pageScript') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>/public/assets/plugins/minimal_rich_editor/jquery.richtext.js"></script>

<script>
$(document).ready(function() {
    // Initialize rich text editor
    $('.todolist').richText({
        height: 350,
        fontColor: false,
        imageUpload: false,
        fileUpload: false,
        videoEmbed: false,
        urls: false,
        table: false,
        removeStyles: false,
        code: false
    });
    
    // Initialize wysihtml5 editors (only if the plugin loaded)
    if ($.fn.wysihtml5) {
        $('.address').wysihtml5('deepExtend', {
            "font-styles": true,
            "emphasis": true,
            "lists": true,
            "html": true,
            "image": false,
            "link": false,
            "format-code": false,
        });

        $('.details').wysihtml5('deepExtend', {
            "font-styles": true,
            "emphasis": true,
            "lists": true,
            "italic": false,
            "html": true,
            "image": false,
            "link": false,
            "format-code": true,
            "blockquote": true,
        });

        // Remove italic and underline buttons
        $('.address,.details').each(function() {
            var contentPar = $(this).parent();
            contentPar.find('[data-wysihtml5-command="italic"]').remove();
            contentPar.find('[data-wysihtml5-command="underline"]').remove();
            contentPar.find('iframe').css('height', '300px');
        });
    }
    
    // Country change handler - Load states
    $(document).on('change', '#country', function() {
        var countryId = $(this).val();
        if (countryId) {
            $.ajax({
                type: "POST",
                data: {'country': countryId},
                url: "<?= base_url('Ajax/get_states'); ?>",
                success: function(html) {
                    $('#state').html(html);
                    $('#district').html('<option value="">--Select District--</option>');
                    $('#city').html('<option value="">--Select City--</option>');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading states:", error);
                }
            });
        }
    });

    // State change handler - Load districts
    $(document).on('change', '#state', function() {
        var stateId = $(this).val();
        if (stateId) {
            $.ajax({
                type: "POST",
                data: {'state': stateId},
                url: "<?= base_url('Ajax/get_districts'); ?>",
                success: function(html) {
                    $('#district').html(html);
                    $('#city').html('<option value="">--Select City--</option>');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading districts:", error);
                }
            });
        }
    });
    
    // District change handler - Load cities
    $(document).on('change', '#district', function() {
        var districtId = $(this).val();
        if (districtId) {
            $.ajax({
                type: "POST",
                data: {'district': districtId},
                url: "<?= base_url('Ajax/get_cities'); ?>",
                success: function(html) {
                    $('#city').html(html);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading cities:", error);
                }
            });
        }
    });

    // File upload handler
    $(document).on("change", "input[type='file']", function() {
        var file = this.files[0];
        if (file) {
            var fileType = file["type"];
            var validImageTypes = ["image/gif", "image/jpg", "image/jpeg", "image/png"];
            
            if ($(this).attr('name') === 'listing_image') {
                if ($.inArray(fileType, validImageTypes) < 0) {
                    alert('Invalid file type. Only jpg, jpeg, png, gif files are allowed');
                    $(this).val('');
                    return false;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size should be less than 5MB');
                    $(this).val('');
                    return false;
                }
            }
            
            var form_data = new FormData();
            form_data.append("images", file);
            
            var $this = $(this);
            var $imgDiv = $this.closest('.mb-3').find('.img-div');
            
            $.ajax({
                dataType: 'json',
                url: "<?= base_url('Ajax/uploadImage'); ?>",
                method: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $imgDiv.html("<label class='text-success'>Uploading...</label>");
                },
                success: function(data) {
                    if (data.status) {
                        var src = "<?= base_url(); ?>/public/uploads/listings/<?= session()->get('user_id'); ?>/" + data.filename;
                        var img = "<img src='" + src + "' width='200' height='100' class='img-thumbnail'>";
                        $imgDiv.html(img);
                        $this.closest('.mb-3').find('.photo').val(data.filename);
                    } else {
                        alert(data.data?.images || 'Upload failed');
                        $imgDiv.html('');
                    }
                },
                error: function() {
                    alert('Upload failed. Please try again.');
                    $imgDiv.html('');
                }
            });
        }
    });
    
    // Remove image handler
    $(document).on('click', '.remove-image', function() {
        if (confirm('Are you sure you want to remove this image?')) {
            $(this).closest('.img-div').remove();
            $(this).closest('.mb-3').find('input[type="file"]').val('');
        }
    });
    
    // Remove rules file handler
    $(document).on('click', '.remove-rules-file', function() {
        if (confirm('Are you sure you want to remove the rules file?')) {
            $(this).closest('.mb-3').find('.mb-2').remove();
            $(this).closest('.mb-3').find('input[type="file"]').val('');
            // You might want to add an AJAX call here to delete the file from server
        }
    });
    
    // Form submission validation
    $('#edit-tournament-form').on('submit', function(e) {
        if (!$('#district').val()) {
            alert('Please select a district');
            e.preventDefault();
            return false;
        }
        if (!$('#city').val()) {
            alert('Please select a city');
            e.preventDefault();
            return false;
        }
        if ($('#start_date').val() && $('#lastbook_date').val() && $('#lastbook_date').val() > $('#start_date').val()) {
            alert('Last Booking Date must be on or before the Start Date.');
            e.preventDefault();
            return false;
        }
        if ($('#lastbook_date').val() && $('#lastbook_date').val() < todayStr) {
            alert('Last Booking Date must be today or later.');
            e.preventDefault();
            return false;
        }
        if ($('#start_date').val() && $('#end_date').val() && $('#end_date').val() < $('#start_date').val()) {
            alert('End Date must be on or after the Start Date.');
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Date restriction logic (mirrors create_tournament)
    var pad = function(n) { return (n < 10 ? '0' : '') + n; };
    var now = new Date();
    var todayStr = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T00:00';

    function applyDateLimits() {
        var s = $('#start_date').val();
        var $start = $('#start_date'), $end = $('#end_date'), $last = $('#lastbook_date');

        // Cannot pick a date before today
        $start.attr('min', todayStr);
        $last.attr('min', todayStr);

        // Last Booking Date: today .. Start Date
        if (s) {
            $last.attr('max', s);
            if ($last.val() && $last.val() > s) {
                $last.val('');
            }
        } else {
            $last.removeAttr('max');
        }

        // End Date: must be on or after Start Date
        if (s) {
            $end.attr('min', s);
            if ($end.val() && $end.val() < s) {
                $end.val('');
            }
        } else {
            $end.removeAttr('min');
        }
    }
    applyDateLimits();
    $(document).on('change', '#start_date', applyDateLimits);
});

function user_active() {
    $("#myModal").modal("show");
}
</script>

<style>
.dash-head1 {
    color: #000;
    font-weight: 600;
}
.dash-link1 {
    color: #000;
    text-decoration: none;
}
.img-thumbnail {
    border-radius: 5px;
    margin-right: 10px;
}
.remove-image, .remove-rules-file {
    margin-top: 10px;
}
.alert {
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>

<?= $this->endSection(); ?>