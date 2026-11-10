const environment = config.environment;
const options = (method, key, token, params = {}) => {
    let opts = {};
    let oKeys = Object.keys(params);
    let oValues = Object.values(params);
    if (oKeys.length > 0) {
        let frmData = [];
        for (var i = 0; i < oKeys.length; i++) {
            let tmpKey = environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(oKeys[i]) : oKeys[i];
            let tmpValue = environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(oValues[i]) : oValues[i];
            if (typeof oValues[i] == 'object' || typeof oValues[i] == 'boolean' || typeof oValues[i] == 'number') tmpValue = environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(JSON.stringify(oValues[i])) : JSON.stringify(oValues[i]);
            frmData.push(`${tmpKey}=${tmpValue}`);
        }
        opts = {
            method: 'POST',
            body: frmData.join('&'),
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
                'Authorization-Key': environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(key) : key,
                'X-Token': token
            }
        }
    } else {
        if (method == 'get') {
            opts = {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization-Key': environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(key) : key,
                    'X-Token': token
                }
            }
        } else if (method == 'delete') {
            opts = {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization-Key': environment == 'production' && (config.debug == undefined || config.debug == false) ? btoa(key) : key,
                    'X-Token': token
                }
            }
        }
    }
    return opts;
}
const queryString = (params) => {
    let pairs = {
        keys: Object.keys(params),
        values: Object.values(params)
    };
    let parameters = pairs.keys.map((i, k) => {
        return environment == 'production' && (config.debug == undefined || config.debug == false) ? `${btoa(i)}=${btoa(pairs.values[k])}` : `${i}=${pairs.values[k]}`;
    });
    return parameters.join('&');
}
const get = async(url) => {
    const key = config.auth_key;
    if (url.includes('undefined')) return false;
    try {
        let response = await fetch(url, options('get', key, null));
        let results = await response.text();
        return environment == 'production' && (config.debug == undefined || config.debug == false) ? JSON.parse(atob(results)) : JSON.parse(results);
    } catch (err) {
        return {code: 421, status: err};
    }
}
const post = async(url, parameters) => {
    const key = config.auth_key;
    if (Object.values(parameters).includes(undefined)) return false;
    try {
        let response = await fetch(url, options('post', key, null, parameters));
        let results = await response.text();
        return environment == 'production' && (config.debug == undefined || config.debug == false) ? JSON.parse(atob(results)) : JSON.parse(results);
    } catch (err) {
        return {code: 421, status: err};
    }
}
const del = async(url) => {
    const key = config.auth_key;
    if (url.includes('undefined')) return false;
    try {
        let response = await fetch(url, options('delete', key, null));
        let results = await response.text();
        return environment == 'production' && (config.debug == undefined || config.debug == false) ? JSON.parse(atob(results)) : JSON.parse(results);
    } catch (err) {
        return {code: 421, status: err};
    }
}
export const httpGet = async(url, params = {}) => {
 if (Object.keys(params).length > 0) {
  let query = queryString(params);
  url = `${url}?${query}`;
 }
 return await get(url);
}
export const httpPost = async(url, params = {}) => {
 return await post(url, params);
}
export const httpDel = async(url, params = {}) => {
 return await del(url, params);
}
export const getUrlParams = async(urlString) => {
 let parameters = {};   
 let paramString = urlString.split('?')[1];
 let params_arr = paramString.split('&');
 for (let i = 0; i < params_arr.length; i++) {
  let pair = params_arr[i].split('=');
  let newObject = {};
  newObject[pair[0]] = pair[1];
  parameters = {...parameters, ...newObject};
 }
 return parameters;
}