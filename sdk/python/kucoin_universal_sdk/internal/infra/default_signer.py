import base64
import hashlib
import hmac
import time
import logging

"""
KcSigner contains information about `apiKey`, `apiSecret`, `apiPassPhrase`, and `apiKeyVersion`
and provides methods to sign and generate headers for API requests.
"""

class KcSigner:
    def __init__(self, api_key: str, api_secret: str, api_passphrase: str, broker_name: str = "",
                 broker_partner: str = "", broker_key: str = ""):
        self.api_key = api_key or ""
        self.api_secret = api_secret or ""
        self.api_passphrase = api_passphrase or ""
        if api_passphrase and api_secret:
            self.api_passphrase = self.sign(api_passphrase.encode('utf-8'), api_secret.encode('utf-8'))
        if not all([api_key, api_secret, api_passphrase]):
            logging.warning("API token is empty. Access is restricted to public interfaces only.")
        self.broker_name = broker_name
        self.broker_partner = broker_partner
        self.broker_key = broker_key

    def sign(self, plain: bytes, key: bytes) -> str:
        hm = hmac.new(key, plain, hashlib.sha256)
        return base64.b64encode(hm.digest()).decode()

    """
    Headers method generates and returns a map of signature headers needed for API authorization
    It takes a plain string as an argument to help form the signature. The outputs are set of API headers,
    """

    def headers(self, plain: str) -> dict:
        timestamp = str(int(time.time() * 1000))
        signature = self.sign((timestamp + plain).encode('utf-8'), self.api_secret.encode('utf-8'))

        return {
            "KC-API-KEY": self.api_key,
            "KC-API-PASSPHRASE": self.api_passphrase,
            "KC-API-TIMESTAMP": timestamp,
            "KC-API-SIGN": signature,
            "KC-API-KEY-VERSION": "3"
        }

    def broker_headers(self, plain: str) -> dict:
        timestamp = str(int(time.time() * 1000))
        signature = self.sign((timestamp + plain).encode(), self.api_secret.encode())

        if self.broker_partner is None or self.broker_name is None or self.broker_partner is None:
            raise RuntimeError("Broker information cannot be empty")

        partner_signature = self.sign((timestamp + self.broker_partner + self.api_key).encode(),
                                      self.api_secret.encode())

        return {
            "KC-API-KEY": self.api_key,
            "KC-API-PASSPHRASE": self.api_passphrase,
            "KC-API-TIMESTAMP": timestamp,
            "KC-API-SIGN": signature,
            "KC-API-KEY-VERSION": "3",
            "KC-API-PARTNER": self.broker_partner,
            "KC-BROKER-NAME": self.broker_name,
            "KC-API-PARTNER-VERIFY": "true",
            "KC-API-PARTNER-SIGN": partner_signature
        }
