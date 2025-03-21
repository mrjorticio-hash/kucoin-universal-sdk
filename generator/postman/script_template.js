function extractPathVariable(str) {
    const regex = /^\{\{(.+?)\}\}$/;
    const match = str.match(regex);
    if (match) {
        return match[1];
    }
    return null;
}

function hasAnyFieldEmpty(obj) {
    for (const key in obj) {
        if (obj[key] === "" || obj[key] === null || obj[key] === undefined) {
            return true;
        }
    }
    return false;
}

function sign(text, secret) {
    const hash = CryptoJS.HmacSHA256(text, secret);
    return CryptoJS.enc.Base64.stringify(hash)
}

function auth(apiKey, method, url, data) {
    if (hasAnyFieldEmpty(apiKey)) {
        console.warn('The API key-related information is not configured; only the public channel API is accessible.')
        return {'User-Agent': `Kucoin-Universal-Postman-SDK/{{SDK-VERSION}}`}
    }

    const timestamp = Date.now();
    const text = timestamp + method.toUpperCase() + url + data;
    const signature = sign(text, apiKey.secret);
    return {
        'KC-API-KEY': apiKey.key,
        'KC-API-SIGN': signature,
        'KC-API-TIMESTAMP': timestamp.toString(),
        'KC-API-PASSPHRASE': sign(apiKey.passphrase || '', apiKey.secret),
        'Content-Type': 'application/json',
        'User-Agent': `Kucoin-Universal-Postman-SDK/{{SDK-VERSION}}`,
        'KC-API-KEY-VERSION': 3,
    };
}

let key = pm.environment.get('API_KEY')
let secret = pm.environment.get('API_SECRET')
let passphrase = pm.environment.get('API_PASSPHRASE')

let url = pm.request.url.getPathWithQuery()
let method = pm.request.method
let body = pm.request.body ? pm.request.body.toString() : ''

for (const index in pm.request.url.path) {
    path = pm.request.url.path[index]
    pathVar = extractPathVariable(path)
    if (pathVar != null) {
        let collectionVariable = pm.collectionVariables.get(pathVar);
        if (collectionVariable != null) {
            url = url.replace(path, collectionVariable)
        } else {
            console.warn('no path variable set: ' + path)
        }
    }
}

header = auth({ key: key, passphrase: passphrase, secret: secret }, method, url, body)

for (const [headerName, headerValue] of Object.entries(header)) {
    pm.request.headers.add({ key: headerName, value: headerValue })
}