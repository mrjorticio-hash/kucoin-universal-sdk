import unittest
from unittest.mock import patch
from .default_signer import KcSigner

class TestKcSigner(unittest.TestCase):
    def setUp(self):
        self.api_key = "test_api_key"
        self.api_secret = "test_api_secret"
        self.api_passphrase = "test_passphrase"
        self.broker_name = "test_broker"
        self.broker_partner = "test_partner"
        self.broker_key = "test_broker_key"

        self.signer = KcSigner(
            self.api_key,
            self.api_secret,
            self.api_passphrase,
            self.broker_name,
            self.broker_partner,
            self.broker_key,
        )

    @patch("time.time", return_value=1234567890)
    def test_sign(self, mock_time):
        plain = b"test_plain"
        expected_signature = self.signer.sign(plain, self.api_secret.encode("utf-8"))
        self.assertIsInstance(expected_signature, str)
        self.assertEqual(expected_signature, "jie/gxS38wLFFaBwl/cAzFbAzlI8MIny96jSXrB+iP4=")

    @patch("time.time", return_value=1234567890)
    def test_headers(self, mock_time):
        plain = "test_plain"
        headers = self.signer.headers(plain)
        
        self.assertIn("KC-API-KEY", headers)
        self.assertIn("KC-API-PASSPHRASE", headers)
        self.assertIn("KC-API-TIMESTAMP", headers)
        self.assertIn("KC-API-SIGN", headers)
        self.assertIn("KC-API-KEY-VERSION", headers)

        self.assertEqual(headers["KC-API-KEY"], self.api_key)
        self.assertEqual(headers["KC-API-PASSPHRASE"], self.signer.api_passphrase)
        self.assertEqual(headers["KC-API-KEY-VERSION"], "3")
        self.assertEqual(headers["KC-API-TIMESTAMP"], "1234567890000")

    @patch("time.time", return_value=1234567890)
    def test_broker_headers(self, mock_time):
        plain = "test_plain"
        headers = self.signer.broker_headers(plain)
        
        self.assertIn("KC-API-KEY", headers)
        self.assertIn("KC-API-PASSPHRASE", headers)
        self.assertIn("KC-API-TIMESTAMP", headers)
        self.assertIn("KC-API-SIGN", headers)
        self.assertIn("KC-API-KEY-VERSION", headers)
        self.assertIn("KC-API-PARTNER", headers)
        self.assertIn("KC-BROKER-NAME", headers)
        self.assertIn("KC-API-PARTNER-VERIFY", headers)
        self.assertIn("KC-API-PARTNER-SIGN", headers)

        self.assertEqual(headers["KC-API-KEY"], self.api_key)
        self.assertEqual(headers["KC-API-PASSPHRASE"], self.signer.api_passphrase)
        self.assertEqual(headers["KC-API-KEY-VERSION"], "3")
        self.assertEqual(headers["KC-API-PARTNER"], self.broker_partner)
        self.assertEqual(headers["KC-BROKER-NAME"], self.broker_name)
        self.assertEqual(headers["KC-API-PARTNER-VERIFY"], "true")
        self.assertEqual(headers["KC-API-TIMESTAMP"], "1234567890000")

if __name__ == "__main__":
    unittest.main()
