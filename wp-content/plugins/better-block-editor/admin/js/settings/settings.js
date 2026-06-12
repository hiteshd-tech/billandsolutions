// @ts-nocheck
// WPBBE_RESPONSIVE_BREAKPOINT_SETTINGS is defined in the PHP code

/**
 * There is no full support of JS modules in WP, so do a trick to avoid polluting the global scope.
 * This is a self-invoking function that will run immediately and will not expose any variables to the global scope.
 * It will only expose the `wpbbeSettingsAddBreakpoint` function to the global scope.
 */
( () => {
	const HTML_WRAPPER_ID = 'user-defined-breakpoint-list';

	const {
		BREAKPOINT_LIST = new Map(),
		ALLOWED_SIZE_UNITS = [],
		WP_OPTION_NAME = '',
		I18N_TRANSLATIONS = {},
	} =
		// eslint-disable-next-line  no-undef
		WPBBE_RESPONSIVE_BREAKPOINT_SETTINGS;

	function wpbbeSettingsGetTemplate( identifier, option ) {
		const key = identifier || window.crypto.getRandomValues( new Uint32Array( 3 ) ).join( '-' );
		const name = option?.name || '';
		const value = option?.value || null;
		const unit = option?.unit || 'px';

		const unitSelect =
			`<select name="${ WP_OPTION_NAME }[${ key }][unit]">` +
			ALLOWED_SIZE_UNITS.map(
				( el ) =>
					`<option value="${ el }" ${ el === unit ? 'selected' : '' }>${ el }</option>`
			).join( '\n' ) +
			'</select>';

		const removeButton = [ 'tablet', 'mobile' ].includes( key )
			? ''
			: `<span
				class="remove-breakpoint dashicons dashicons-trash"
				onclick="
					if (window.confirm('${ I18N_TRANSLATIONS.remove_breakpoint_confirm_message }')) {
						this.parentNode.remove();
					}
					return false;
				"
				title="${ I18N_TRANSLATIONS.remove_breakpoint_button_title }"
			/>`;

		return `
		<div class="user-defined-breakpoint item">
			<input
				name="${ WP_OPTION_NAME }[${ key }][name]"
				required
				type="text"
				value="${ name }"
				size="15"
				maxlength="20"
			/>

			<input
				name="${ WP_OPTION_NAME }[${ key }][value]"
				required
				type="number"
				min="0"
				step="1"
				max="9999"
				class="small-text"
				value="${ value }"
			/>

			${ unitSelect }

			${ removeButton }
		</div>
		`;
	}
	// export the function to the global scope
	window.wpbbeSettingsAddBreakpoint = function ( event ) {
		event.stopPropagation();
		event.preventDefault();
		document
			.getElementById( HTML_WRAPPER_ID )
			?.insertAdjacentHTML( 'beforeend', wpbbeSettingsGetTemplate() );
	};

	// show current active breakpoints
	BREAKPOINT_LIST.forEach( ( option, key ) => {
		document.getElementById( HTML_WRAPPER_ID )?.insertAdjacentHTML(
			'beforeend',
			wpbbeSettingsGetTemplate( key, {
				name: option.name,
				value: option.value,
				unit: option.unit,
			} )
		);
	} );

	const tabWrapper = document.querySelector( '.wpbbe-tabs' );
	const tabs = document.querySelectorAll( '.wpbbe-tabs .nav-tab' );
	const settings = document.querySelectorAll( '.wpbbe-setting' );
	const plusSeparator = document.querySelector( '.wpbbe-plus-separator' );
	const moduleEnableCheckbox = document.querySelectorAll(
		'.wpbbe-module-enable input[type="checkbox"]'
	);

	function toggleTabRow( el, visible ) {
		const tr = el.closest( 'tr' );
		if ( ! tr ) {
			return;
		}

		tr.classList.toggle( 'wpbbe-hidden-row', ! visible );
	}

	function updateTabVisibility() {
		tabs.forEach( ( tab ) => {
			const tabName = tab.dataset.tab;

			const hasItems = document.querySelector(
				`.wpbbe-setting[data-tab="${ tabName }"]:not(.wpbbe-hidden-row)`
			);

			tab.style.display = hasItems ? '' : 'none';
		} );
	}

	function activateTab( tabName ) {
		// tabs UI
		tabs.forEach( ( tab ) => {
			tab.classList.toggle( 'nav-tab-active', tab.dataset.tab === tabName );
		} );

		settings.forEach( ( el ) => {
			const tab = el.dataset.tab;
			toggleTabRow( el, tab === tabName );
		} );

		// trigger module checkbox updates for visible tab
		moduleEnableCheckbox.forEach( ( cb ) => {
			cb.dispatchEvent( new Event( 'change' ) );
		} );
	}

	// tab click
	tabs.forEach( ( tab ) => {
		tab.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			const tabName = tab.dataset.tab;
			localStorage.setItem( 'wpbbe-active-tab', tabName );
			activateTab( tab.dataset.tab );
		} );
	} );

	//module settings show/hide
	moduleEnableCheckbox.forEach( ( cb ) => {
		const settingEl = cb.closest( '.wpbbe-setting' );
		if ( ! settingEl ) {
			return;
		}
		const moduleName = settingEl.dataset.module;
		if ( ! moduleName ) {
			return;
		}
		const targets = document.querySelectorAll(
			`.wpbbe-module-setting[data-module="${ moduleName }"]`
		);
		const update = () => {
			const isEnabled = cb.checked;

			targets.forEach( ( el ) => {
				// if the module setting is in a different tab, we should not show it even if the module is enabled
				const tab = el.dataset.tab;
				const activeTab = document.querySelector( '.nav-tab-active' )?.dataset.tab;

				const visible = isEnabled && tab === activeTab;

				toggleTabRow( el, visible );
			} );

			updateTabVisibility();
			updatePlusSeparator();
		};
		cb.addEventListener( 'change', update );
		update();
	} );

	function updatePlusSeparator() {
		if ( ! plusSeparator ) {
			return;
		}

		const hasVisiblePlus = document.querySelector(
			'.wpbbe-plus-feature:not(.wpbbe-hidden-row)'
		);

		plusSeparator.classList.toggle( 'wpbbe-hidden-row', ! hasVisiblePlus );
	}

	const savedTab = localStorage.getItem( 'wpbbe-active-tab' );
	activateTab( savedTab || 'features' );
	tabWrapper?.classList.add( 'wpbbe-ready' );
} )();
