<?php
/**
 * Payment Gateway Base Class
 *
 * @package     Restrict Content Pro
 * @subpackage  Classes/Roles
 * @copyright   Copyright (c) 2012, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
*/

class RCP_Payment_Gateway_Stripe extends RCP_Payment_Gateway {

	public $id;
	private $secret_key;
	private $publishable_key;

	public function init() {

		global $rcp_options;

		$this->id          = 'stripe';
		$this->title       = 'Stripe';
		$this->description = __( 'Pay with a credit or debit card', 'rcp' );
		$this->supports[]  = 'one-time';
		$this->supports[]  = 'recurring';
		$this->supports[]  = 'fees';

		$this->test_mode   = isset( $rcp_options['sandbox'] );

		if( $this->test_mode ) {

			$this->secret_key      = isset( $rcp_options['stripe_test_secret'] )      ? trim( $rcp_options['stripe_test_secret'] )      : '';
			$this->publishable_key = isset( $rcp_options['stripe_test_publishable'] ) ? trim( $rcp_options['stripe_test_publishable'] ) : '';

		} else {

			$this->secret_key      = isset( $rcp_options['stripe_live_secret'] )      ? trim( $rcp_options['stripe_live_secret'] )      : '';
			$this->publishable_key = isset( $rcp_options['stripe_live_publishable'] ) ? trim( $rcp_options['stripe_live_publishable'] ) : '';

		}

		add_action( 'init', array( $this, 'process_webhooks' ) );

	}

