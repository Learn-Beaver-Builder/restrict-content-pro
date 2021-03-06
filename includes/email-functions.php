<?php
/**
 * Email Functions
 *
 * Functions for sending emails to members.
 *
 * @package     Restrict Content Pro
 * @subpackage  Email Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Send emails to members based on subscription status changes.
 *
 * @param int    $user_id ID of the user to send the email to.
 * @param string $status  User's status, to determine which email to send.
 *
 * @return void
 */
function rcp_email_subscription_status( $user_id, $status = 'active' ) {

	global $rcp_options;

	$user_info     = get_userdata( $user_id );
	$message       = '';
	$admin_message = '';
	$site_name     = stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) );

	$admin_emails  = ! empty( $rcp_options['admin_notice_emails'] ) ? $rcp_options['admin_notice_emails'] : get_option('admin_email');
	$admin_emails  = apply_filters( 'rcp_admin_notice_emails', explode( ',', $admin_emails ) );
	$admin_emails  = array_map( 'sanitize_email', $admin_emails );

	// Allow add-ons to add file attachments

	$attachments = apply_filters( 'rcp_email_attachments', array(), $user_id, $status );

	$emails = new RCP_Emails;
	$emails->member_id = $user_id;

	switch ( $status ) :

		case "active" :

			if( rcp_is_trialing( $user_id ) ) {
				break;
			}

			if( ! isset( $rcp_options['disable_active_email'] ) ) {

				$message = isset( $rcp_options['active_email'] ) ? $rcp_options['active_email'] : '';
				$message = apply_filters( 'rcp_subscription_active_email', $message, $user_id, $status );
				$subject = isset( $rcp_options['active_subject'] ) ? $rcp_options['active_subject'] : '';
				$subject = apply_filters( 'rcp_subscription_active_subject', $subject, $user_id, $status );

			}

			if( ! isset( $rcp_options['disable_active_email_admin'] ) ) {
				$admin_message = isset( $rcp_options['active_email_admin'] ) ? $rcp_options['active_email_admin'] : '';
				$admin_subject = isset( $rcp_options['active_subject_admin'] ) ? $rcp_options['active_subject_admin'] : '';

				if( empty( $admin_message ) ) {
					$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $user_info->display_name . ' (' . $user_info->user_login . ') ' . __( 'is now subscribed to', 'rcp' ) . ' ' . $site_name . ".\n\n" . __( 'Subscription level', 'rcp' ) . ': ' . rcp_get_subscription( $user_id ) . "\n\n";
					$admin_message = apply_filters( 'rcp_before_admin_email_active_thanks', $admin_message, $user_id );
					$admin_message .= __( 'Thank you', 'rcp' );
				}

				if( empty( $admin_subject ) ) {
					$admin_subject = sprintf( __( 'New subscription on %s', 'rcp' ), $site_name );
				}
			}
			break;

		case "cancelled" :

			if( ! isset( $rcp_options['disable_cancelled_email'] ) ) {

				$message = isset( $rcp_options['cancelled_email'] ) ? $rcp_options['cancelled_email'] : '';
				$message = apply_filters( 'rcp_subscription_cancelled_email', $message, $user_id, $status );
				$subject = isset( $rcp_options['cancelled_subject'] ) ? $rcp_options['cancelled_subject'] : '';
				$subject = apply_filters( 'rcp_subscription_cancelled_subject', $subject, $user_id, $status );

			}

			if( ! isset( $rcp_options['disable_cancelled_email_admin'] ) ) {
				$admin_message = isset( $rcp_options['cancelled_email_admin'] ) ? $rcp_options['cancelled_email_admin'] : '';
				$admin_subject = isset( $rcp_options['cancelled_subject_admin'] ) ? $rcp_options['cancelled_subject_admin'] : '';

				if( empty( $admin_message ) ) {
					$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $user_info->display_name . ' (' . $user_info->user_login . ') ' . __( 'has cancelled their subscription to', 'rcp' ) . ' ' . $site_name . ".\n\n" . __( 'Their subscription level was', 'rcp' ) . ': ' . rcp_get_subscription( $user_id ) . "\n\n";
					$admin_message = apply_filters( 'rcp_before_admin_email_cancelled_thanks', $admin_message, $user_id );
					$admin_message .= __( 'Thank you', 'rcp' );
				}

				if( empty( $admin_subject ) ) {
					$admin_subject = sprintf( __( 'Cancelled subscription on %s', 'rcp' ), $site_name );
				}
			}

		break;

		case "expired" :

			if( ! isset( $rcp_options['disable_expired_email'] ) ) {

				$message = isset( $rcp_options['expired_email'] ) ? $rcp_options['expired_email'] : '';
				$message = apply_filters( 'rcp_subscription_expired_email', $message, $user_id, $status );

				$subject = isset( $rcp_options['expired_subject'] ) ? $rcp_options['expired_subject'] : '';
				$subject = apply_filters( 'rcp_subscription_expired_subject', $subject, $user_id, $status );

				add_user_meta( $user_id, '_rcp_expired_email_sent', 'yes' );

			}

			if( ! isset( $rcp_options['disable_expired_email_admin'] ) ) {
				$admin_message = isset( $rcp_options['expired_email_admin'] ) ? $rcp_options['expired_email_admin'] : '';
				$admin_subject = isset( $rcp_options['expired_subject_admin'] ) ? $rcp_options['expired_subject_admin'] : '';

				if ( empty( $admin_message ) ) {
					$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $user_info->display_name . "'s " . __( 'subscription has expired', 'rcp' ) . "\n\n";
					$admin_message = apply_filters( 'rcp_before_admin_email_expired_thanks', $admin_message, $user_id );
					$admin_message .= __( 'Thank you', 'rcp' );
				}

				if ( empty( $admin_subject ) ) {
					$admin_subject = sprintf( __( 'Expired subscription on %s', 'rcp' ), $site_name );
				}
			}

		break;

		case "free" :

			if( ! isset( $rcp_options['disable_free_email'] ) ) {

				$message = isset( $rcp_options['free_email'] ) ? $rcp_options['free_email'] : '';
				$message = apply_filters( 'rcp_subscription_free_email', $message, $user_id, $status );

				$subject = isset( $rcp_options['free_subject'] ) ? $rcp_options['free_subject'] : '';
				$subject = apply_filters( 'rcp_subscription_free_subject', $subject, $user_id, $status );

			}

			if( ! isset( $rcp_options['disable_free_email_admin'] ) ) {
				$admin_message = isset( $rcp_options['free_email_admin'] ) ? $rcp_options['free_email_admin'] : '';
				$admin_subject = isset( $rcp_options['free_subject_admin'] ) ? $rcp_options['free_subject_admin'] : '';

				if( empty( $admin_message ) ) {
					$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $user_info->display_name . ' (' . $user_info->user_login . ') ' . __( 'is now subscribed to', 'rcp' ) . ' ' . $site_name . ".\n\n" . __( 'Subscription level', 'rcp' ) . ': ' . rcp_get_subscription( $user_id ) . "\n\n";
					$admin_message = apply_filters( 'rcp_before_admin_email_free_thanks', $admin_message, $user_id );
					$admin_message .= __( 'Thank you', 'rcp' );
				}

				if( empty( $admin_subject ) ) {
					$admin_subject = sprintf( __( 'New free subscription on %s', 'rcp' ), $site_name );
				}
			}

		break;

		case "trial" :

			if( ! isset( $rcp_options['disable_trial_email'] ) ) {

				$message = isset( $rcp_options['trial_email'] ) ? $rcp_options['trial_email'] : '';
				$message = apply_filters( 'rcp_subscription_trial_email', $message, $user_id, $status );

				$subject = isset( $rcp_options['trial_subject'] ) ? $rcp_options['trial_subject'] : '';
				$subject = apply_filters( 'rcp_subscription_trial_subject', $subject, $user_id, $status );

			}

			if( ! isset( $rcp_options['disable_trial_email_admin'] ) ) {
				$admin_message = isset( $rcp_options['trial_email_admin'] ) ? $rcp_options['trial_email_admin'] : '';
				$admin_subject = isset( $rcp_options['trial_subject_admin'] ) ? $rcp_options['trial_subject_admin'] : '';

				if( empty( $admin_message ) ) {
					$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $user_info->display_name . ' (' . $user_info->user_login . ') ' . __( 'is now subscribed to', 'rcp' ) . ' ' . $site_name . ".\n\n" . __( 'Subscription level', 'rcp' ) . ': ' . rcp_get_subscription( $user_id ) . "\n\n";
					$admin_message = apply_filters( 'rcp_before_admin_email_trial_thanks', $admin_message, $user_id );
					$admin_message .= __( 'Thank you', 'rcp' );
				}

				if( empty( $admin_subject ) ) {
					$admin_subject = sprintf( __( 'New trial subscription on %s', 'rcp' ), $site_name );
				}
			}

		break;

		default:
			break;

	endswitch;

	if( ! empty( $message ) ) {
		$emails->send( $user_info->user_email, $subject, $message, $attachments );
	}

	if( ! empty( $admin_message ) ) {
		$emails->send( $admin_emails, $admin_subject, $admin_message );
	}
}

