<?php
use Films\Modules\View;
?>
<nav aria-label="...">
    <ul class="pagination">
        <?php
        if ($current_page - 1> $max_left) { ?>
            <li class="page-item">
                <a class="page-link" href="<?=View::linkToPage(1)?>">First</a>
            </li>
        <?php }
        if ($current_page == 1) { ?>
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
            </li>
        <?php }else{ ?>
            <li class="page-item">
                <a class="page-link" href="<?=View::linkToPage($current_page - 1)?>">Previous</a>
            </li>
        <?php } for($i = $first_page; $i <= $last_page; $i++) { if($i == $current_page) { ?>
        <li class="page-item active"><span class="page-link" href="#"><?=$i?></span></li>
        <?php } else { ?>
        <li class="page-item"><a href="<?=View::linkToPage($i)?>" class="page-link"><?=$i?></a></li>
        <?php } } if($current_page == $total_pages) { ?>
        <li class="page-item disabled">
            <span class="page-link">Next</span>
        </li>
        <?php }else{ ?>
        <li class="page-item">
            <a class="page-link" href="<?=View::linkToPage($current_page + 1)?>">Next</a>
        </li>
        <?php } ?>
        <?php if ($current_page < $total_pages - $max_right) { ?>
            <li class="page-item">
                <a class="page-link" href="<?=View::linkToPage($total_pages)?>">Last</a>
            </li>
        <?php } ?>
    </ul>
</nav>