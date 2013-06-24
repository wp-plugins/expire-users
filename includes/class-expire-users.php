<?php

class Expire_Users {
	
	var $admin;
	var $cron;
	var $settings;
	var $user;
	
	function Expire_Users() {
		$this->cron = new Expire_Users_Cron();
		$this->admin = new Expire_User_Admin();
		add_filter( 'authenticate', array( $this, 'authenticate' ), 10, 3 );
		add_filter( 'allow_password_reset', array( $this, 'allow_password_reset' ), 10, 2 );
		add_filter( 'shake_error_codes', array( $this, 'shake_error_codes' ) );
		add_action( 'register_form', array( $this, 'register_form' ) );
		add_action( 'user_register', array( $this, 'user_register' ) );
		add_action( 'expire_users_expired', array( $this, 'handle_on_expire_default_to_role' ) );
		add_action( 'expire_users_expired', array( $this, 'handle_on_expire_user_reset_password' ) );
		add_action( 'expire_users_expired', array( $this, 'handle_on_expire_user_email' ) );
		add_action( 'expire_users_expired', array( $this, 'handle_on_expire_user_email_admin' ) );
		add_filter( 'expire_users_email_notification_message', array( $this, 'email_notification_filter' ), 20, 2 );
		add_filter( 'expire_users_email_admin_notification_message', array( $this, 'email_notification_filter' ), 20, 2 );
		add_filter( 'expire_users_email_notification_subject', array( $this, 'email_notification_filter' ), 20, 2 );
		add_filter( 'expire_users_email_admin_notification_subject', array( $this, 'email_notification_filter' ), 20, 2 );
		add_filter( 'option_expire_users_notification_message', array( $this, 'default_expire_users_notification_message' ) );
		add_filter( 'option_expire_users_notification_admin_message', array( $this, 'default_expire_users_notification_admin_message' ) );
	}
	
	/**
	 * Register Form
	 * Adds a hidden field to the register form to flag that a new user should use
	 * the auto-expire settings.
	 */
	function register_form() {
		echo '<input type="hidden" name="expire_users" value="auto" />';
	}
	
	/**
	 * User Register
	 * Runs on user registration.
	 */
	function user_register( $user_id ) {
		if ( isset( $_POST['expire_users'] ) && 'auto' == $_POST['expire_users'] ) {
			
			$expire_settings = $this->admin->settings->get_default_expire_settings();
			
			$expire_data = array(
				'expire_user_date_type'         => $expire_settings['expire_user_date_type'],
				'expire_user_date_in_num'       => $expire_settings['expire_user_date_in_num'],
				'expire_user_date_in_block'     => $expire_settings['expire_user_date_in_block'],
				'expire_user_date_on_timestamp' => $expire_settings['expire_timestamp'],
				'expire_user_role'              => $expire_settings['expire_user_role'],
				'expire_user_reset_password'    => $expire_settings['expire_user_reset_password'],
				'expire_user_email'             => $expire_settings['expire_user_email'],
				'expire_user_email_admin'       => $expire_settings['expire_user_email_admin']
			);
			
			$user = new Expire_User( $user_id );
			$user->set_expire_data( $expire_data );
			$user->save_user();
		}
	}
	
	/**
	 * Change role when user expires?
	 */
	function handle_on_expire_default_to_role( $expired_user ) {
		if ( $expired_user->on_expire_default_to_role ) {
			if ( get_role( $expired_user->on_expire_default_to_role ) ) {
				$u = new WP_User( $expired_user->user_id );
				$u->set_role( $expired_user->on_expire_default_to_role );
			}
		}
	}
	
	/**
	 * Generate random password when user expires?
	 */
	function handle_on_expire_user_reset_password( $expired_user ) {
		if ( $expired_user->on_expire_user_reset_password ) {
			$password = wp_generate_password( 12, false );
			wp_set_password( $password, $expired_user->user_id );
		}
	}
	
	/**
	 * Send notification email when user expires?
	 */
	function handle_on_expire_user_email( $expired_user ) {
		if ( $expired_user->on_expire_user_email ) {
			$u = new WP_User( $expired_user->user_id );
			$message = apply_filters( 'expire_users_email_admin_notification_message', get_option( 'expire_users_notification_message' ), $expired_user );
			$subject = apply_filters( 'expire_users_email_admin_notification_subject', __( 'Your login details to %%sitename%% have expired', 'expired-users' ), $expired_user );
			if ( ! empty( $subject ) && ! empty( $message ) ) {
				wp_mail( $u->user_email, $subject, $message );
			}
		}
	}
	
	/**
	 * Send admin notification email when user expires?
	 */
	function handle_on_expire_user_email_admin( $expired_user ) {
		if ( $expired_user->on_expire_user_email_admin ) {
			$message = apply_filters( 'expire_users_email_notification_message', get_option( 'expire_users_notification_admin_message' ), $expired_user );
			$subject = apply_filters( 'expire_users_email_notification_subject', __( 'Login details to %%sitename%% have expired (%%username%%)', 'expired-users' ), $expired_user );
			if ( ! empty( $subject ) && ! empty( $message ) ) {
				wp_mail( get_bloginfo( 'admin_email' ), $subject, $message );
			}
		}
	}
	
	/**
	 * Email notification filter
	 */
	function email_notification_filter( $message, $expired_user ) {
		$u = new WP_User( $expired_user->user_id );
		$message = str_replace( '%%name%%', trim( $u->user_nicename . ' ' . $u->last_name ), $message );
		$message = str_replace( '%%username%%', $u->user_login, $message );
		$message = str_replace( '%%expirydate%%', date( 'jS F Y @ h:i', $expired_user->expire_timestamp ), $message );
		$message = str_replace( '%%sitename%%', get_bloginfo( 'name' ), $message );
		return $message;
	}
	
	function default_expire_users_notification_message( $value ) {
		if ( empty( $value ) ) {
			$value = __( 'Your access to %%sitename%% has expired.', 'expire-users' );
		}
		return $value;
	}
	
	function default_expire_users_notification_admin_message( $value ) {
		if ( empty( $value ) ) {
			$value = __( 'Access to %%sitename%% has expired for %%name%% (%%username%%) on %%expirydate%%', 'expire-users' );
		}
		return $value;
	}
	
	/**
	 * Authenticate
	 */
	function authenticate( $user, $username, $password ) {
		$checkuser = get_user_by( 'login', $username );
		if ( $checkuser ) {
			$expired = get_user_meta( $checkuser->ID, '_expire_user_expired', true );
			if ( $expired == 'Y' ) {
				remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );
				return new WP_Error( 'expire_users_expired', __( '<strong>ERROR</strong>: Your user details have expired.', 'expire-users' ) );
			}
		}
		return $user;
	}
	
	/**
	 * Allow Password Reset
	 */
	function allow_password_reset( $allow, $user_ID ) {
		if ( absint( $user_ID ) > 0 ) {
			$expired = get_user_meta( $user_ID, '_expire_user_expired', true );
			if ( $expired == 'Y' ) {
				$allow = new WP_Error( 'expire_users_expired_password_reset', __( '<strong>ERROR</strong>: Your user details have expired so you are no longer able to reset your password.', 'expire-users' ) );
			}
		}
		return $allow;
	}
	
	/**
	 * Shake Error Codes
	 */
	function shake_error_codes( $shake_codes ){
		 $shake_codes[] = 'expire_users_expired';
		 $shake_codes[] = 'expire_users_expired_password_reset';
		 return $shake_codes;
	}
		
}

?>