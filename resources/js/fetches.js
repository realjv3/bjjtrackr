/**
 * Manages abort signals for fetch requests
 */
export default class Fetches {
    constructor() {
        this.setAbortController();
        this.fetchRequests.controller.signal.onabort = () => this.cancelFetches();
    }

    getSignal() {
        return this.fetchRequests.controller.signal;
    }

    setAbortController() {
        this.fetchRequests = {
            controller: new AbortController(),
        };
    }

    cancelFetches() {
        this.fetchRequests.controller.abort();
        this.setAbortController();
    }
}
