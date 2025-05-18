class Base {

    _el = (selector, context = document) => {
        return context.querySelector(selector);
    }

    _els = (selector, context = document) => {
        return context.querySelectorAll(selector);
    }
    

	/**
	 * Events
	 */
	#events = {}

    /**
     * On
     */
    on(event, callback) {
        if (!this.#events[event]) {
            this.#events[event] = [];
        }
        this.#events[event].push(callback);
        return this;
    }

    /**
     * Emit
     */
    emit(event, ...args) {
        if (this.#events[event]) {
            this.#events[event].forEach(callback => {
                callback(...args);
            });
        }
        return this;
    }

    // Constructor
    init () {
        this.events();
        // on state ready 
        document.addEventListener('DOMContentLoaded', () => this.ready());
    }

    // Fires on DOMContentLoaded
    ready() {}

    // Events
    events() {}

    get html() {
        return document.querySelector('html');
    }
}

export default Base;