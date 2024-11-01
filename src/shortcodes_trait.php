<?php

namespace eightb\sold_alerts;

use \Exception;

/**
	@brief		Handle all shortcodes.
	@since		2016-12-09 21:33:57
**/
trait shortcodes_trait
{
	/**
		@brief		Show the base form and the necessary javascript.
		@since		2016-12-11 13:21:19
	**/
	public function shortcode_8b_sold_alerts()
	{
		$form = $this->form();
		$form->prefix( '8b_sold_alerts' );

		$replacements = [
			'content' => '',		// Content should be the first text replaced.
			'google_api_key' => 'AIzaSyADO3hJlNyLiEk-19vo3Zu9vKF_895euwg',
			'js' => $this->paths( 'url' ) . '/js/js.js',
		];
		$dir = dirname( __DIR__ );
		$template = file_get_contents( $dir . '/views/template.html' );

		$tempform = $this->form();
		$tempform->prefix( '8b_sold_alerts' );

		$handled = false;

		if ( isset( $_GET[ 'unsubscribe' ] ) )
			if ( isset( $_GET[ 'key' ] ) )
			{
				$id = intval( $_GET[ 'unsubscribe' ] );
				$key = sanitize_text_field( $_GET[ 'key' ] );

				// Does this lead ID exist?
				$lead = $this->get_lead()->load_from_store( $id );
				if ( $lead != false )
				{
					if ( $lead->get_unsubscribe_key() == $key )
					{
						$lead->set_inactive();
						$replacements[ 'content' ] = file_get_contents( $dir . '/views/you_have_been_unsubscribed.html' );
						$handled = true;
					}
				}
			}

		if ( ! $handled )
		{
			$ph = $this->get_local_or_site_option( 'lead_form_enter_your_address_placeholder' );
			$form->text( 'address' )
				->css_class( 'address' )
				->label_( 'Enter your address' )
				->maxlength( 128 )
				->placeholder( $ph )
				->required()
				->size( 32 );

			// This holds the places address.
			$form->hidden_input( 'found_address' )
				->css_class( 'found_address' );
				//->value( '1600 Tremont Street;02120' );		// Debug

			// First name's first.
			if ( $this->get_local_or_site_option( 'lead_form_first_name_visible' ) == 'on' )
			{
				$ph = $this->get_local_or_site_option( 'lead_form_first_name_placeholder' );
				$form->text( 'user_first_name' )
					->label( $ph )
					->placeholder( $ph )
					->required( $this->get_local_or_site_option( 'lead_form_first_name_required' )  == 'on' );
			}

			// Then last name.
			if ( $this->get_local_or_site_option( 'lead_form_last_name_visible' ) == 'on' )
			{
				$ph = $this->get_local_or_site_option( 'lead_form_last_name_placeholder' );
				$form->text( 'user_last_name' )
					->label( $ph )
					->placeholder( $ph )
					->required( $this->get_local_or_site_option( 'lead_form_last_name_required' )  == 'on' );
			}

			$ph = $this->get_local_or_site_option( 'lead_form_email_placeholder' );
			$form->email( 'user_email' )
				->label_( 'Your e-mail address' )
				->placeholder( $ph )
				->required()
				->size( 32 );

			$text = $this->get_local_or_site_option( 'lead_form_submit_button_text' );
			$form->primary_button( 'subscribe_me' )
				->value( $text );

			// Now decide which content template to load.
			if (
				empty( $_POST )
				OR ( ! isset( $_POST[ '8b_sold_alerts' ] ) )
				OR ( ! isset( $_POST[ '8b_sold_alerts' ][ 'nonce' ] ) )
			)
			{
				$replacements[ 'content' ] = file_get_contents( $dir . '/views/ask_for_address.html' );
				$replacements[ 'form' ] = $form . '';
			}
			else
			{
				// Convenience. Return just the subarray.
				$post = $_POST[ '8b_sold_alerts' ];

				// Check the nonce.
				$nonce_value = $post[ 'nonce' ];
				if ( ! wp_verify_nonce( $nonce_value, $this->get_nonce_key() ) )
					wp_die( 'Security check failed.' );

				// Look up the address in the API.
				$found_address = sanitize_text_field( $post[ 'found_address' ] );
				$found_address = explode( ';', $found_address );
				$replacements[ 'searched_address' ] = sanitize_text_field( $post[ 'address' ] );

				// No valid address? Go back to the main search.
				if ( count( $found_address ) < 2 )	// Address, zip
				{
					// Remove a non-existent query arg to get the current url.
					wp_redirect( remove_query_arg( '8b' ) );
					exit;
				}

				// We have been given the user's names, save the data.
				$form->post();
				$form->use_post_values();

				$email_address = $form->input( 'user_email' )->get_filtered_post_value();
				$email_address = strtolower( $email_address );

				// Do this now so we can get the post type.
				$lead = new classes\Lead();

				// Find existing e-mail address
				$posts = get_posts( [
					'post_title' => $email_address,
					'post_type' => $lead->get_post_type(),
					'posts_per_page' => PHP_INT_MAX,
				] );
				$found = false;
				foreach( $posts as $post )
					if ( $post->post_title == $email_address )
					{
						$found = true;
						$replacements[ 'error_message' ] = 'You have already subscribed to Sold Alerts!';
						$replacements[ 'content' ] = file_get_contents( $dir . '/views/an_error_occurred.html' );
					}

				if ( ! $found )
				{
					$lead->set( 'post_title', $email_address );
					if ( $this->get_local_or_site_option( 'lead_form_first_name_visible' ) == 'on' )
						$lead->meta->lead_first_name = $form->input( 'user_first_name' )->get_filtered_post_value();
					if ( $this->get_local_or_site_option( 'lead_form_last_name_visible' ) == 'on' )
						$lead->meta->lead_last_name = $form->input( 'user_last_name' )->get_filtered_post_value();
					$lead->meta->lead_email = $email_address;
					$lead->meta->lead_street = $found_address[ 0 ];
					$lead->meta->lead_zip = $found_address[ 1 ];

					$post_content = file_get_contents( $dir . '/views/lead_content.html' );

					$replacements = array_merge( $replacements, $lead->get_shortcodes() );

					$post_content = $this->replace_shortcodes( $post_content, $replacements );
					$lead->set( 'post_content', $post_content );

					$lead->save();

					// Add the subscriber, which also sends the sales.
					try
					{
						$this->get_api()->send_sales( $lead, [
							'prune' => false,
						] );
						$lead->broadcast();
						$this->send_lead( $lead, $replacements );
						$replacements[ 'content' ] = file_get_contents( $dir . '/views/thank_you_for_subscribing.html' );
						$replacements[ 'thank_you_text' ] = $this->get_local_or_site_option( 'thank_you_text' );
					}
					catch( Exception $e )
					{
						$replacements[ 'error_message' ] = $e->getMessage();
						$replacements[ 'content' ] = file_get_contents( $dir . '/views/an_error_occurred.html' );
						$this->debug( 'Error %s. Deleting lead %s.', $e->getMessage(), $lead->id );

						// Force delete of this lead.
						global $wpdb;
						$query = sprintf( "DELETE FROM `%s` WHERE `ID` = '%d'",
							$wpdb->posts,
							$lead->id
						);
						$wpdb->get_results( $query );
					}
				}
			}
		}

		$template = $this->replace_shortcodes( $template, $replacements );

		return $template;
	}
}
