<?php

use Films\Modules\View;

?>
<dl class="row">
    <dt class="col-sm-3">Name</dt>
    <dd class="col-sm-9"><?= $film->name ?></dd>

    <dt class="col-sm-3">Year</dt>
    <dd class="col-sm-9"><?= $film->year ?></dd>

    <dt class="col-sm-3">Format</dt>
    <dd class="col-sm-9"><?= $film->format ?></dd>
</dl>

    <?php if (count($film->actor_list) > 0) { ?>
        <p>Actors:</p>
        <ul class="list-group row">
            <?php foreach ($film->actor_list as $actor) { ?>
                <li class="list-group-item col-4"><a href="<?= View::makeUrl(['controller' => 'film', 'action' => 'search'], ['by' => 'actor', 'name' => $actor['actor_name']]) ?>"><?=$actor['actor_name']?></a></li>
            <?php } ?>
        </ul>
        <br>
        <?php }  ?>

<a class="btn btn-outline-danger" href="<?= View::makeUrl(['controller' => 'film', 'action' => 'delete', 'id' => $film->id]) ?>">Delete</a>