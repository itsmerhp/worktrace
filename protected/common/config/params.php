<?php

return [
//------------------------//
// SYSTEM SETTINGS
//------------------------//

    /**
     * Registration Needs Activation.
     *
     * If set to true users will have to activate their accounts using email account activation.
     */
    'rna' => false,
    /**
     * Login With Email.
     *
     * If set to true users will have to login using email/password combo.
     */
    'lwe' => false,
    /**
     * Force Strong Password.
     *
     * If set to true users will have to use passwords with strength determined by StrengthValidator.
     */
    'fsp' => false,
    /**
     * Set the password reset token expiration time.
     */
    'user.passwordResetTokenExpire' => 3600,
//------------------------//
// EMAILS
//------------------------//

    /**
     * Email used in contact form.
     * Users will send you emails to this address.
     */
    'siteName' => 'WorkTrace',
    'siteShortName' => 'WT',
    'adminEmail' => 'worktrace1@gmail.com',
    'logo_url' => '',
    /**
     * Not used in template.
     * You can set support email here.
     */
    'supportEmail' => 'worktrace1@gmail.com',
    'SITE_OWNER_NAME' => 'Heidi',
    'SITE_OWNER_EMAIL' => 'worktrace1@gmail.com',
    'DOCUMENT_ROOT' => $_SERVER["DOCUMENT_ROOT"] . "/worktrace/",
    'uploads_path' => dirname(__FILE__) . '/../../../uploads/',
    'upload_path' => 'uploads/',
    //For pagination. 20 records per page
    'records_per_page' => 10,
    //User roles
    'USER_ROLES' => ['superadmin' => 1, 'owner' => 2, 'manager' => 3, 'employee' => 4],
    'DEVICE_TYPE' => ['android' => 1, 'ios' => 2],
    //For status dropdown.
    'STATUS_SELECT' => [0 => 'Inactive', 1 => 'Active'],
    //Email template ids of email_format table.
    'EMAIL_TEMPLATE_ID' => ['forgot_password' => 1, 'welcome' => 2, 'restaurant_otp' => 4, 'restaurant_registered' => 5],
    //GENDER
    'GENDER' => [1 => 'Male', 2 => 'Female', 3 => 'Other'],
    'MESSAGE_COLOR' => ['success' => '#39C178', 'error' => '#FC6666', 'other' => '#444444']
];