/**
 * Sends "expiring soon" notice to user.
 *
 * @see rcp_check_for_soon_to_expire_users()
 *
 * @param int $user_id ID of the user to send the email to.
 *
 * @return void
 */
function rcp_email_expiring_notice( $user_id = 0 ) {

	global $rcp_options;
	$user_info = get_userdata( $user_id );
	$message   = ! empty( $rcp_options['renew_notice_email'] ) ? $rcp_options['renew_notice_email'] : false;
	$message   = apply_filters( 'rcp_expiring_soon_email', $message, $user_id );
	$subject   = apply_filters( 'rcp_expiring_soon_subject', $rcp_options['renewal_subject'], $user_id );

	if( ! $message ) {
		return;
	}

	$emails = new RCP_Emails;
	$emails->member_id = $user_id;
	$emails->send( $user_info->user_email, $subject, $message );

}

/**
 * Triggers the expiration notice when an account is marked as expired.
 *
 * @param string $status  User's status.
 * @param int    $user_id ID of the user to email.
 *
 * @access  public
 * @since   2.0.9
 * @return  void
 */
function rcp_email_on_expiration( $status, $user_id ) {

	if( 'expired' == $status ) {

		// Send expiration email.
		rcp_email_subscription_status( $user_id, 'expired' );

	}

}
add_action( 'rcp_set_status', 'rcp_email_on_expiration', 11, 2 );

