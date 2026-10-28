<?php
$page_title    = 'Choose a Package';
$page_subtitle = 'Purchase a package to start creating tournaments';
?>
<?= $this->extend('organizer_base'); ?>

<?= $this->section('pageCss'); ?>
<style>
.pkg-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px; margin-top: 10px;
}
.pkg-card {
    background: #fff; border: 1px solid #E5E7EB; border-radius: 14px;
    padding: 26px 24px; position: relative; display: flex; flex-direction: column;
    transition: box-shadow 0.15s, border-color 0.15s;
}
.pkg-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.pkg-card.featured { border-color: #2563EB; box-shadow: 0 4px 16px rgba(37,99,235,.10); }
.pkg-badge {
    position: absolute; top: -11px; right: 20px; background: #2563EB; color: #fff;
    font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px;
    text-transform: uppercase; letter-spacing: .04em;
}
.pkg-badge.current { background: #059669; }
.pkg-name { font-size: 17px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.pkg-desc { font-size: 13px; color: #6B7280; margin: 0 0 16px; min-height: 18px; }
.pkg-price { font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 2px; }
.pkg-price span { font-size: 13px; font-weight: 500; color: #6B7280; }
.pkg-duration { font-size: 12px; color: #9CA3AF; margin-bottom: 18px; text-transform: capitalize; }
.pkg-features { list-style: none; margin: 0 0 22px; padding: 0; flex-grow: 1; }
.pkg-features li {
    font-size: 13px; color: #374151; padding: 7px 0; display: flex; align-items: center; gap: 8px;
    border-bottom: 1px dashed #F3F4F6;
}
.pkg-features li:last-child { border-bottom: none; }
.pkg-features li i { color: #059669; font-size: 12px; width: 14px; }
.pkg-features li.disabled { color: #9CA3AF; }
.pkg-features li.disabled i { color: #D1D5DB; }
.pkg-buy-btn {
    border: none; background: #2563EB; color: #fff; border-radius: 8px;
    padding: 11px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
    text-align: center; text-decoration: none; display: block; transition: background 0.15s;
}
.pkg-buy-btn:hover { background: #1D4ED8; color: #fff; }
.pkg-buy-btn.current { background: #F3F4F6; color: #6B7280; cursor: default; pointer-events: none; }
.pkg-empty {
    text-align: center; padding: 60px 20px; color: #6B7280; font-size: 14px;
}
.org-alert {
    border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
    font-size: 14px; display: flex; align-items: flex-start; gap: 10px;
}
.org-alert.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.org-alert.danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.org-alert.info    { background: #EFF6FF; color: #1E3A8A; border: 1px solid #BFDBFE; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<!-- ── Alerts ──────────────────────────────────────────── -->
<?php if (session()->has('success_message')) : ?>
    <div class="org-alert success">
        <i class="fa-solid fa-circle-check"></i>
        <div><?= session('success_message') ?><?php session()->remove('success_message') ?></div>
    </div>
<?php endif ?>
<?php if (session()->has('error')) : ?>
    <div class="org-alert danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div><?= session('error') ?><?php session()->remove('error') ?></div>
    </div>
<?php endif ?>

<!-- ── Current membership summary (if any) ─────────────── -->
<?php if (!empty($active)) : ?>
    <?php $cur_pkg = $active['package']; $cur_mem = $active['membership']; ?>
    <div class="org-alert info">
        <i class="fa-solid fa-circle-info"></i>
        <div>
            You currently have the <strong><?= esc($cur_pkg->package_name) ?></strong> package —
            <?php if (!empty($cur_mem->membership_end_date)) : ?>
                valid until <strong><?= date('d M Y', strtotime($cur_mem->membership_end_date)) ?></strong>.
            <?php else : ?>
                <strong>lifetime access</strong>.
            <?php endif ?>
        </div>
    </div>
<?php endif ?>

<!-- ── Package grid ──────────────────────────────────────── -->
<?php if (empty($packages)) : ?>
    <div class="pkg-empty">
        <i class="fa-solid fa-box-open" style="font-size:32px;color:#D1D5DB;margin-bottom:10px;display:block;"></i>
        No packages are available right now. Please check back later.
    </div>
<?php else : ?>
    <div class="pkg-grid">
        <?php foreach ($packages as $pkg) :
            $is_current = !empty($active) && $active['package']->package_id == $pkg->package_id;
        ?>
        <div class="pkg-card <?= $pkg->package_is_featured ? 'featured' : '' ?>">
            <?php if ($is_current) : ?>
                <span class="pkg-badge current">Current Plan</span>
            <?php elseif ($pkg->package_is_featured) : ?>
                <span class="pkg-badge">Popular</span>
            <?php endif ?>

            <h6 class="pkg-name"><?= esc($pkg->package_name) ?></h6>
            <p class="pkg-desc"><?= esc($pkg->package_description ?? '') ?></p>

            <div class="pkg-price">
                <?= $pkg->package_price > 0 ? '₹' . number_format($pkg->package_price, 2) : 'Free' ?>
                <?php if ($pkg->package_price > 0 && $pkg->package_duration_type !== 'lifetime') : ?>
                    <span>/ <?= esc($pkg->package_duration_type) ?></span>
                <?php endif ?>
            </div>
            <div class="pkg-duration">
                <?= $pkg->package_duration_type === 'lifetime' ? 'Lifetime access' : esc($pkg->package_duration_type) . ' plan' ?>
            </div>

            <ul class="pkg-features">
                <li class="<?= $pkg->package_can_create_tournament ? '' : 'disabled' ?>">
                    <i class="fa-solid <?= $pkg->package_can_create_tournament ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    <?= $pkg->package_can_create_tournament ? 'Create tournaments' : 'Tournament creation not included' ?>
                </li>
                <li>
                    <i class="fa-solid fa-circle-check"></i>
                    <?= $pkg->package_max_tournaments > 0 ? $pkg->package_max_tournaments . ' tournament(s)' : 'Unlimited tournaments' ?>
                </li>
                <li>
                    <i class="fa-solid fa-circle-check"></i>
                    <?= $pkg->package_max_matches > 0 ? $pkg->package_max_matches . ' matches' : 'Unlimited matches' ?>
                </li>
                <li>
                    <i class="fa-solid fa-circle-check"></i>
                    <?= $pkg->package_max_teams > 0 ? $pkg->package_max_teams . ' teams' : 'Unlimited teams' ?>
                </li>
                <li class="<?= $pkg->package_priority_booking ? '' : 'disabled' ?>">
                    <i class="fa-solid <?= $pkg->package_priority_booking ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    Priority booking
                </li>
            </ul>

            <?php if ($is_current) : ?>
                <span class="pkg-buy-btn current">Currently Active</span>
            <?php else : ?>
                <!--<a href="<?= base_url('organizer/buy_package/' . $pkg->package_id) ?>"-->
                <!--   class="pkg-buy-btn"-->
                <!--   onclick="return confirm('Purchase the <?= esc($pkg->package_name) ?> package?');">-->
                <!--    <?= $pkg->package_price > 0 ? 'Purchase Package' : 'Activate Free Plan' ?>-->
                <!--</a>-->
                
                <a href="<?= base_url('organizer/razorpay/checkout/' . $pkg->package_id) ?>"
                   class="pkg-buy-btn">
                    <?= $pkg->package_price > 0 ? 'Purchase Package' : 'Activate Free Plan' ?>
                </a>
            <?php endif ?>
        </div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?= $this->endSection(); ?>