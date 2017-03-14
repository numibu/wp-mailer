<?php

$realtor_page = menu_page_url( 'realtor', 0);
$setting_page = menu_page_url( 'setting', 0);

?>
<br>
<a class='button' title="Настроить" href="<?php echo $setting_page ?>">Настроить (админ)</a>
<span>   </span>
<a class='button button-cancel' title="Добавить" href="<?php echo $realtor_page ?>">Добавить задание</a>
<br>