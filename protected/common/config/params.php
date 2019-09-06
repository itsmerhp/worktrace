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
    'rna'                           => false,
    /**
     * Login With Email.
     *
     * If set to true users will have to login using email/password combo.
     */
    'lwe'                           => false,
    /**
     * Force Strong Password.
     *
     * If set to true users will have to use passwords with strength determined by StrengthValidator.
     */
    'fsp'                           => false,
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
    'siteName'     => 'Gr8Dish', 
	'siteShortName'     => 'GD', 
    'adminEmail'   => 'gr8dish800@gmail.com',
    'logo_url'     => '',
    /**
     * Not used in template.
     * You can set support email here.
     */
    'supportEmail'        => 'gr8dish800@gmail.com',
	'SITE_OWNER_NAME'        => 'Jeramy',
	'SITE_OWNER_EMAIL'        => 'gr8dish800@gmail.com',
	'DOCUMENT_ROOT'		  =>	$_SERVER["DOCUMENT_ROOT"]."/",
    'uploads_path'        => dirname(__FILE__).'/../../../uploads/',
    //Find restaurants in the radios of 50 miles
    'nearByRestaurant'    => 5000,
    'nearByHomeFeed'      => 4500,
    'upload_path'         => 'uploads/',
    //For pagination. 20 records per page
    'records_per_page'    => 10,
    //User roles
    'USER_ROLES'          => ['admin'=>1,'app_user'=>2],
	'USER_TYPE'          => ['user'=>1,'restaurant'=>2],
	'USER_TYPE_VALUES'          => [1 =>'Normal User',2=>'Restaurant User'],
    'DEVICE_TYPE'          => ['android'=>1,'ios'=>2],
    //For status dropdown.
    'STATUS_SELECT'       => [0=>'Inactive',1=>'Active'],
    //Email template ids of email_format table.
    'EMAIL_TEMPLATE_ID'   => ['forgot_password'=>1,'welcome'=>2,'restaurant_otp'=>4,'restaurant_registered'=>5],
    //GENDER
    'GENDER'             => [1=>'Male',2=>'Female',3=>'Other'],
	'MESSAGE_COLOR'      => ['success' => '#39C178','error' => '#FC6666','other' => '#444444'],
	'AWS_KEY'			=>	'AKIAIEKLQCVD2SZSQRNQ',
	'AWS_SECRET'			=>	'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',
	'AWS_BUCKET'			=>	'gr8dish',
	'FEED_TYPE'          => ['ranked'=>1,'pull_single'=>2],
	'LOCATION_TYPE'          => ['local'=>1,'national'=>2],
	'LOCAL_DISTANCE'          => 35,
	'FCM_TOKEN' 		=>	'AAAAzuF-mM8:APA91bEovsnXsSaAPzQSyWOfgtb2CMR_FO231ikJ0epI1cux9UXL6DC7GOk-vw80RaOnjF6hfIzX-KLxm37zgJvQJdIs9qWyf96Nf6h5C3tCPbWzS-UKb8UctZqJyHnR84Jkk6ImoRiX',
];