/**
 * Triggers the activation notice when an account is marked as active.
 *
 * @param string $status  User's status.
 * @param int    $user_id ID of the user to email.
 *
 * @access  public
 * @since   2.1
 * @return  void
 */
function rcp_email_on_activation( $status, $user_id ) {

	if( 'active' == $status && get_user_meta( $user_id, '_rcp_new_subscription', true ) ) {

		// Send welcome email.
		rcp_email_subscription_status( $user_id, 'active' );

	}

}
add_action( 'rcp_set_status', 'rcp_email_on_activation', 11, 2 );

/**
 * Triggers the free trial notice when an account is marked as active.
 *
 * @param string $status  User's status.
 * @param int    $user_id ID of the user to email.
 *
 * @access  public
 * @since   2.7.2
 * @return  void
 */
function rcp_email_on_free_trial( $status, $user_id ) {

	if( 'active' == $status && rcp_is_trialing( $user_id ) ) {

		// Send free trial welcome email.
		rcp_email_subscription_status( $user_id, 'trial' );

	}

}
add_action( 'rcp_set_status', 'rcp_email_on_free_trial', 11, 2 );

/**
 * Triggers the cancellation notice when an account is marked as cancelled.
 *
 * @param string $status  User's status.
 * @param int    $user_id ID of the user to email.
 *
 * @access  public
 * @since   2.1
 * @return  void
 */
function rcp_email_on_cancellation( $status, $user_id ) {

	if( 'cancelled' == $status ) {

		// Send cancellation email.
		rcp_email_subscription_status( $user_id, 'cancelled' );

	}

}
add_action( 'rcp_set_status', 'rcp_email_on_cancellation', 11, 2 );

/**
 * Triggers an email to the member when a payment is received.
 *
 * @param int   $payment_id ID of the payment.
 * @param array $args       Array of payment data.
 * @param float $amount     Amount of the payment.
 *
 * @access  public
 * @since   2.3
 * @return  void
 */
