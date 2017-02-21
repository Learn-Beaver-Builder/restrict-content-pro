<?php
/**
 * Help Menus
 *
 * @package     Restrict Content Pro
 * @subpackage  Admin/Help Menus
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( get_bloginfo('version') < 3.3 ) {
	// use old help tab format for WP version less than 3.3
	include('help-menus-setup-old.php');
} else {
	// use the new, better format
	include('help-menus-setup.php');
}

/**
 * Render members tab content
 *
 * @param string $id
 *
 * @return string
 */
function rcp_render_members_tab_content( $id ) {
	switch( $id ) :

		case 'general' :
			ob_start(); ?>
			<p><?php _e( 'This page displays an overview of the registered members on your site, as well as a list of all users, sorted by subscription status.', 'rcp' ); ?></p>
			<p><?php _e( 'By default, only "active" users are shown in the list, but you can choose to show other statuses by simply clicking on the status name, just above the users table.', 'rcp' ); ?></p>
			<p><?php _e( 'On this page, you can perform a variety of tasks, including:', 'rcp' ); ?></p>
			<ul>
				<li><?php _e( 'View subscription details of any registered user', 'rcp' ); ?></li>
				<li><?php _e( 'Edit the subscription details of any registered user', 'rcp' ); ?></li>
				<li><?php _e( 'Activate / Deactivate the subscription of any registered user', 'rcp' ); ?></li>
				<li><?php _e( 'Add new subscriptions to pre-existing users', 'rcp' ); ?></li>
			</ul>
			<p><?php printf( __( 'You can also enter a username or email address in the search box to locate a specific member. To search by first or last name, prefix your search with one of the following: %s, %s', 'rcp' ), '<code>first_name:</code>', '<code>last_name:</code>' ); ?></p>
			<?php
			break;
		case 'adding_subs' :
			ob_start(); ?>
			<p><?php _e( 'Adding a premium subscription to an existing user (meaning the user exists in the WordPress database) is easy. At the bottom of the screen, simply choose the user you wish to add a subscription for, choose the subscription level to grant them, choose an expiration date, and click <em>Add User Subscription</em>.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Note</strong>: when you add a subscription to a user manually, you <em>cannot</em> charge that user for the subscription. This simply allows you to grant access to premium content for special cases, such as when you have given a membership away as a competition prize, or a user has paid with some alternate method.', 'rcp' ); ?></p>

			<p><?php _e( 'Also note, you can also add / modify a user\'s subscription from the regular WordPress Users page. At the right side of each user entry will be links to Add / Edit Subscriptions.', 'rcp' ); ?></p>
			<?php
			break;
		case 'member_details' :
			ob_start(); ?>
			<p><?php _e( 'The Details page for a member, shows information about that user\'s subscription, including:', 'rcp' ); ?></p>
			<ul>
				<li><?php _e( 'The Status of the subscription, either Active, Pending, Expired, or Cancelled', 'rcp' ); ?></li>
				<li><?php _e( 'The subscription level the user is signed up with', 'rcp' ); ?></li>
				<li><?php _e( 'The expiration date for the user\'s subscription', 'rcp' ); ?></li>
				<li><?php _e( 'The Subscription Key, which is the unique identifier key generated when the user signs up', 'rcp' ); ?></li>
				<li><?php _e( 'A list of all (if any) discount codes the member has ever used, both when signing up for the first time and adding another subscription', 'rcp' ); ?></li>
				<li><?php _e( 'A list of all payments that have been made to you from this member', 'rcp' ); ?></li>
			</ul>
			<?php
			break;
		case 'editing_member' :
			ob_start(); ?>
			<p><?php _e( 'The Edit Member page allows administrators to modify details of a member\'s subscription. The details that can be changed are:', 'rcp' ); ?></p>
			<ul>
				<li><?php _e( 'Status - sets the state of the member\'s subscription. Only <em>Active</em> members can view premium content', 'rcp' ); ?></li>
				<li><?php _e( 'Subscription Level - sets the subscription package the member is assigned to. This does not affect the member\'s access level', 'rcp' ); ?></li>
				<li><?php _e( 'Expiration Date - this is the date the member\'s subscription will expire. When a member\'s expiration date is reach, their subscription status will be automatically changed to Expired', 'rcp' ); ?></li>
			</ul>
			<?php
			break;

		default;
			break;

	endswitch;

	return ob_get_clean();
}

