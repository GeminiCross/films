<div class="col-7">
    <form action="" method="post">
        <label for="name">Name</label>
        <p><input type="text" class="form-control" required name="name" id="name" value="<?=@$this->form_data['name']?>"></p>
        <label for="year">Year</label>
        <p><input type="text" class="form-control" required pattern="[0-9]{4}" name="year" id="year" value="<?=@$this->form_data['year']?>"></p>
        <label for="actors">Actors</label><br>
        Перечислите актёров, снявшихся в этом фильме(разделяя их запятой и пробелом: Will Smith, Johnny Depp)
        <p><textarea name="actors" class="form-control" id="actor_list" cols="10" rows="5"><?=@$this->form_data['actors']?></textarea></p>
        <label for="format">Format</label>
        <p><select name="format" class="form-control col-2" id="format">
                <?php foreach ($formats as $format) { ?>
                        <option value="<?=$format?>" id="<?=$format?>"><?=$format?></option>
                <?php } ?>
            </select></p>
        <input type="submit" class="form-control" value="Сохранить">
    </form>
    <p><h3>Загрузить фильм(ы) из файла</h3></p>
    <form enctype="multipart/form-data" action="/film-create" method="POST" >
        <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
        <input type="hidden" class="form-control" name="MAX_FILE_SIZE" value="30000" />
        <!-- Название элемента input определяет имя в массиве $_FILES -->
        <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" name="films" class="custom-file-input" id="inputGroupFile02">
                <label class="custom-file-label" for="inputGroupFile02">Выберите файл</label>
            </div>
            <div class="input-group-append">
                <input type="submit" class="form-control" value="Отправить файл" />
            </div>
        </div>
    </form>
</div>