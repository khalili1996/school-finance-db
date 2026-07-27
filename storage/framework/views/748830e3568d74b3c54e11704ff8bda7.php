<?php switch($status):
    case ('present'): ?> <span class="badge bg-success">حاضر</span> <?php break; ?>
    <?php case ('blocked'): ?> <span class="badge bg-danger">محروم</span> <?php break; ?>
    <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
    <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
    <?php default: ?> <span class="badge bg-secondary"><?php echo e($status); ?></span>
<?php endswitch; ?>
<?php /**PATH E:\school_finance_db\resources\views\admin\partials\student-status.blade.php ENDPATH**/ ?>