package main

import (
	"bytes"
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"strconv"
	"time"

	"github.com/google/uuid"
)

type KcSigner struct {
	apiKey        string
	apiSecret     string
	apiPassPhrase string
}

func Sign(plain []byte, key []byte) []byte {
	hm := hmac.New(sha256.New, key)
	hm.Write(plain)
	data := hm.Sum(nil)
	return []byte(base64.StdEncoding.EncodeToString(data))
}

func (ks *KcSigner) Headers(plain string) map[string]string {
	t := strconv.FormatInt(time.Now().UnixNano()/1000000, 10)
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

func NewKcSigner(key, secret, passPhrase string) *KcSigner {
	ks := &KcSigner{
		apiKey:        key,
		apiSecret:     secret,
		apiPassPhrase: string(Sign([]byte(passPhrase), []byte(secret))),
	}
	return ks
}

func getTradeFees(signer *KcSigner, client *http.Client) {
	endpoint := "https://api.kucoin.com"
	path := "/api/v1/trade-fees"
	method := "GET"
	queryParams := "symbols=BTC-USDT"

	fullURL := fmt.Sprintf("%s%s?%s", endpoint, path, queryParams)
	rawPath := fmt.Sprintf("%s?%s", path, queryParams)

	req, err := http.NewRequest(method, fullURL, nil)
	if err != nil {
		fmt.Println("Error creating request:", err)
		return
	}
	var b bytes.Buffer
	b.WriteString(method)
	b.WriteString(rawPath)
	b.Write([]byte{})

	headers := signer.Headers(b.String())
	for k, v := range headers {
		req.Header.Set(k, v)
	}
	resp, err := client.Do(req)
	if err != nil {
		fmt.Println("Error sending request:", err)
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		fmt.Println("Error reading response:", err)
		return
	}

	fmt.Println("Response:", string(body))
}

func addLimitOrder(signer *KcSigner, client *http.Client) {
	endpoint := "https://api.kucoin.com"
	path := "/api/v1/hf/orders"
	method := "POST"

	orderData := map[string]interface{}{
		"clientOid": uuid.NewString(),
		"side":      "buy",
		"symbol":    "BTC-USDT",
		"type":      "limit",
		"price":     "10000",
		"size":      "0.001",
	}

	orderDataBytes, err := json.Marshal(orderData)
	if err != nil {
		fmt.Println("Error marshalling order data:", err)
		return
	}

	fullURL := fmt.Sprintf("%s%s", endpoint, path)

	var b bytes.Buffer
	b.WriteString(method)
	b.WriteString(path)
	b.Write(orderDataBytes)

	req, err := http.NewRequest(method, fullURL, bytes.NewBuffer(orderDataBytes))
	if err != nil {
		fmt.Println("Error creating request:", err)
		return
	}

	headers := signer.Headers(b.String())
	for k, v := range headers {
		req.Header.Set(k, v)
	}

	req.Header.Set("Content-Type", "application/json")

	resp, err := client.Do(req)
	if err != nil {
		fmt.Println("Error sending request:", err)
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		fmt.Println("Error reading response:", err)
		return
	}

	fmt.Println("Response:", string(body))
}

func SignExample() {
	apiKey := os.Getenv("API_KEY")
	apiSecret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	signer := NewKcSigner(apiKey, apiSecret, passphrase)

	client := &http.Client{}

	getTradeFees(signer, client)
	addLimitOrder(signer, client)
}