/**
 * Render subscriptions tab content
 *
 * @param string $id
 *
 * @return string
 */
function rcp_render_subscriptions_tab_content( $id ) {
	switch( $id ) :

		case 'general' :
			ob_start(); ?>
			<p><?php _e( 'Subscription levels allow you to setup different membership packages. For example, you could have one package that grants members access to your premium content for one month, and another that grants users access for an entire year. There is no limit to the number of packages you can create. You can also create "Trial" packages; these grant users premium access for a limited period of time, and can be completely free.', 'rcp' ); ?></p>
			<p><?php _e( 'This page will show you an overview of all the subscription packages you have created on your site. It will also show a variety of details for each package, including the total number of Active subscribers for each level.', 'rcp' ); ?></p>
			<?php
			break;
		case 'adding_subscriptions' :
			ob_start(); ?>
			<p><?php _e( 'Adding new subscription levels is very simple. First, enter the name you want to give the membership package. This name is displayed on the registration form. Second, give your membership package a description. This is also shown on the registration form.', 'rcp' ); ?></p>
			<p><?php _e( 'Next you need to choose the duration for your subscription package. There are several of options for this:', 'rcp' ); ?></p>
			<ol>
				<li><?php _e( 'If you are creating a free, unlimited registration, enter "0" here. This will cause users who register with this package to have no expiration date.', 'rcp' ); ?></li>
				<li><?php _e( 'If you are creating a trial subscription, which will grant users access to premium content for a limited amount of time for free, then choose the length of time you wish the trial to last.', 'rcp' ); ?></li>
				<li><?php _e( 'If you are creating a regular, paid subscription, then simply enter the duration for the subscription.', 'rcp' ); ?></li>
			</ol>
			<p><?php _e( 'Once you have entered a number for the duration, ensure you also choose the correct time unit for the package. This is either <em>Day(s)</em>, <em>Month(s)</em>, or <em>Year(s)</em>.', 'rcp' ); ?></p>
			<p><?php _e( 'Next, enter the price for this subscription. The price will be the amount paid for the duration chosen above. So, for example, if you entered 3 Months above, then this would be the price for 3 months of access to the premium content.', 'rcp' ); ?></p>
			<p><?php _e( 'If you want a free or trial membership, simply enter "0", or choose "Free" from the drop down.', 'rcp' ); ?></p>
			<?php
			break;
		case 'editing_subscriptions' :
			ob_start(); ?>
			<p><?php _e( 'After you have created a subscription, you may edit it at anytime. Making changes to a subscription will have no effect on current subscribers to that subscription, even if you change the price of the package.', 'rcp' ); ?></p>
			<p><?php _e( 'To edit a subscription, click "Edit" on the right side of the screen for the subscription you wish to modify. You will be presented with an edit form to change any and all details of the package. Simply make the changes you need and click "Update Subscription".', 'rcp' ); ?></p>
			<?php
			break;
		case 'deleting_subscriptions' :
			ob_start(); ?>
			<p><?php _e( 'If at anytime you wish to remove a subscription, you may do so by clicked "Delete" on the right side of the screen, from the Subscription Levels page. A popup notification will appear, alerting you that you are about to remove the level permanently. If you confirm, the data for the subscription level will be deleted, with no way to get it back.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Note</strong>: when you delete a subscription, all subscribers of that subscription will have their status changed to <strong>Cancelled</strong>, meaning that all of them will have their access to premium content revoked.', 'rcp' ); ?></p>
			<p><?php _e( 'If you are going to delete a subscription with active subscribers, it is advised that you first change the subscription level of each of the subscribers before deleting the membership package.', 'rcp' ); ?></p>
			<?php
			break;

		default;
			break;

	endswitch;

	return ob_get_clean();
}

/**
 * Render discounts tab content
 *
 * @param string $id
 *
 * @return string
 */
