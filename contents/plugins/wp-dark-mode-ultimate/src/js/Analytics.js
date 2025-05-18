
import Ajax from './Ajax';
import Base from './Base';

class Analytics extends Base {
	
	/**
	 * Init
	 */
	events () {
		this.trackVisitor()
	}

	/**
	 * Track visitor
	 */
	async trackVisitor() {
		// Bail, if analytics is disabled.
		if ( ! wp_dark_mode_json.analytics_enabled ) {
			return;
		}

		// Bail, if user has already visited.
		if ( localStorage.getItem( 'wp-dark-mode-visitor' ) ) {
			// return;
		}

		// Set visitor.
		const visitor_id = localStorage.getItem( 'wp-dark-mode-visitor' ) || null;
		// Log('visitor_id', visitor_id);

		const payload = {
			visitor_id: visitor_id || false,
			mode: WPDarkMode.isActive ? 'dark' : 'light',
			nonce: wp_dark_mode_json.nonce,
		};

		if( ! visitor_id ) {

			// For new visitors, send meta data.
			payload.meta = JSON.stringify({
				os: navigator.platform,
				browser: navigator.appCodeName,
				browser_version: navigator.appVersion,
				language: navigator.language,
				timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
			})

			try {
				payload.ip = await fetch( 'https://api.ipify.org?format=json' ).then( response => response.json() ).then( data => data.ip );
			} catch (error) {
				//
				// Log('error', error)
			}
		}

		// Send request.
		const result = await Ajax.post( 'wp_dark_mode_update_visitor', payload );
		// Log('result', result);
		// Set visited.
		if( result && result.success && !visitor_id ) {
			localStorage.setItem( 'wp-dark-mode-visitor', result.data.visitor_id );
		}
	}

}

export default new Analytics();