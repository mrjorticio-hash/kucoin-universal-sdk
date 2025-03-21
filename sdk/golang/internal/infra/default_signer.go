package infra

import (
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"strconv"
	"time"
)

// KcSigner contains information about `apiKey`, `apiSecret`, `apiPassPhrase`, and `apiKeyVersion`
// and provides methods to sign and generate headers for API requests.
type KcSigner struct {
	apiKey        string
	apiSecret     string
	apiPassPhrase string
	apiKeyVersion string
	brokerName    string
	brokerPartner string
	brokerKey     string
}

func Sign(plain []byte, key []byte) []byte {
	hm := hmac.New(sha256.New, key)
	hm.Write(plain)
	data := hm.Sum(nil)
	return []byte(base64.StdEncoding.EncodeToString(data))
}

// Headers method generates and returns a map of signature headers needed for API authorization
// It takes a plain string as an argument to help form the signature. The outputs are set of API headers,
func (ks *KcSigner) Headers(plain string) map[string]string {
	t := IntToString(time.Now().UnixNano() / 1000000)
	p := []byte(t + plain)
	s := string(Sign(p, []byte(ks.apiSecret)))
	ksHeaders := map[string]string{
		"KC-API-KEY":         ks.apiKey,
		"KC-API-PASSPHRASE":  ks.apiPassPhrase,
		"KC-API-TIMESTAMP":   t,
		"KC-API-SIGN":        s,
		"KC-API-KEY-VERSION": "3",
	}
	return ksHeaders
}

func (ks *KcSigner) BrokerHeaders(plain string) map[string]string {
	t := IntToString(time.Now().UnixNano() / 1000000)
	p := []byte(t + plain)
	s := string(Sign(p, []byte(ks.apiSecret)))

	bs := string(Sign([]byte(t+ks.brokerPartner+ks.apiKey), []byte(ks.apiSecret)))

	ksHeaders := map[string]string{
		"KC-API-KEY":            ks.apiKey,
		"KC-API-PASSPHRASE":     ks.apiPassPhrase,
		"KC-API-TIMESTAMP":      t,
		"KC-API-SIGN":           s,
		"KC-API-KEY-VERSION":    "3",
		"KC-API-PARTNER":        ks.brokerPartner,
		"KC-BROKER-NAME":        ks.brokerName,
		"KC-API-PARTNER-VERIFY": "true",
		"KC-API-PARTNER-SIGN":   bs,
	}
	return ksHeaders
}

func NewKcSigner(key, secret, passPhrase, brokerName, brokerPartner, brokerKey string) *KcSigner {
	ks := &KcSigner{
		apiKey:        key,
		apiSecret:     secret,
		apiPassPhrase: string(Sign([]byte(passPhrase), []byte(secret))),
		brokerKey:     brokerKey,
		brokerName:    brokerName,
		brokerPartner: brokerPartner,
	}
	return ks
}

func IntToString(i int64) string {
	return strconv.FormatInt(i, 10)
}
