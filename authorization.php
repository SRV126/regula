<?php

require_once 'phpgen_settings.php';
require_once 'components/application.php';
require_once 'components/security/permission_set.php';
require_once 'components/security/user_authentication/table_based_user_authentication.php';
require_once 'components/security/grant_manager/user_grant_manager.php';
require_once 'components/security/grant_manager/composite_grant_manager.php';
require_once 'components/security/grant_manager/hard_coded_user_grant_manager.php';
require_once 'components/security/grant_manager/table_based_user_grant_manager.php';
require_once 'components/security/table_based_user_manager.php';

include_once 'components/security/user_identity_storage/user_identity_session_storage.php';

require_once 'database_engine/mysql_engine.php';

$grants = array('guest' => 
        array('attendance2' => new PermissionSet(false, false, false, false),
        'shortage' => new PermissionSet(false, false, false, false),
        'students' => new PermissionSet(false, false, false, false),
        'units' => new PermissionSet(false, false, false, false),
        'branch' => new PermissionSet(false, false, false, false),
        'logview' => new PermissionSet(false, false, false, false),
        'membership_userrecords' => new PermissionSet(false, false, false, false))
    ,
    'defaultUser' => 
        array('attendance2' => new PermissionSet(true, false, false, false),
        'shortage' => new PermissionSet(true, false, false, false),
        'students' => new PermissionSet(true, false, false, false),
        'units' => new PermissionSet(true, false, false, false),
        'branch' => new PermissionSet(true, false, false, false),
        'logview' => new PermissionSet(false, false, false, false),
        'membership_userrecords' => new PermissionSet(true, false, false, false))
    ,
    'saurav' => 
        array('attendance2' => new PermissionSet(false, false, false, false),
        'shortage' => new PermissionSet(false, false, false, false),
        'students' => new PermissionSet(false, false, false, false),
        'units' => new PermissionSet(false, false, false, false),
        'branch' => new PermissionSet(false, false, false, false),
        'logview' => new AdminPermissionSet(),
        'membership_userrecords' => new PermissionSet(false, false, false, false))
    ,
    'faculty' => 
        array('attendance2' => new PermissionSet(false, false, false, false),
        'shortage' => new PermissionSet(false, false, false, false),
        'students' => new PermissionSet(false, false, false, false),
        'units' => new PermissionSet(false, false, false, false),
        'branch' => new PermissionSet(false, false, false, false),
        'logview' => new AdminPermissionSet(),
        'membership_userrecords' => new PermissionSet(false, false, false, false))
    ,
    'abhijeet' => 
        array('attendance2' => new PermissionSet(false, false, false, false),
        'shortage' => new PermissionSet(false, false, false, false),
        'students' => new PermissionSet(false, false, false, false),
        'units' => new PermissionSet(false, false, false, false),
        'branch' => new PermissionSet(false, false, false, false),
        'logview' => new AdminPermissionSet(),
        'membership_userrecords' => new PermissionSet(false, false, false, false))
    ,
    'unnati' => 
        array('attendance2' => new PermissionSet(false, false, false, false),
        'shortage' => new PermissionSet(false, false, false, false),
        'students' => new PermissionSet(false, false, false, false),
        'units' => new PermissionSet(false, false, false, false),
        'branch' => new PermissionSet(false, false, false, false),
        'logview' => new AdminPermissionSet(),
        'membership_userrecords' => new PermissionSet(false, false, false, false))
    );

$appGrants = array('guest' => new PermissionSet(false, false, false, false),
    'defaultUser' => new PermissionSet(false, false, false, false),
    'saurav' => new AdminPermissionSet(),
    'faculty' => new AdminPermissionSet(),
    'abhijeet' => new AdminPermissionSet(),
    'unnati' => new AdminPermissionSet());

$dataSourceRecordPermissions = array();

$tableCaptions = array('attendance2' => 'Attendance',
'shortage' => 'Shortage',
'students' => 'Students',
'units' => 'Subjects',
'branch' => 'Branch',
'logview' => 'Log',
'membership_userrecords' => 'About');

$usersTableInfo = array(
    'TableName' => 'membership_users',
    'UserId' => 'memberID',
    'UserName' => 'passMD5',
    'Password' => 'passMD5',
    'Email' => '',
    'UserToken' => '',
    'UserStatus' => ''
);

function EncryptPassword($password, &$result)
{

}

function VerifyPassword($enteredPassword, $encryptedPassword, &$result)
{

}

function BeforeUserRegistration($username, $email, $password, &$allowRegistration, &$errorMessage)
{

}    

function AfterUserRegistration($username, $email)
{

}    

function PasswordResetRequest($username, $email)
{

}

function PasswordResetComplete($username, $email)
{

}

function CreatePasswordHasher()
{
    $hasher = CreateHasher('');
    if ($hasher instanceof CustomStringHasher) {
        $hasher->OnEncryptPassword->AddListener('EncryptPassword');
        $hasher->OnVerifyPassword->AddListener('VerifyPassword');
    }
    return $hasher;
}

function CreateTableBasedGrantManager()
{
    return null;
}

function CreateTableBasedUserManager() {
    global $usersTableInfo;
    return new TableBasedUserManager(MySqlIConnectionFactory::getInstance(), GetGlobalConnectionOptions(), $usersTableInfo, CreatePasswordHasher(), false);
}

function SetUpUserAuthorization()
{
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;

    $hasher = CreatePasswordHasher();

    $hardCodedGrantManager = new HardCodedUserGrantManager($grants, $appGrants);
    $tableBasedGrantManager = CreateTableBasedGrantManager();
    $grantManager = new CompositeGrantManager();
    $grantManager->AddGrantManager($hardCodedGrantManager);
    if (!is_null($tableBasedGrantManager)) {
        $grantManager->AddGrantManager($tableBasedGrantManager);
    }

    $userAuthentication = new TableBasedUserAuthentication(new UserIdentitySessionStorage(), true, $hasher, CreateTableBasedUserManager(), false, false, false);

    GetApplication()->SetUserAuthentication($userAuthentication);
    GetApplication()->SetUserGrantManager($grantManager);
    GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}
