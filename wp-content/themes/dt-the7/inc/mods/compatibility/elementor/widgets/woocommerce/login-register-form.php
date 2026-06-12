<?php
/**
 * The7 WC register form widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * The7 WC register form widget for Elementor.
 */
class Login_Register_Form extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-woocommerce-login-register-form';
	}

	/**
	 * @return string|null
	 */
	protected function the7_title() {
		return esc_html__( 'Login/Register Form', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-lock-user';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'login', 'form', 'register', 'woocommerce' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-login-register-form' ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'the7-login-register-form' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer( 'the7-login-register-form', THE7_ELEMENTOR_JS_URI . '/the7-login-register-form.js' );
		the7_register_style( 'the7-login-register-form', THE7_ELEMENTOR_CSS_URI . '/the7-login-register-form.css' );
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content.
		$this->add_logged_out_controls();
		$this->add_additional_options_controls();

		// Style.
		$this->add_box_content_style_controls();
		$this->add_title_style_controls();
		$this->add_description_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_logged_out_controls() {
		$this->start_controls_section(
			'logged_out_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
			]
		);

		$text_columns     = range( 1, 2 );
		$text_columns     = array_combine( $text_columns, $text_columns );
		$text_columns[''] = esc_html__( 'Default', 'the7mk2' );

		$this->add_responsive_control(
			'form_columns',
			[
				'label'                => esc_html__( 'Columns', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => $text_columns,
				'default'              => '2',
				'prefix_class'         => 'elementor%s-form-col-',
				'selectors_dictionary' => [
					'1' => $this->combine_to_css_vars_definition_string(
						[

							'form-columns' => '1',
							'row-gap'      => 'var(--grid-column-gap, 0)',
						]
					),
					'2' => $this->combine_to_css_vars_definition_string(
						[

							'form-columns' => '2',
							'row-gap'      => '0',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'review_column_gap',
			[
				'label'      => esc_html__( 'Rows(Columns) Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vw' ],
				'range'      => [
					'px' => [
						'max' => 100,
					],
					'%'  => [
						'max'  => 10,
						'step' => 0.1,
					],
					'vw' => [
						'max'  => 10,
						'step' => 0.1,
					],
					'em' => [
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-register-form' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_additional_options_controls() {
		$this->start_controls_section(
			'additional_options_section',
			[
				'label' => esc_html__( 'Logged-In State', 'the7mk2' ),
			]
		);

		$this->add_control(
			'redirect_after_login',
			[
				'label'        => esc_html__( 'Redirect To My Account', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'show_logged_in_message',
			[
				'label'        => esc_html__( 'Logged In Message', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_height',
			[
				'label'      => esc_html__( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-register-form > div' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_position',
			[
				'label'                => esc_html__( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'selectors_dictionary' => [
					'top'    => 'justify-content: flex-start; flex-flow: column;',
					'center' => 'justify-content: center; flex-flow: column;',
					'bottom' => 'justify-content: flex-end; flex-flow: column;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword' => ' {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-login-register-form > div, {{WRAPPER}} .woocommerce-ResetPassword',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .the7-login-register-form h2',
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'     => esc_html__( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-login-register-form h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-login-register-form h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_description_style_controls() {
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .the7-login-register-form p, {{WRAPPER}} .the7-login-register-form .lost_password a, {{WRAPPER}} .woocommerce-ResetPassword p',
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-login-register-form p, {{WRAPPER}} .woocommerce-ResetPassword p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'short_desc_link_color',
			[
				'label'     => esc_html__( 'Link Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-login-register-form p a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {
		if ( is_user_logged_in() && ! $this->is_edit_mode() ) {
			if ( ! $this->get_settings_for_display( 'show_logged_in_message' ) ) {
				return;
			}

			$current_user = wp_get_current_user();
			$allowed_html = [
				'a' => [
					'href' => [],
				],
			];
			echo '<p>';
				printf(
					/* translators: 1: user display name 2: logout url */
					wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
					'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
					esc_url( wp_logout_url( remove_query_arg( 'fake-arg' ) ) )
				);
			echo '</p>';
			return;
		}

		$username = '';
		$email    = '';

		if ( isset( $_POST['username'] ) ) {
			$username = wp_unslash( $_POST['username'] );
		}
		if ( isset( $_POST['email'] ) ) {
			$email = wp_unslash( $_POST['email'] );
		}

		do_action( 'woocommerce_before_customer_login_form' );

		echo '<div class="the7-login-register-form elementor-labels-above">';

			echo '<div class="the7-login-form">';
				echo '<h2>' . esc_html__( 'Login', 'woocommerce' ) . '</h2>';

				echo '<form class="woocommerce-form woocommerce-form-login login" method="post">';

					do_action( 'woocommerce_login_form_start' );

					echo '<div class="elementor-form-fields-wrapper">';
						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label" for="username">' . esc_html__( 'Username or email address', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>';

							echo '<input type="text" class="woocommerce-Input  elementor-field-textual elementor-field elementor-size-md" name="username" id="username" autocomplete="username" value="' . esc_attr( $username ) . '" />';
						echo '</div>';
						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label" for="password">' . esc_html__( 'Password', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>';
							echo '<input class="woocommerce-Input elementor-field-textual elementor-field elementor-size-md" type="password" name="password" id="password" autocomplete="current-password" />';
						echo '</div>';

						do_action( 'woocommerce_login_form' );

						/**
						 * IMPORTANT: Button below should go before label. It's by design.
						 */
						echo '<div class="elementor-field-group elementor-column elementor-field-type-submit">';
							echo '<button type="submit" class="elementor-button elementor-size-md button woocommerce-form-login__submit" name="login" value=" ' . esc_attr__( 'Log in', 'woocommerce' ) . '">' . esc_html__( 'Log in', 'woocommerce' ) . '</button>';
						echo '</div>';
						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label woocommerce-form-login__rememberme"><input class="woocommerce-form__input woocommerce-form__input-checkbox elementor-field elementor-size-md" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span>' . esc_html__( 'Remember me', 'woocommerce' ) . '</span></label>';

							wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' );

							$this->add_redirection_hidden_field();

							echo '<p class="woocommerce-LostPassword lost_password"><a href="#lost-password">' . esc_html__( 'Lost your password?', 'woocommerce' ) . '</a></p>';
						echo '</div>';

					echo '</div>';

					do_action( 'woocommerce_login_form_end' );

				echo '</form>';
			echo '</div>';

		if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) {

			echo '<div class="the7-register-form">';
				echo '<h2>' . esc_html__( 'Register', 'woocommerce' ) . '</h2>';

				echo '<form method="post" class="woocommerce-form woocommerce-form-register register" ' . do_action( 'woocommerce_register_form_tag' ) . '>';

					do_action( 'woocommerce_register_form_start' );

					echo '<div class="elementor-form-fields-wrapper">';

			if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {

						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label" for="reg_username">' . esc_html__( 'Username', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>';
							echo '<input type="text" class="woocommerce-Input elementor-field-textual elementor-field elementor-size-md" name="username" id="reg_username" autocomplete="username" value="' . esc_attr( $username ) . '" />';
						echo '</div>';
			}

						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label" for="reg_email">' . esc_html__( 'Email address', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>';
							echo '<input type="email" class="woocommerce-Input elementor-field-textual elementor-field elementor-size-md" name="email" id="reg_email" autocomplete="email" value="' . esc_attr( $email ) . '" />';
						echo '</div>';

			if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {

						echo '<div class="elementor-field-group elementor-column">';
							echo '<label class="elementor-field-label" for="reg_password" class="elementor-field-label">' . esc_html__( 'Password', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>';
							echo '<input type="password" class="woocommerce-Input elementor-field-textual elementor-field elementor-size-md" name="password" id="reg_password" autocomplete="new-password" />';
						echo '</div>';

						echo '<div class="elementor-field-group elementor-column">';
			} else {
						echo '<div class="elementor-field-group elementor-column">';
							echo '<p>' . esc_html__( 'A link to set a new password will be sent to your email address.', 'woocommerce' ) . '</p>';
			}

							do_action( 'woocommerce_register_form' );

						echo '</div>';

						wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' );

						$this->add_redirection_hidden_field();

						echo '<div class="elementor-field-group elementor-column elementor-field-type-submit">';
							echo '<button type="submit" class="elementor-button elementor-size-md button woocommerce-form-register__submit" name="register" value="' . esc_attr__( 'Register', 'woocommerce' ) . '">' . esc_html__( 'Register', 'woocommerce' ) . '</button>';
						echo '</div>';
					echo '</div>';

					do_action( 'woocommerce_register_form_end' );

				echo '</form>';
			echo '</div>';
		}

		echo '</div>';

		do_action( 'woocommerce_after_customer_login_form' );

		do_action( 'woocommerce_before_lost_password_form' );

		echo '<form method="post" class="woocommerce-ResetPassword elementor-labels-above">';
			echo '<div class="elementor-form-fields-wrapper">';
				echo '<p class="elementor-field-group elementor-column">' . apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ) . '</p>';

				echo '<div class="elementor-field-group elementor-column">';
					echo '<label class="elementor-field-label" for="user_login">' . esc_html__( 'Username or email', 'woocommerce' ) . '</label>';
					echo '<input class="woocommerce-Input  elementor-field-textual elementor-field elementor-size-md" type="text" name="user_login" id="user_login" autocomplete="username" />';
				echo '</div>';

				do_action( 'woocommerce_lostpassword_form' );

				echo '<div class="elementor-field-group elementor-column elementor-field-type-submit">';
					echo '<input type="hidden" name="wc_reset_password" value="true" />';
					echo '<button type="submit" class="elementor-button elementor-size-md button' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ) . '" value="' . esc_attr__( 'Reset password', 'woocommerce' ) . '">' . esc_html__( 'Reset password', 'woocommerce' ) . '</button>';
				echo '</div>';
			echo '</div>';

			wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' );

		echo '</form>';

		do_action( 'woocommerce_after_lost_password_form' );
	}

	/**
	 * @return void
	 */
	protected function add_redirection_hidden_field() {
		if ( $this->get_settings_for_display( 'redirect_after_login' ) ) {
			echo '<input type="hidden" name="redirect" value="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"/>';
		}
	}

}
