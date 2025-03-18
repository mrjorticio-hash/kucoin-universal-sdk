import crypto from "crypto";
import axios, {AxiosInstance, AxiosRequestConfig} from "axios";

class KcSigner {
    private apiKey: string;
    private apiSecret: string;
    private apiPassphrase: string;

    constructor(apiKey: string, apiSecret: string, apiPassphrase: string) {
        /**
         * KcSigner contains information about `apiKey`, `apiSecret`, `apiPassphrase`
         * and provides methods to sign and generate headers for API requests.
         */
        this.apiKey = apiKey || "";
        this.apiSecret = apiSecret || "";
        this.apiPassphrase = apiPassphrase || "";

        if (apiPassphrase && apiSecret) {
            this.apiPassphrase = this.sign(apiPassphrase, apiSecret);
        }

        if (!apiKey || !apiSecret || !apiPassphrase) {
            console.warn("API token is empty. Access is restricted to public interfaces only.");
        }
    }

    private sign(plain: string, key: string): string {
        return crypto.createHmac("sha256", key).update(plain).digest("base64");
    }

    public headers(plain: string): Record<string, string> {
        /**
         * Headers method generates and returns a map of signature headers needed for API authorization.
         * It takes a plain string as an argument to help form the signature. The outputs are a set of API headers.
         */
        const timestamp = Date.now().toString();
        const signature = this.sign(timestamp + plain, this.apiSecret);

        return {
            "KC-API-KEY": this.apiKey,
            "KC-API-PASSPHRASE": this.apiPassphrase,
            "KC-API-TIMESTAMP": timestamp,
            "KC-API-SIGN": signature,
            "KC-API-KEY-VERSION": "3",
            "Content-Type": "application/json",
        };
    }
}

async function processHeaders(
    signer: KcSigner,
    body: string,
    rawUrl: string,
    method: string
): Promise<Record<string, string>> {
    /**
     * Creates and returns headers for API authentication.
     */
    const payload = method + rawUrl + body;
    return signer.headers(payload);
}

async function getTradeFees(signer: KcSigner, axiosInstance: AxiosInstance) {
    const endpoint = "https://api.kucoin.com";
    const path = "/api/v1/trade-fees";
    const method = "GET";
    const queryParams = new URLSearchParams({symbols: "BTC-USDT"}).toString();

    const fullPath = `${endpoint}${path}?${queryParams}`;
    const rawUrl = `${path}?${queryParams}`;

    const headers = await processHeaders(signer, "", rawUrl, method);
    const config: AxiosRequestConfig = {
        method,
        url: fullPath,
        headers,
    };

    try {
        const response = await axiosInstance(config);
        console.log(response.data);
    } catch (error) {
        console.error("Error fetching trade fees:", error);
    }
}

async function addLimitOrder(signer: KcSigner, axiosInstance: AxiosInstance) {
    const endpoint = "https://api.kucoin.com";
    const path = "/api/v1/hf/orders";
    const method = "POST";

    const orderData = JSON.stringify({
        clientOid: crypto.randomUUID(),
        side: "buy",
        symbol: "BTC-USDT",
        type: "limit",
        price: "10000",
        size: "0.001",
    });

    const fullPath = `${endpoint}${path}`;
    const rawUrl = path;

    const headers = await processHeaders(signer, orderData, rawUrl, method);
    const config: AxiosRequestConfig = {
        method,
        url: fullPath,
        headers,
        data: orderData,
    };

    try {
        const response = await axiosInstance(config);
        console.log(response.data);
    } catch (error) {
        console.error("Error placing limit order:", error);
    }
}

async function main() {
    /**
     * Main function to execute API calls.
     */
    const key = process.env.API_KEY || "";
    const secret = process.env.API_SECRET || "";
    const passphrase = process.env.API_PASSPHRASE || "";

    const axiosInstance = axios.create();
    const signer = new KcSigner(key, secret, passphrase);

    await getTradeFees(signer, axiosInstance);
    await addLimitOrder(signer, axiosInstance);
}

main().catch((error) => {
    console.error("Error executing main function:", error);
});