function rcp_email_payment_received( $payment_id, $args, $amount ) {

	global $rcp_options;

	if ( isset( $rcp_options['disable_payment_received_email'] ) ) {
		return;
	}

	$user_info = get_userdata( $args['user_id'] );

	if( ! $user_info ) {
		return;
	}

	$message = ! empty( $rcp_options['payment_received_email'] ) ? $rcp_options['payment_received_email'] : false;
	$message = apply_filters( 'rcp_payment_received_email', $message, $payment_id, $args );

	if( ! $message ) {
		return;
	}

	$emails = new RCP_Emails;
	$emails->member_id = $args['user_id'];
	$emails->payment_id = $payment_id;

	$emails->send( $user_info->user_email, $rcp_options['payment_received_subject'], $message );

}
add_action( 'rcp_insert_payment', 'rcp_email_payment_received', 10, 3 );

/**
 * Get a list of available email templates
 *
 * @since 2.7
 * @return array
 */
function rcp_get_email_templates() {
	$emails = new RCP_Emails;
	return $emails->get_templates();
}

/**
 * Get a formatted HTML list of all available tags
 *
 * @since 2.7
 * @return string $list HTML formated list
 */
function rcp_get_emails_tags_list() {
	// The list
	$list = '<ul>';

	// Get all tags
	$emails = new RCP_Emails;
	$email_tags = $emails->get_tags();

	// Check
	if( count( $email_tags ) > 0 ) {
		foreach( $email_tags as $email_tag ) {
			$list .= '<li><em>%' . $email_tag['tag'] . '%</em> - ' . $email_tag['description'] . '</li>';
		}
	}

	// Backwards compatibility for displaying extra tags from add-ons, etc.
	ob_start();
	do_action( 'rcp_available_template_tags' );
	$list .= ob_get_clean();

	$list .= '</ul>';

	// Return the list
	return $list;
}


/**
 * Email template tag: name
 * The member's name
 *
 * @since 2.7
 * @param int $member_id
 * @return string name
 */
function rcp_email_tag_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->first_name . ' ' . $member->last_name;
}

/**
 * Email template tag: username
 * The member's username on the site
 *
 * @since 2.7
 * @param int $member_id
 * @return string username
 */
function rcp_email_tag_user_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->user_login;
}

/**
 * Email template tag: user_email
 * The member's email
 *
 * @since 2.7
 * @param int $member_id
 * @return string email
 */
function rcp_email_tag_user_email( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->user_email;
}

/**
 * Email template tag: firstname
 * The member's first name
 *
 * @since 2.7
 * @param int $member_id
 * @return string first name
 */
function rcp_email_tag_first_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->first_name;
}

/**
 * Email template tag: lastname
 * The member's last name
 *
 * @since 2.7
 * @param int $member_id
 * @return string last name
 */
function rcp_email_tag_last_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->last_name;
}

/**
 * Email template tag: displayname
 * The member's display name
 *
 * @since 2.7
 * @param int $member_id
 * @return string last name
 */
function rcp_email_tag_display_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->display_name;
}

/**
 * Email template tag: expiration
 * The member's expiration date
 *
 * @since 2.7
 * @param int $member_id
 * @return string expiration
 */
function rcp_email_tag_expiration( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->get_expiration_date();
}

/**
 * Email template tag: subscription_name
 * The name of the member's subscription level
 *
 * @since 2.7
 * @param int $member_id
 * @return string subscription name
 */
function rcp_email_tag_subscription_name( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->get_subscription_name();
}

/**
 * Email template tag: subscription_key
 * The member's subscription key
 *
 * @since 2.7
 * @param int $member_id
 * @return string subscription key
 */
function rcp_email_tag_subscription_key( $member_id = 0 ) {
	$member = new RCP_Member( $member_id );
	return $member->get_subscription_key();
}

/**
 * Email template tag: member_id
 * The member's user ID number
 *
 * @since 2.7
 * @param int $member_id
 * @return string subscription key
 */
function rcp_email_tag_member_id( $member_id = 0 ) {
	return $member_id;
}

/**
 * Email template tag: amount
 * The amount of the member's payment.
 *
 * @since 2.7
 * @param int $member_id The member ID.
 * @param int $payment_id The payment ID
 * @return string amount
 */