function rcp_render_discounts_tab_content( $id ) {
	switch( $id ) :

		case 'general' :
			ob_start(); ?>
			<p><?php _e( 'Discount codes all you to give special offers to new registrations, giving extra incentive for users to sign up for your website\'s premium content section. Restrict Content Pro\'s discount codes work just like any other. There are two kinds:', 'rcp' ); ?></p>
			<ul>
				<li><?php _e( 'Flat - a flat dollar amount discount. This will take the specified number of dollars (or whatever your currency is) off of the base subscription price.', 'rcp' ); ?></li>
				<li><?php _e( 'Percentage - a discount based on a percentage amount. So if your subscription is $10, and your discount is 10%, the registration price will be $9.', 'rcp' ); ?></li>
			</ul>
			<?php
			break;
		case 'adding_discounts' :
			ob_start(); ?>
			<p><?php _e( 'You may create an unlimited number of discount codes, and adding them is simple. From the Discount Codes menu page, simply fill out the form for Add New Discount.', 'rcp' ); ?></p>
			<ul>
				<li><?php _e( 'Name - This is just used for your own administrative / organizational purposes.', 'rcp' ); ?></li>
				<li><?php _e( 'Description - This is used to describe the discount code, and only used for administrative / organizational purposes.', 'rcp' ); ?></li>
				<li><?php _e( 'Code - This is the actual code that users will enter in the registration form when signing up. The code can be anything you want, though a string of all uppercase letters, that preferably spell out a word or phrase, is recommended. It is best to avoid using spaces.', 'rcp' ); ?></li>
				<li><?php _e( 'Type - This is the type of discount you want this code to give, either flat or percentage. Read "General" for an explanation of code types.', 'rcp' ); ?></li>
				<li><?php _e( 'Amount - This is the amount of discount to give with this code. The discount amount is subtracted from the subscription base price.', 'rcp' ); ?></li>
				<li><?php _e( 'Subscription - You can choose to limit the discount code to a specific subscription only, or allow it to be activated on any level.', 'rcp' ); ?></li>
				<li><?php _e( 'Expiration Date - Optionally, you can select a date for the discount code to expire. Leave blank for no expiration.', 'rcp' ); ?></li>
				<li><?php _e( 'Max Uses - You can specify a maximum number of times a discount code may be used. Leave blank for unlimited.', 'rcp' ); ?></li>
			</ul>
			<?php
			break;
		case 'editing_discounts' :
			ob_start(); ?>
			<p><?php _e( 'Discount codes can be edited at anytime to change the name, description, code, type, and/or amount. You can also deactivate codes to make them unavailable, but keep them available for future use.', 'rcp' ); ?></p>
			<p><?php _e( 'To edit a discount, click "Edit" on the right side of the screen, next to the discount code you wish to modify. This will bring up a form with all of the discount code\'s information. Simply change what you wish and click "Update Discount" when finished. You may cancel your editing by clicking "Cancel" at the top of the page.', 'rcp' ); ?></p>
			<?php
			break;
		case 'using_discounts' :
			ob_start(); ?>
			<p><?php _e( 'Discount codes are used when a user registers a new subscription on your site. As long as you have at least one discount code created, there will be an option for the user to enter a code when filling out the registration form.', 'rcp' ); ?></p>
			<p><?php _e( 'If a user enters a discount code, then that code is checked for validity when the form is submitted. If the code is invalid, an error will be shown, and if the code is valid, then the discount will be applied to the subscription price when the user is redirected to the payment gateway.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Note</strong>: users may only use a discount code one time. When a code is used, it is recorded in the database for that user and may never be used by them again.', 'rcp' ); ?></p>
			<p><?php _e( 'Each time a discount code is used, a count will be increased in the database so that you can see the total number of times a code has been used.', 'rcp' ); ?></p>
			<p><?php _e( 'If you wish to see all the discount codes a particular user has used, click "Details" on the user from the Members page.', 'rcp' ); ?></p>
			<?php
			break;

		default;
			break;

	endswitch;

	return ob_get_clean();
}

/**
 * Render payments tab content
 *
 * @param string $id
 *
 * @return string
 */
