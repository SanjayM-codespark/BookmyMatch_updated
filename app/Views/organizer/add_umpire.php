<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<?php if (session()->has('success_message')) : ?>
    <div style="display:flex;align-items:center;gap:10px;background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;">
        <i class="fa-solid fa-circle-check"></i>
        <?= session('success_message') ?><?php session()->remove('success_message') ?>
    </div>
<?php endif ?>

<div class="page-card" style="max-width:560px;">
    <div class="page-card-header" style="margin-bottom:22px;">
        <span class="page-card-title"><?= esc($page_name ?? 'Umpire') ?></span>
    </div>

    <form action="<?= current_url() ?>" method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label-sm">Photo</label>
            <input type="file" name="image" class="form-control" accept="image/*"
                   onchange="previewImage(event)" style="height:auto;padding:7px 12px;">
            <div class="field-error"><?= session('errors.image') ?></div>
            <div id="imagePreview" class="mt-2">
                <?php if (!empty($umpire->user_profile_img)) : ?>
                    <img src="<?= base_url('public/uploads/umpires/' . $umpire->user_profile_img) ?>"
                         style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid #E5E7EB;">
                <?php endif ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-sm">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="Umpire's full name"
                   value="<?= set_value('name', $umpire->user_full_name ?? '') ?>">
            <div class="field-error"><?= session('errors.name') ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label-sm">Mobile Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="mobile" placeholder="Mobile number"
                   value="<?= set_value('mobile', $umpire->user_mobile ?? '') ?>">
            <div class="field-error"><?= session('errors.mobile') ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label-sm">Password <?= empty($umpire->user_id) ? '<span class="text-danger">*</span>' : '' ?></label>
            <input type="password" class="form-control" name="password" placeholder="Password">
            <div class="field-error"><?= session('errors.password') ?></div>
        </div>

        <div class="mb-4">
            <label class="form-label-sm">Confirm Password <?= empty($umpire->user_id) ? '<span class="text-danger">*</span>' : '' ?></label>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password">
            <div class="field-error"><?= session('errors.confirm_password') ?></div>
        </div>

        <input type="hidden" name="id" value="<?= $umpire->user_id ?? '' ?>">
        <button type="submit"
                style="padding:9px 28px;background:#2563EB;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
            <?= !empty($umpire->user_id) ? 'Update Umpire' : 'Add Umpire' ?>
        </button>
    </form>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid #E5E7EB;">`;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}
</script>
<style>
.form-label-sm { font-size:13px; font-weight:500; color:#374151; margin-bottom:5px; display:block; }
.field-error { font-size:12px; color:#EF4444; margin-top:3px; }
.form-control { border:1px solid #D1D5DB; border-radius:8px; font-size:14px; }
.form-control:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,.12); outline:none; }
</style>
<?= $this->endSection(); ?>
