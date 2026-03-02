<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <?php if($row['status'] == 'Pending'): ?>
                                        <form action="<?= base_url('admin/transactions/approve') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                        </form>
                                        <form action="<?= base_url('admin/transactions/reject') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        </form>

                                    <?php elseif($row['status'] == 'Approved'): ?>
                                        <button class="btn btn-sm btn-info text-white handover-btn" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['collection_title']) ?>">Handover</button>

                                    <?php elseif($row['status'] == 'Renewing'): ?>
                                        <button class="btn btn-sm btn-warning fw-bold admin-renew-btn" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['collection_title']) ?>">Renew</button>
                                        <form action="<?= base_url('admin/transactions/reject') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                        </form>

                                    <?php elseif($row['status'] == 'Borrowed'): ?>
                                        <form action="<?= base_url('admin/transactions/processReturn') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm" title="Mark as Returned"><i class="bi bi-check-circle"></i> Return</button>
                                        </form>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-outline-danger btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <form action="<?= base_url('admin/transactions/reportIssue') ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="issue_type" value="Lost">
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-question-circle me-2"></i>Mark as Lost</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?= base_url('admin/transactions/reportIssue') ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="issue_type" value="Damaged">
                                                        <button type="submit" class="dropdown-item text-warning"><i class="bi bi-x-octagon me-2"></i>Mark as Damaged</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                        <form action="<?= base_url('admin/transactions/sendManualReminder') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Send Email Reminder"><i class="bi bi-bell-fill"></i></button>
                                        </form>

                                    <?php else: ?>
                                        <span class="text-muted small">---</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>