function rcp_render_payments_tab_content( $id ) {
	switch( $id ) :

		case 'general' :
			ob_start(); ?>
			<p><?php _e( 'This page is a log of all payments that have ever been recorded with Restrict Content Pro. Each time a payment is made, whether it is a one-time sign up payment, or a recurring subscription payment, it is logged here.', 'rcp' ); ?></p>
			<p><?php _e( 'You can see the subscription package the payment was made for, the date is was made, the total amount paid, and the user that made the payment.', 'rcp' ); ?></p>
			<p><?php _e( 'At the bottom of the payments list, you can also see the total amount that has been earned from subscription payments.', 'rcp' ); ?></p>
			<p><?php _e( 'Payment data is permanent and cannot be manipulated or changed.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Note</strong>: this page only lists completed payments. It will not display any payments that are pending, voided, or cancelled.', 'rcp' ); ?></p>
			<?php
			break;
		default;
			break;

	endswitch;

	return ob_get_clean();
}

/**
 * Render settings tab content
 *
 * @param string $id
 *
 * @return string
 */
function rcp_render_settings_tab_content( $id ) {

	switch( $id ) :

		case 'general' :
			ob_start(); ?>
			<p><?php _e( 'This Settings page lets you configure all of the options available for Restrict Content Pro. You should configure the settings as desired before using the plugin.', 'rcp' ); ?></p>
			<p><?php printf( __( 'If you have any trouble with these settings, or anything else with the plugin, you are welcome to request assistance through our <a href="%s">support forms</a>.', 'rcp' ), 'http://restrictcontentpro.com/support' ); ?></p>
			<?php
			break;
		case 'pages' :
			ob_start(); ?>
			<p><?php _e( 'Restrict Content Pro automatically creates several pages for use inside the plugin. Each page should contain a specific shortcode to display the correct contents.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Registration Page</strong> - This is the page that contains the [register_form] short code. This option is necessary in order to generate the link (to the registration page) used by short codes such as [subscription_details], which shows the details of a user\'s current subscription, or a link to the registration page if not logged in.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Success Page</strong> - This is the page that users are sent to after they have a successful registration. If the user is signing up for a free account, they will be sent to this page and immediately logged in. If the user is signing up for a premium subscription, they will be sent to this page after submitting payment.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Account Page</strong> - This page displays the currently logged in user\'s membership information. It contains the [subscription_details] shortcode.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Edit Profile Page</strong> - This is the page that contains the [rcp_profile_editor] shortcode and allows the member to update their profile information, including first name, last name, email address, and password.', 'rcp' ); ?></p>
			<p><?php printf( __( '<strong>Update Billing Card Page</strong> - This page contains the [rcp_update_card] shortcode and allows the member to update the credit card used for payments. This is only available with some payment gateways. Read <a href="%s">our help article</a> for more information.', 'rcp' ), 'http://docs.restrictcontentpro.com/article/1608-rcpupdatecard' ); ?></p>
			<?php
			break;
		case 'messages' :
			ob_start(); ?>
			<p><?php _e( 'These are the messages displayed to a user when they attempt to view content that they do not have access to.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Free Content Message</strong> - this message will be displayed to non-logged in users when they attempt to access a post or page that is restricted to registered users only. In this case, registered users refers to members that have an account on the site, not necessarily users that have a paid subscription. So this message will only be displayed to non-logged in users.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Premium Content Message</strong> - this message is displayed to users, logged in or not, when they attempt to access premium-members-only content. This message will be displayed even to logged in users, if they do not have an active subscription on the site.', 'rcp' ); ?></p>

			<p><?php _e( 'You may use HTML tags in these messages', 'rcp' ); ?></p>
			<?php
			break;
		case 'payments' :
			ob_start(); ?>
			<p><?php _e( 'These settings control payment settings, enabled gateways, and API keys.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Currency</strong> - Choose the currency for your site\'s subscription packages.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Currency Position</strong> - Choose the location of your currency sign, either before or after the amount.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Enabled Gateways</strong> - Choose which gateway(s) you wish to enable on your registration form. You may choose one or several. After selecting a gateway, you also need to scroll down to enter your API key(s) and other details.', 'rcp' ); ?></p>
			<p><?php printf( __( '<strong>Sandbox Mode</strong> - This option allows you to test the plugin with your chosen gateway\'s sandbox tools . You can submit payments using test accounts and/or card data . Leave this option as <strong> unchecked</strong> in order for your site to function live. Contact <a href="%s">support</a> if you have any questions about processing test payments.', 'rcp' ), 'http://restrictcontentpro.com/support' ); ?></p>
			<?php
			break;
		case 'emails' :
			ob_start(); ?>
			<p><?php _e( 'These settings allow you to customize the emails that are sent to users when their subscription statuses change. Emails are sent to users when their accounts are activated (after successful payment), when accounts are cancelled, when a subscription reaches its expiration date, and when a user signs up for a free trial account. Emails are <strong>not</strong> sent when a user\'s status or subscription is manually changed by site admins.', 'rcp' ); ?></p>
			<p><?php _e( 'Each message that is sent out to users can be customized to your liking. There are a variety of template tags available for use in the emails, and those are listed below (and to the right of the input fields):', 'rcp' ); ?></p>
			<?php echo rcp_get_emails_tags_list(); ?>
			<p><?php _e( 'Each of these template tags will be automatically replaced with their values when the email is sent.', 'rcp' ); ?></p>
			<p><?php _e( 'You may use HTML in the emails.', 'rcp' ); ?></p>
			<?php
			break;
		case 'misc' :
			ob_start(); ?>
			<p><?php _e( '<strong>Hide Restricted Posts</strong> - this option will cause all premium posts to be completely hidden from users who do not have access to them. This is useful if you wish to have content that is 100% invisible to non-authorized users. What this means is that premium posts won\'t be listed on blog pages, archives, recent post widgets, search results, RSS feeds, or anywhere else. If, when this setting is enabled, a user tries to access a premium post from a direct URL, they will be automatically redirected to the page you choose below.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Redirect Page</strong> - this is the page non-authorized users are sent to when they try to access a premium post by direct URL.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Redirect Default Login URL</strong> - this option will force the wp-login.php URL to redirect to the page you choose below.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Login Page</strong> - this is the page the default login URL redirects to. This page should contain the [login_form] short code.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Content Excerpts</strong> - choose whether or not to show excerpts to members without access to the content. You can choose a global setting like "always" or "never", or you can choose to decide for each post individually.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Prevent Account Sharing</strong> - check this on if you\'d like to prevent multiple users from logging into the same account simultaneously.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Disable WordPress Toolbar</strong> - this option will disable the WordPress toolbar for your subscribers. It will not disable the toolbar for administrators.', 'rcp' ); ?></p>
			<p><?php _e( '<strong>Disable Form CSS</strong> - the plugin adds a small amount of CSS code to the registration form. Check this option to prevent that CSS from being loaded. This is useful if you only want to use your own styling.', 'rcp' ); ?></p>
			<p><?php printf( __( '<strong>reCaptcha</strong> - Check this to enable a reCaptcha validation form on the registration form. This is an anti-spam protection and will require that the user enter letters / numbers in a field that match a provided image. This requires that you have a reCaptcha account, which is <a href="%s">free to signup for</a>.', 'rcp' ), 'https://www.google.com/recaptcha' ); ?></p>
			<p><?php printf( __( '<strong>Opt into beta versions</strong> - this option allows you to test the Restrict Content Pro beta versions. If enabled, you\'ll receive an update notification in WordPress when a new beta version is available. You can read more about beta testing in <a href="%s">our help article</a>.', 'rcp' ), 'http://docs.restrictcontentpro.com/article/1784-test-beta-versions' ); ?></p>
			<?php
			break;
		case 'logging' :
			ob_start(); ?>
			<p><?php _e( '<strong>Enable IPN Reports</strong> - by checking this option, you will enable an automatic email that is sent to the WordPress admin email anytime a PayPal IPN attempt is made. IPN attempts are made when a user signs up for a paid subscription, and when recurring payments are made or cancelled.', 'rcp' ); ?></p>
			<p><?php _e( 'When an IPN attempt is made, it is either Valid, or Invalid. A valid IPN is one that resulted in a successful payment and notification of the payment. An invalid IPN attempt happens when, for whatever reason, PayPal is unable to correctly notify your site of a payment or subscription change.', 'rcp' ); ?></p>
			<p><?php _e( 'With this option enabled, the email address set in the General WordPress Settings will get an email every time an IPD request is made. This is useful for debugging, in the case something is not working correctly.', 'rcp' ); ?></p>
			<?php
			break;
		default;
			break;

	endswitch;

	return ob_get_clean();
}
