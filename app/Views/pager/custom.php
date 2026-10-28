<?php if ($pager->hasPrevious()) : ?>
    <a href="<?= $pager->getPreviousPage() ?>" class="custom-pager btn btn-primary">
        &laquo; <?= lang('Pager.previous') ?>
    </a>
<?php endif ?>

<?php foreach ($pager->links() as $link) : ?>
    <a href="<?= $link['uri'] ?>" class="custom-pager btn <?= $link['active'] ? 'btn-dark' : 'btn-outline-secondary' ?>">
        <?= $link['title'] ?>
    </a>
<?php endforeach ?>

<?php if ($pager->hasNext()) : ?>
    <a href="<?= $pager->getNextPage() ?>" class="custom-pager btn btn-danger">
        <?= lang('Pager.next') ?> &raquo;
    </a>
<?php endif ?>
