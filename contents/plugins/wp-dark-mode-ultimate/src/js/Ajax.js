import axios from 'axios';

class Ajax {
    async request(method = 'GET', action = '', data = {}){
        const target = typeof wp_dark_mode_admin_json !== 'undefined' ? wp_dark_mode_admin_json : wp_dark_mode_json
        
        let url = target?.url?.ajax + '?action=' + action + '&_wpnonce=' + target.nonce;


        const headers = {
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        }

        const args = {
            method,
            headers,
            credentials: 'same-origin',
        }

        if ( method !== 'POST' && Object.keys(data).length > 0) {
            url += '&' + this.serialize(data); 
        } else { 
            args.data = data
        }

        // Use axios to make the request
        const response = await axios(url, args)

        // Return the response
        return response.data;
    }

    async get(action = '', data = {}){
        return await this.request('GET', action, data);
    }

    async post(action = '', data = {}){
        return await this.request('POST', action, data);
    }

    serialize(obj) {
        var str = [];
        for (var p in obj)
          if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
          }
        return str.join("&");
    }
}

export default new Ajax();