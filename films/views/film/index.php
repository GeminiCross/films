<?php

use Films\Modules\View;
if (isset($films)) {
?>
<div class="container">
    <div class="row">
        <?php foreach ($films as $film) { ?>
            <div class="col-4">
                <div class="card col-12 mx-auto" style="width: 22rem;">
                    <div class="card-body">
                        <h5 class="card-title"><?=$film->name?></h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Год выхода: <?=$film->year?></li>
                        <li class="list-group-item">Формат: <?=$film->year?></li>
                    </ul>
                    <div class="card-body">
                        <a href="<?= View::makeUrl(['controller' => 'film', 'action' => 'view', 'id' => $film->id]) ?>" class="card-link">Подробнее</a>
                        <a href="<?= View::makeUrl(['controller' => 'film', 'action' => 'delete', 'id' => $film->id]) ?>" class="card-link">Удалить</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="navbar-fixed-bottom row-fluid footer">
    <div class="navbar-inner">
        <div class="container">
            <?=$this->pagination?>
        </div>
    </div>
</div>
<?php } ?>