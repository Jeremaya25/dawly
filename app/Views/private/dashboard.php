<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('style') ?>
    <style>
        .my-url {
            background-color: lightyellow;
        }
        .anonymous-url {
            background-color: lightgrey;
        }
    </style>
<?php $this->endSection() ?>

<?php $this->section('content') ?>
    <table id="dashboard-table" class="table w-75 m-auto">
        <thead>
            <tr>
                <?php if (has_permission('urls.manage')): ?>
                    <th scope="col">User</th>
                <?php endif ?>
                <th scope="col">URL</th>
                <th scope="col">Shortname</th>
                <th scope="col">Description</th>
                <th scope="col">Visits</th>
                <?php if (has_permission('urls.manage')): ?>
                    <th scope="col">Active</th>
                <?php endif ?>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($urls as $url): ?>
                <tr <?php if (!isset($url['user_id'])) echo "class='anonymous-url'";
                    else if ($url['user_id'] == user_id()) echo "class='my-url'";?>
                >
                    <?php if (isset($url['user_id']) && has_permission("urls.manage")): ?>
                        <td><?= $userModel->getUserById($url['user_id'])['username'] ?></td>
                    <?php elseif (has_permission("urls.manage")) : ?>
                        <td>Anonymous</td>
                    <?php endif ?>
                    <td><?= $url['url'] ?></td>
                    <td><?= $url['shortname'] ?></td>
                    <?php if ($url['description'] != null): ?>
                        <td>Yes</td>
                    <?php else: ?>
                        <td>No</td>
                    <?php endif ?>
                    <td><?= $visitModel->countVisitsByRoute($url['route']) ?></td>
                    <?php if (has_permission('urls.manage')): ?>
                        <td>
                            <?php if ($url['active']): ?>
                                <a href="<?= base_url("/private/deactivate-url/{$url['route']}") ?>">
                                    <button class="btn btn-warning">Disable</button>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url("/private/activate-url/{$url['route']}") ?>">
                                    <button class="btn btn-success">Enable</button>
                                </a>
                            <?php endif ?>
                        </td>
                    <?php endif ?>
                    <td>
                        <button class="btn btn-primary" onclick='window.open("<?=base_url($url["route"])?>", "_blank");' <?php if(!$url['active']) echo "disabled='disabled'"?>>Go</button>
                        
                        <a href="<?= base_url("/private/delete-url/{$url['route']}") ?>">
                            <button class="btn btn-danger">Delete</button>
                        </a>
                    </td>
                    <td>
                        <!-- Statistics -->
                        <a href="<?= base_url("/private/statistics/{$url['route']}") ?>">
                            <button class="btn btn-info">Statistics</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php $this->endSection() ?>