<?php 
$url = admin_url('admin-ajax.php');
$admin_setting = menu_page_url( 'admin-settings', 0);
ini_set("display_errors",1);
error_reporting(E_ALL);
?>
<script>
window.admin_ajax_url = '<?php echo $url; ?>';
</script>

<div class="tabs">
    <input class="tabs-input" id="tab1" type="radio" name="tabs" checked>
    <label for="tab1" title="Content list">Публикации</label>
 
    <input class="tabs-input" id="tab2" type="radio" name="tabs">
    <label for="tab2" title="Addressee list">Список адресатов</label>
 
    <input class="tabs-input" id="tab3" type="radio" name="tabs">
    <label for="tab3" title="Mailer list"><span class="allTask">(<?php echo \mailer\models\TaskGrid::countTask(); ?>)</span>Заданий к исполнению</label>
 
    <section id="content1">
        <p>
            <?php echo \mailer\models\ContentToMailer::getHTMLContent2() ; ?>
        </p>
    </section>  
    <section id="content2">
        
            <?php echo \mailer\models\AddresseeList::getHTMLContent() ; ?>
            <?php echo \mailer\models\AddresseeList::getHTMLFormAddCustomAddressee() ; ?>
        
    </section> 
    <section id="content3">
        <p>
            <?php echo 
            '<!--<span class ="sendAll" onclick="sendAllTask">Отправить все </span>'
            . '<span class ="deleteAll onclick="deleteAllTask""> Удалить все</span>-->' ;
            ?>
        </p>
        <?php echo \mailer\models\TaskGrid::createTaskGrid(); ?>
    </section>   
</div>