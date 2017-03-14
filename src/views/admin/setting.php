<?php

use mailer\models\UserRoleAsMailer as RAM;
use mailer\models\UserRoleAsAddressee as RAA;
use mailer\models\PostTypeToMailer as PTM;

$admin_setting = menu_page_url( 'admin-settings', 0);
?>

<?php
$roles = filter_input(INPUT_POST, 'roles', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
if ( $roles ){ RAM::saveAllMailerRoles($roles); }

$rolesAddressee = filter_input(INPUT_POST, 'rolesAdrressee', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
if ( $rolesAddressee ){ RAA::saveAllAddresseRoles($rolesAddressee); }

$postType = filter_input(INPUT_POST, 'postType', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
if ( $postType ){ PTM::saveAllPostTypes($postType); }


echo \mailer\models\UserRoleAsMailer::getFormWithRole();

echo '<br>';

echo \mailer\models\UserRoleAsAddressee::getFormWithRole();

echo '<br>';

echo \mailer\models\PostTypeToMailer::getFormWithPostType();

echo '<br>';
