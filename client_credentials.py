import requests
import json

CLIENT_ID = '42c06fb738594d198e571b47d39bdd75'
CLIENT_SECRET = 'ifPKW8LZUNfEKEIhvDaOcMzP4daIhQEcmE7qB9iQMRlxdQKLxBhncLIrUp2piGyC'
TOKEN_URL = "https://allegro.pl/auth/oauth/token"


def get_access_token():
    try:
        data = {'grant_type': 'client_credentials'}
        access_token_response = requests.post(TOKEN_URL, data=data, verify=False, allow_redirects=False, auth=(CLIENT_ID, CLIENT_SECRET))
        return access_token_response
    except requests.exceptions.HTTPError as err:
        raise SystemExit(err)


def main():
    access_token_response = get_access_token()
    tokens = json.loads(access_token_response.text)
    access_token = tokens['access_token']
    print("access token: " + access_token)


if __name__ == "__main__":
    main()