function rcp_email_tag_amount( $member_id = 0, $payment_id = 0 ) {

	global $rcp_payments_db;

	if ( ! empty( $payment_id ) ) {
		$payment = $rcp_payments_db->get_payment( $payment_id );
	} else {
		$payment = $rcp_payments_db->get_payments( array(
			'user_id' => $member_id,
			'order'   => 'DESC',
			'number'  => 1
		) );

		$payment = reset( $payment );

		if ( empty( $payment ) || ! is_object( $payment ) ) {
			$payment = new stdClass;
			$payment->amount = false;
		}
	}

	return html_entity_decode( rcp_currency_filter( $payment->amount ), ENT_COMPAT, 'UTF-8' );
}

/**
 * Email template tag: sitename
 * Your site name
 *
 * @since 2.7
 * @return string sitename
 */
function rcp_email_tag_site_name() {
	return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
}

/**
 * Emails a member when a renewal payment fails.
 *
 * @since 2.7
 * @param object $member  The member (RCP_Member object).
 * @param object $gateway The gateway used to process the renewal.
 * @return void
 */
function rcp_email_member_on_renewal_payment_failure( RCP_Member $member, RCP_Payment_Gateway $gateway ) {

	global $rcp_options;

	if ( ! empty( $rcp_options['disable_renewal_payment_failed_email'] ) ) {
		return;
	}

	$status = $member->get_status();

	$message = isset( $rcp_options['renewal_payment_failed_email'] ) ? $rcp_options['renewal_payment_failed_email'] : '';
	$message = apply_filters( 'rcp_subscription_renewal_payment_failed_email', $message, $member->ID, $status );

	$subject = isset( $rcp_options['renewal_payment_failed_subject'] ) ? $rcp_options['renewal_payment_failed_subject'] : '';
	$subject = apply_filters( 'rcp_subscription_renewal_payment_failed_subject', $subject, $member->ID, $status );

	$emails = new RCP_Emails;
	$emails->member_id = $member->ID;

	$emails->send( $member->user_email, $subject, $message );
}
add_action( 'rcp_recurring_payment_failed', 'rcp_email_member_on_renewal_payment_failure', 10, 2 );

/**
 * Email the site admin when a new manual payment is received.
 *
 * @param RCP_Member                 $member
 * @param int                        $payment_id
 * @param RCP_Payment_Gateway_Manual $gateway
 *
 * @since  2.7.3
 * @return void
 */
function rcp_email_admin_on_manual_payment( $member, $payment_id, $gateway ) {

	global $rcp_options;

	if ( isset( $rcp_options['disable_new_user_notices'] ) ) {
		return;
	}

	$admin_emails   = array();
	$admin_emails[] = get_option( 'admin_email' );
	$admin_emails   = apply_filters( 'rcp_admin_notice_emails', $admin_emails );

	$emails             = new RCP_Emails;
	$emails->member_id  = $member->ID;
	$emails->payment_id = $payment_id;

	$site_name = stripslashes_deep( html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT, 'UTF-8' ) );

	$admin_message = __( 'Hello', 'rcp' ) . "\n\n" . $member->display_name . ' (' . $member->user_login . ') ' . __( 'just submitted a manual payment on', 'rcp' ) . ' ' . $site_name . ".\n\n" . __( 'Subscription level', 'rcp' ) . ': ' . $member->get_subscription_name() . "\n\n";
	$admin_message = apply_filters( 'rcp_before_admin_email_manual_payment_thanks', $admin_message, $member->ID );
	$admin_message .= __( 'Thank you', 'rcp' );
	$admin_subject = sprintf( __( 'New manual payment on %s', 'rcp' ), $site_name );

	$emails->send( $admin_emails, $admin_subject, $admin_message );

}
add_action( 'rcp_process_manual_signup', 'rcp_email_admin_on_manual_payment', 10, 3 );

/**
 * Disable the mandrill_nl2br filter while sending RCP emails
 *
 * @since 2.7.2
 * @return void
 */
function rcp_disable_mandrill_nl2br() {
	add_filter( 'mandrill_nl2br', '__return_false' );
}
add_action( 'rcp_email_send_before', 'rcp_disable_mandrill_nl2br' );