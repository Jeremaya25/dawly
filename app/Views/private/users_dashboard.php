<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('style')?>
    <style>
        .my-user {
            background-color: lightyellow;
        }
    </style>
<?php $this->endSection()?>

<?php $this->section('content')?>
    <table id="users_table" class="table w-50 m-auto">
        <thead>
            <tr>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Groups</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr <?php if($user['id'] == user_id()) echo "class='my-user'"?>>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td>
                        <?php foreach ($userModel->getUserGroups($user['id']) as $group): ?>
                            <span class="badge badge-primary" style="background-color: blue"><?= $group['name'] ?></span>
                        <?php endforeach ?>
                    </td>
                    <td>
                        <?php if ($user['id'] != user_id()): ?>
                            <?php if($user['active']): ?>
                                <a href="<?= base_url("/private/deactivate-user/{$user['id']}") ?>">
                                    <button class="btn btn-warning">Disable</button>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url("/private/activate-user/{$user['id']}") ?>">
                                    <button class="btn btn-success">Enable</button>
                                </a>
                            <?php endif ?>
                            <a href="<?= base_url("/private/delete-user/{$user['id']}") ?>">
                                <button class="btn btn-danger">Delete</button>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php $this->endSection() ?>