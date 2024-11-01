<?php

namespace eightb\sold_alerts;

use \Exception;

/**
	@brief		All admin menu functions.
	@since		2016-12-09 20:29:44
**/
trait admin_menu_trait
{
	public function network_admin_menu_emails()
	{
		$get = 'get_local_option';
		$set = 'update_local_option';

		if ( is_network_admin() )
		{
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		$r = '';

		$keys_to_save = [
			'email_new_lead_recipients',
			'email_new_lead_sender_email',
			'email_new_lead_sender_name',
			'email_new_lead_subject',
			'email_new_lead_text',
			'email_sales_sender_email',
			'email_sales_sender_name',
			'email_sales_recipients',
			'email_sales_replyto_email',
			'email_sales_text',
			'email_sales_subject',
		];

		$fs = $form->fieldset( 'fs_new_lead_email' );
		$fs->legend->label_( 'Lead Email' );

		$fs->markup( 'm_new_lead_email_text' )
			->p_( 'These are the settings for the email sent when a new lead is created.' );

		$email_new_lead_sender_email = $fs->text( 'email_new_lead_sender_email' )
			->description_( 'Send the email from this email address. Note that this value may be restricted by your webhost.' )
			->label_( 'Sender Email' )
			->size( 64 )
			->value( $this->$get( 'email_new_lead_sender_email' ) );

		$email_new_lead_sender_name = $fs->text( 'email_new_lead_sender_name' )
			->description_( 'Send the email with this sender name.' )
			->label_( 'Sender name' )
			->size( 64 )
			->value( $this->$get( 'email_new_lead_sender_name' ) );

		$email_new_lead_recipients = $fs->textarea( 'email_new_lead_recipients' )
			->description_( 'To which email addresses shall new leads be sent? One email address per line. Shortcodes allowed.' )
			->label_( 'New lead recipients' )
			->placeholder( "email@address.com" )
			->rows( 5, 40 )
			->value( $this->$get( 'email_new_lead_recipients' ) );

		$email_new_lead_subject = $fs->text( 'email_new_lead_subject' )
			->description_( 'Subject of the new lead email. Valid shortcodes are [8b_sold_alerts_first_name], [8b_sold_alerts_last_name] & [8b_sold_alerts_email].' )
			->label_( 'New lead subject' )
			->size( 64 )
			->value( $this->$get( 'email_new_lead_subject' ) );

		$email_new_lead_text = $fs->wp_editor( 'email_new_lead_text' )
			->description_( 'This is the text of the email for new leads that is sent to the new lead email recipients. Valid shortcodes are [8b_sold_alerts_first_name], [8b_sold_alerts_last_name], [8b_sold_alerts_email] & [8b_home_value_searched_address].' )
			->label_( 'New lead Email' )
			->rows( 10 )
			->set_unfiltered_value( $this->$get( 'email_new_lead_text' ) );

		$fs = $form->fieldset( 'fs_sales_email' );
		$fs->legend->label_( 'Sold Alerts Email' );

		$fs->markup( 'm_sales_email_text' )
			->p_( 'These are the settings for the email sent showing the subscriber their sold alerts.' );

		$email_sales_sender_email = $fs->text( 'email_sales_sender_email' )
			->description_( 'Send the email from this email address. Note that this value may be restricted by your webhost.' )
			->label_( 'Sender Email' )
			->size( 64 )
			->value( $this->$get( 'email_sales_sender_email' ) );

		$email_sales_sender_name = $fs->text( 'email_sales_sender_name' )
			->description_( 'Send the email with this sender name.' )
			->label_( 'Sender name' )
			->size( 64 )
			->value( $this->$get( 'email_sales_sender_name' ) );

		$email_sales_replyto_email = $fs->text( 'email_sales_replyto_email' )
			->description_( 'Optional reply-to address in the e-mail.' )
			->label_( 'Reply-to e-mail address' )
			->size( 64 )
			->value( $this->$get( 'email_sales_replyto_email' ) );

		$email_sales_recipients = $fs->textarea( 'email_sales_recipients' )
			->description_( 'To which email addresses shall sold alerts also be sent, in addition to the subscriber? One email address per line. Shortcodes allowed.' )
			->label_( 'Sold Alerts copies' )
			->placeholder( "email@address.com" )
			->rows( 5, 40 )
			->value( $this->$get( 'email_sales_recipients' ) );

		$email_sales_subject = $fs->text( 'email_sales_subject' )
			->description_( 'Subject of the Sold Alerts email. Valid shortcodes are [8b_sold_alerts_first_name], [8b_sold_alerts_last_name] &[8b_sold_alerts_email].' )
			->label_( 'Sold Alerts subject' )
			->size( 64 )
			->value( $this->$get( 'email_sales_subject' ) );

		$email_sales_text = $fs->wp_editor( 'email_sales_text' )
			->description_( 'This is the text of the email for sold alerts that is sent to the subscriber. Valid shortcodes are [8b_sold_alerts_first_name], [8b_sold_alerts_last_name], [8b_sold_alerts_email], [8b_sold_alerts_data_size], [8b_sold_alerts_data_beds] and [8b_sold_alerts_data_baths].' )
			->label_( 'Sold Alerts Email' )
			->rows( 10 )
			->set_unfiltered_value( $this->$get( 'email_sales_text' ) );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		$this->add_reset_button( $form );

		// Remove the "No text" and replace them with empty values.
		foreach( $form->inputs() as $input )
			if ( $input->get_value() == 'No text' )
				$input->value( '' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			if ( $save->pressed() )
			{
				foreach( $keys_to_save as $key )
				{
					$this->$set( $key, $$key->get_post_value() );
				}

				$r .= $this->info_message_box()
					->_( 'Saved!' );
			}

			$r .= $this->handle_reset_button( $form, $keys_to_save );
			$r .= $this->handle_copy_network_settings_button( $form, $keys_to_save );

			$_POST = [];
			echo $r .= $this->network_admin_menu_emails();
			return;
		}

		if ( is_network_admin() )
			$r .= $this->p_( 'These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.' );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Handle form settings.
		@since		2017-09-01 16:12:29
	**/
	public function network_admin_menu_forms()
	{
		$keys_to_save = [
			'lead_form_email_placeholder',
			'lead_form_enter_your_address_placeholder',
			'lead_form_first_name_visible',
			'lead_form_first_name_required',
			'lead_form_first_name_placeholder',
			'lead_form_last_name_visible',
			'lead_form_last_name_required',
			'lead_form_last_name_placeholder',
			'lead_form_submit_button_text',
			'thank_you_text',
		];

		$get = 'get_local_option';
		$set = 'update_local_option';

		if ( is_network_admin() )
		{
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		//$form->css_class( 'plainview_form_auto_tabs' );
		$r = '';

		$lead_form_enter_your_address_placeholder = $form->text( 'lead_form_enter_your_address_placeholder' )
			->label_( 'Enter Your Address Field Placeholder' )
			->value( $this->$get( 'lead_form_enter_your_address_placeholder' ) );

		// First name
		$lead_form_first_name_visible = $form->checkbox( 'lead_form_first_name_visible' )
			->label_( 'Show First Name Field' )
			->checked( $this->$get( 'lead_form_first_name_visible' ) == 'on' );

		$lead_form_first_name_required = $form->checkbox( 'lead_form_first_name_required' )
			->label_( 'Require First Name' )
			->checked( $this->$get( 'lead_form_first_name_required' ) == 'on' );

		$lead_form_first_name_placeholder = $form->text( 'lead_form_first_name_placeholder' )
			->label_( 'First Name Field Placeholder' )
			->value( $this->$get( 'lead_form_first_name_placeholder' ) );

		// Last name
		$lead_form_last_name_visible = $form->checkbox( 'lead_form_last_name_visible' )
			->label_( 'Show Last Name' )
			->checked( $this->$get( 'lead_form_last_name_visible' ) == 'on' );

		$lead_form_last_name_required = $form->checkbox( 'lead_form_last_name_required' )
			->label_( 'Require Last Name Field' )
			->checked( $this->$get( 'lead_form_last_name_required' ) == 'on' );

		$lead_form_last_name_placeholder = $form->text( 'lead_form_last_name_placeholder' )
			->label_( 'Last Name Field Placeholder' )
			->value( $this->$get( 'lead_form_last_name_placeholder' ) );

		$lead_form_submit_button_text = $form->text( 'lead_form_submit_button_text' )
			->label_( 'Submit Button Text' )
			->value( $this->$get( 'lead_form_submit_button_text' ) );

		$thank_you_text= $form->wp_editor( 'thank_you_text' )
			->description_( 'This text is shown to the user after subscription.' )
			->label_( 'Thank you text' )
			->rows( 10 )
			->set_unfiltered_value( $this->$get( 'thank_you_text' ) );

		$lead_form_email_placeholder = $form->text( 'lead_form_email_placeholder' )
			->label_( 'E-mail Field Placeholder' )
			->value( $this->$get( 'lead_form_email_placeholder' ) );

		// Remove the "No text" and replace them with empty values.
		foreach( $form->inputs() as $input )
			if ( $input->get_value() == 'No text' )
				$input->value( '' );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		$this->add_reset_button( $form );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			if ( $save->pressed() )
			{
				foreach( $keys_to_save as $key )
				{
					// Checkboxes are handled differently, due to their false nature.
					if ( method_exists( $$key, 'is_checked' ) )
					{
						$value = $$key->is_checked();
						$value = $value ? 'on' : 'off';
					}
					else
						$value = $$key->get_post_value();
					$this->$set( $key, $value );
				}

				$r .= $this->info_message_box()
					->_( 'Saved!' );
			}


			$r .= $this->handle_reset_button( $form, $keys_to_save );
			$r .= $this->handle_copy_network_settings_button( $form, $keys_to_save );

			$_POST = [];
			echo $r .= $this->network_admin_menu_forms();
			return;
		}

		if ( is_network_admin() )
			$r .= $this->p_( 'These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.' );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		network_admin_menu_settings
		@since		2016-12-09 20:32:03
	**/
	public function network_admin_menu_general()
	{
		$get = 'get_local_option';
		$set = 'update_local_option';

		if ( is_network_admin() )
		{
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		//$form->css_class( 'plainview_form_auto_tabs' );
		$r = '';

		$keys_to_save = [
			'allow_delete_leads',
			'display_email_log',
			'load_css',
			'new_lead_webhooks',
		];
		$fs = $form->fieldset( 'fs_api_settings' );
		$fs->legend->label_( 'API settings' );

		if ( $this->show_network_settings() )
		{
			$sold_alerts_api_key = $fs->text( 'sold_alerts_api_key' )
				->description_( "This key is used to retrieve the listing from the Sold Alerts server. Use the checkbox below to generate or retrieve a previously generated key for this Wordpress installation. The key is attached to the domain name of this server." )
				->label_( 'Sold Alerts API key' )
				->size( 64 )
				->value( $this->get_api_key() );

			$generate_sold_alerts_api_key = $fs->checkbox( 'generate_sold_alerts_api_key' )
				->checked( $this->$get( 'sold_alerts_api_key' ) == '' )
				->description_( 'Check & save to generate a new or retrieve your existing Sold Alerts API key.' )
				->label_( 'Generate or Retrieve key' );

			// Only show the renew info if there is a key.
			if ( $this->get_api_key() != '' )
			{
				$text = $this->get_text_file( 'api_key_info' );
				$text = $this->replace_api_text( $text, [ 'form' => false ] );
			}
			else
			{
				$text = $this->get_text_file( 'api_key_info_no_key' );
			}

			$fs->markup( 'm_sa_api_key_info' )
				->p( $text );

			$test_sold_alerts = $fs->secondary_button( 'test_sold_alerts' )
				->value_( 'Use after saving: test the Sold Alerts API key' );

		}
		else
		{
			$url = network_admin_url( 'settings.php?page=8b_sold_alerts');
			$fs->markup( 'm_api_for_network' )
				->p_( 'Please visit the <a href="%s">Sold Alerts network settings</a> page to configure your API keys.', $url );
		}

		$fs = $form->fieldset( 'fs_general_settings' );
		$fs->legend->label_( 'General settings' );

		// Only network admins are allowed to lead pool.
		if ( is_network_admin() )
		{
			$lead_pool_blog = $fs->select( 'lead_pool_blog' )
				->value( $this->$get( 'lead_pool_blog' ) )
				->label_( 'Lead pool blog' )
				->option( 'Lead pooling disabled', 0 )
				->required();

			// Because the desc contains html, we need to handle it the long way.
			$description = $this->_( 'To which blog will all leads automatically be pooled. This function requires the %sfree Broadcast plugin%s.',
				'<a href="https://wordpress.org/plugins/threewp-broadcast/">',
				'</a>'
				);
			$lead_pool_blog->description->label->content = $description;

			if ( function_exists( 'ThreeWP_Broadcast' ) )
			{
				$blogs = get_sites( [
					'number' => PHP_INT_MAX,
				] );
				foreach( $blogs as $blog )
				{
					$details = get_blog_details( $blog->blog_id );
					$label = sprintf( '%s (%s)', $details->blogname, $blog->blog_id );
					$lead_pool_blog->option( $label, $blog->blog_id );
				}
			}
		}

		$allow_delete_leads = $fs->checkbox( 'allow_delete_leads' )
			->checked( $this->$get( 'allow_delete_leads' ) )
			->description_( 'For debug purposes, allow leads to be deleted before they expire. This should only be used temporarily, for test purposes.' )
			->label( 'Allow lead deletion' );

		$create_shortcode = $fs->checkbox( 'create_shortcode' )
			->description_( 'Use this checkbox to create a new page with the [%s] shortcode on it.', $this->get_plugin_prefix() )
			->label( 'Create shortcode on new page' );

		$display_email_log = $fs->checkbox( 'display_email_log' )
			->checked( $this->$get( 'display_email_log' ) )
			->description_( 'Display the Sold Alerts email log menu item.' )
			->label( 'Display email log' );

		$load_css = $fs->checkbox( 'load_css' )
			->checked( $this->$get( 'load_css' ) )
			->description_( "Load the plugin's own CSS for the front-end, or disable to style the form yourself." )
			->label_( 'Load plugin CSS' );

		$new_lead_webhooks = $form->textarea( 'new_lead_webhooks' )
			->description_( "Optional webhooks URLs to which to send new leads. One line per URL." )
			->label_( 'Webhooks' )
			->rows( 5, 50 )
			->value( $this->$get( 'new_lead_webhooks' ) );

		$send_webhooks = $form->checkbox( 'send_webhooks' )
			->description_( "Send a test lead to each specified webhook upon saving this form?" )
			->label_( 'Test webhooks' );

		// Remove the "No text" and replace them with empty values.
		foreach( $form->inputs() as $input )
			if ( $input->get_value() == 'No text' )
				$input->value( '' );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		$this->add_reset_button( $form );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			if ( $save->pressed() )
			{
				if ( is_network_admin() )
				{
					foreach( [
						'lead_pool_blog',
					] as $key )
						$this->update_site_option( $key, $$key->get_post_value() );
				}

				if ( $this->show_network_settings() )
				{
					$old_api_key = $this->get_api_key();
					foreach( [
						'sold_alerts_api_key',
					] as $key )
						$this->update_site_option( $key, $$key->get_post_value() );
				}

				// The checkbox should override the manual api key.
				if ( isset( $generate_sold_alerts_api_key ) )
				{
					if ( $generate_sold_alerts_api_key->is_checked() )
					{
						try
						{
							$data = $this->get_api()->generate();

							$r .= $this->info_message_box()
								->_( $data->message );

							// WP caches site options per request. The new API key is received in a different request.
							// We must clear our cache in order to get the new API key.
							$this->clear_site_option_cache( [ 'sold_alerts_api_key', 'google_api_key', 'subscriber_count', 'subscriber_max' ] );
						}
						catch ( Exception $e )
						{
							$r .= $this->error_message_box()
								->_( 'Unable to generate or retrieve your Sold Alerts API key: %s', $e->getMessage() );
						}
					}
					else
					{
						// New API key inputted? Refresh the status.
						try
						{
							$new_api_key = $this->get_api_key();
							if ( $new_api_key != $old_api_key )
								if ( $new_api_key != '' )
									$this->get_api()->status();
						}
						catch( Exception $e )
						{
						}
					}
				}

				if ( $create_shortcode->is_checked() )
				{
					$page_id = wp_insert_post( [
						'post_title' => 'Sold Alerts',
						'post_content' => '[8b_sold_alerts]',
						'post_type' => 'page',
						'post_status' => 'publish',
					] );
					$r .= $this->info_message_box()
						->_( '<a href="%s">A page containing the shortcode</a> has been created.', get_permalink( $page_id ) );
				}

				foreach( $keys_to_save as $key )
					$this->$set( $key, $$key->get_post_value() );

				if ( $send_webhooks->is_checked() )
				{
					$lead = $this->generate_random_lead();
					$this->send_lead_to_webhooks( $lead );
					$r .= $this->info_message_box()
						->_( 'A lead with random information has been sent to the URLs in the webhook textarea.' );
				}

				$r .= $this->info_message_box()
					->_( 'Saved!' );
			}


			if ( $this->show_network_settings() )
			{
				if ( $test_sold_alerts->pressed() )
				{
					try
					{
						$data = $this->get_api()->status();
						$r .= $this->info_message_box()
							->_( 'Your key seems valid and you have %s of a maximum %s subscribers available.',
								intval( $data->subscriber_count ),
								intval( $data->subscriber_max )
							);
					}
					catch ( Exception $e )
					{
						$r .= $this->error_message_box()
							->_( 'Sold Alerts API key test failure: %s', $e->getMessage() );
					}
				}
			}


			$r .= $this->handle_reset_button( $form, $keys_to_save );
			$r .= $this->handle_copy_network_settings_button( $form, $keys_to_save );

			$_POST = [];
			echo $r .= $this->network_admin_menu_general();
			return;
		}

		if ( is_network_admin() )
			$r .= $this->p_( 'These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.' );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}
}