	public function process_signup() {

		Stripe::setApiKey( $this->secret_key );

		$paid = false;

		if ( $this->auto_renew ) {

			// process a subscription sign up

			$plan_id = strtolower( str_replace( ' ', '', $this->subscription_name ) );

			if ( ! $this->plan_exists( $plan_id ) ) {
				// create the plan if it doesn't exist
				$this->create_plan( $this->subscription_name );
			}

			try {

				$customer_id = rcp_get_stripe_customer_id( $this->user_id );

				if ( $customer_id ) {

					$customer_exists = true;

					try {

						// Update the customer to ensure their card data is up to date
						$cu = Stripe_Customer::retrieve( $customer_id );

						if( isset( $cu->deleted ) && $cu->deleted ) {

							// This customer was deleted
							$customer_exists = false;

						}

					// No customer found
					} catch ( Exception $e ) {


						$customer_exists = false;

					}

				}

				// Customer with a discount
				if ( ! empty( $this->discount_code ) ) {

					if( $customer_exists ) {

						$cu->card   = $_POST['stripeToken'];
						$cu->coupon = $this->discount_code;
						$cu->save();

						// Update the customer's subscription in Stripe
						$customer_response = $cu->updateSubscription( array( 'plan' => $plan_id ) );

					} else {

						$customer = Stripe_Customer::create( array(
								'card' 			=> $_POST['stripeToken'],
								'plan' 			=> $plan_id,
								'email' 		=> $this->email,
								'description' 	=> 'User ID: ' . $this->user_id . ' - User Email: ' . $this->email . ' Subscription: ' . $this->subscription_name,
								'coupon' 		=> $_POST['rcp_discount']
							)
						);

					}

				// Customer without a discount
				} else {

					if( $customer_exists ) {

						$cu->card   = $_POST['stripeToken'];
						$cu->save();

						// Update the customer's subscription in Stripe
						$customer_response = $cu->updateSubscription( array( 'plan' => $plan_id ) );

					} else {

						$customer = Stripe_Customer::create( array(
								'card' 			=> $_POST['stripeToken'],
								'plan' 			=> $plan_id,
								'email' 		=> $this->email
								'description' 	=> 'User ID: ' . $this->user_id . ' - User Email: ' . $this->email . ' Subscription: ' . $this->subscription_name
							)
						);

					}

				}

				if ( ! empty( $this->fee ) ) {

					if( $this->fee > 0 ) {
						$description = sprintf( __( 'Signup Fee for %s', 'rcp_stripe' ), $this->subscription_name );
					} else {
						$description = sprintf( __( 'Signup Discount for %s', 'rcp_stripe' ), $this->subscription_name );
					}

					Stripe_InvoiceItem::create( array(
							'customer'    => $customer->id,
							'amount'      => $this->fee * 100,
							'currency'    => strtolower( $this->currency ),
							'description' => $description
						)
					);

					// Create the invoice containing taxes / discounts / fees
					$invoice = Stripe_Invoice::create( array(
						'customer' => $customer->id, // the customer to apply the fee to
					) );
					$invoice->pay();

				}

				rcp_stripe_set_as_customer( $this->user_id, $customer );

				// subscription payments are recorded via webhook

				$paid = true;

			} catch ( Stripe_CardError $e ) {

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

				exit;

			} catch (Stripe_InvalidRequestError $e) {

				// Invalid parameters were supplied to Stripe's API
				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_AuthenticationError $e) {

				// Authentication with Stripe's API failed
				// (maybe you changed API keys recently)

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_ApiConnectionError $e) {

				// Network communication with Stripe failed

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_Error $e) {

				// Display a very generic error to the user

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Exception $e) {

				// Something else happened, completely unrelated to Stripe

				$error = "<p>An unidentified error occurred.</p>";
				$error .= print_r( $e, true );

				wp_die( $error );

			}

		} else {

			// process a one time payment signup

			try {

				$charge = Stripe_Charge::create( array(
						'amount' 		=> $this->price * 100, // amount in cents
						'currency' 		=> strtolower( $this->currency ),
						'card' 			=> $_POST['stripeToken'],
						'description' 	=> 'User ID: ' . $this->user_id . ' - User Email: ' . $this->email . ' Subscription: ' . $this->subscription_name
					)
				);

				$payment_data = array(
					'date'              => date( 'Y-m-d g:i:s', time() ),
					'subscription'      => $this->subscription_name,
					'payment_type' 		=> 'Credit Card One Time',
					'subscription_key' 	=> $this->subscription_key,
					'amount' 			=> $this->price,
					'user_id' 			=> $this->user_id,
					'transaction_id'    => $charge->id
				);

				$rcp_payments = new RCP_Payments();
				$rcp_payments->insert( $payment_data );

				$paid = true;

			} catch ( Stripe_CardError $e ) {

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

				exit;

			} catch (Stripe_InvalidRequestError $e) {

				// Invalid parameters were supplied to Stripe's API
				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_AuthenticationError $e) {

				// Authentication with Stripe's API failed
				// (maybe you changed API keys recently)

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_ApiConnectionError $e) {

				// Network communication with Stripe failed

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Stripe_Error $e) {

				// Display a very generic error to the user

				$body = $e->getJsonBody();
				$err  = $body['error'];

				$error = "<h4>An error occurred</h4>";
				if( isset( $err['code'] ) ) {
					$error .= "<p>Error code: " . $err['code'] ."</p>";
				}
				$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
				$error .= "<p>Message: " . $err['message'] . "</p>";

				wp_die( $error );

			} catch (Exception $e) {

				// Something else happened, completely unrelated to Stripe

				$error = "<p>An unidentified error occurred.</p>";
				$error .= print_r( $e, true );

				wp_die( $error );

			}
		}

		if ( $paid ) {

			// set this user to active
			$member = new RCP_Member( $this->user_id );
			$member->renew();

			if ( $data['new_user'] ) {

				// log the new user in
				rcp_login_user_in( $this->user_id, $this->user_name, $_POST['rcp_user_pass'] );

			} else {

				delete_user_meta( $this->user_id, '_rcp_stripe_sub_cancelled' );

			}

			do_action( 'rcp_stripe_signup', $this->user_id, $this );

		} else {

			wp_die( __( 'An error occurred, please contact the site administrator: ', 'rcp_stripe' ) . get_bloginfo( 'admin_email' ), __( 'Error', 'rcp' ), array( '401' ) );

		}

		// redirect to the success page, or error page if something went wrong
		wp_redirect( $this->return_url ); exit;

	}

	public function process_webhooks() {

		if( ! isset( $_GET['listener'] ) || strtoupper( $_GET['listener'] ) != 'stripe' ) {
			return;
		}

		global $rcp_options;



	}

	public function fields() {

		ob_start();
		rcp_get_template_part( 'card-form' );
		return ob_get_clean();
	}

	private function create_plan( $plan_id = '' ) {

		// get all subscription level info for this plan
		$plan           = rcp_get_subscription_details_by_name( $plan_name );
		$price          = $plan->price * 100;
		$interval       = $plan->duration_unit;
		$interval_count = $plan->duration;
		$name           = $plan->name;
		$plan_id        = strtolower( str_replace( ' ', '', $plan_name ) );
		$currency       = strtolower( $rcp_options['currency'] );

		Stripe::setApiKey( $this->secret_key );

		try {

			Stripe_Plan::create( array(
				"amount"         => $price,
				"interval"       => $interval,
				"interval_count" => $interval_count,
				"name"           => $name,
				"currency"       => $currency,
				"id"             => $plan_id
			) );

			// plann successfully created
			return true;

		} catch ( Exception $e ) {
			// there was a problem
			return false;
		}

	}

	private function plan_exists( $plan_id = '' ) {

		$plan_id = strtolower( str_replace( ' ', '', $plan_id ) );

		Stripe::setApiKey( $this->secret_key );

		try {
			$plan = Stripe_Plan::retrieve( $plan_id );
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

}