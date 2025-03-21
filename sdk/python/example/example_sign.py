import base64
import hashlib
import hmac
import json
import logging
import os
import time
import uuid
from urllib.parse import urlencode

import requests


class KcSigner:
    def __init__(self, api_key: str, api_secret: str, api_passphrase: str):
        """
        KcSigner contains information about `apiKey`, `apiSecret`, `apiPassPhrase`
        and provides methods to sign and generate headers for API requests.
        """
        self.api_key = api_key or ""
        self.api_secret = api_secret or ""
        self.api_passphrase = api_passphrase or ""

        if api_passphrase and api_secret:
            self.api_passphrase = self.sign(api_passphrase.encode('utf-8'), api_secret.encode('utf-8'))

        if not all([api_key, api_secret, api_passphrase]):
            logging.warning("API token is empty. Access is restricted to public interfaces only.")

    def sign(self, plain: bytes, key: bytes) -> str:
        hm = hmac.new(key, plain, hashlib.sha256)
        return base64.b64encode(hm.digest()).decode()

    def headers(self, plain: str) -> dict:
        """
        Headers method generates and returns a map of signature headers needed for API authorization
        It takes a plain string as an argument to help form the signature. The outputs are set of API headers.
        """
        timestamp = str(int(time.time() * 1000))
        signature = self.sign((timestamp + plain).encode('utf-8'), self.api_secret.encode('utf-8'))

        return {
            "KC-API-KEY": self.api_key,
            "KC-API-PASSPHRASE": self.api_passphrase,
            "KC-API-TIMESTAMP": timestamp,
            "KC-API-SIGN": signature,
            "KC-API-KEY-VERSION": "3"
        }


def process_headers(signer: KcSigner, body: bytes, raw_url: str, request: requests.PreparedRequest, method: str):
    request.headers["Content-Type"] = "application/json"

    # Create the payload by combining method, raw URL, and body
    payload = method + raw_url + body.decode()
    headers = signer.headers(payload)

    # Add headers to the request
    request.headers.update(headers)


def get_trade_fees(signer: KcSigner, session: requests.Session):
    endpoint = "https://api.kucoin.com"
    path = "/api/v1/trade-fees"
    method = "GET"
    query_params = {"symbols": "BTC-USDT"}

    # Build full URL and raw URL
    full_path = f"{endpoint}{path}?{urlencode(query_params)}"
    raw_url = f"{path}?{urlencode(query_params)}"

    req = requests.Request(method=method, url=full_path).prepare()
    process_headers(signer, b"", raw_url, req, method)

    resp = session.send(req)
    resp_obj = json.loads(resp.content)
    print(resp_obj)


def add_limit_order(signer: KcSigner, session: requests.Session):
    endpoint = "https://api.kucoin.com"
    path = "/api/v1/hf/orders"
    method = "POST"

    # Prepare order data
    order_data = json.dumps({
        "clientOid": str(uuid.uuid4()),
        "side": "buy",
        "symbol": "BTC-USDT",
        "type": "limit",
        "price": "10000",
        "size": "0.001",
    })

    # Build full URL and raw URL
    full_path = f"{endpoint}{path}"
    raw_url = path

    req = requests.Request(method=method, url=full_path, data=order_data).prepare()
    process_headers(signer, order_data.encode(), raw_url, req, method)

    resp = session.send(req)
    resp_obj = json.loads(resp.content)
    print(resp_obj)


if __name__ == '__main__':
    # Load API credentials from environment variables
    key = os.getenv("API_KEY", "")
    secret = os.getenv("API_SECRET", "")
    passphrase = os.getenv("API_PASSPHRASE", "")

    session = requests.Session()
    signer = KcSigner(key, secret, passphrase)

    # Perform operations
    get_trade_fees(signer, session)
    add_limit_order(signer, session)
