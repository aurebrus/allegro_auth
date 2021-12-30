import requests
import json
from requests.auth import HTTPBasicAuth
import time

CLIENT_ID = 'fc3bec4965f248278d3de4904ec8e052'
CLIENT_SECRET = 'BscrPdfFhDhQavVK6xmyPSYzpPpQdexgBTOg03mIkKNsWUT7LtG3YjX3vrmgGjIr'
CODE_URL = "https://allegro.pl/auth/oauth/device"
TOKEN_URL = "https://allegro.pl/auth/oauth/token"


def get_code():
    try:
        payload = {'client_id': CLIENT_ID}
        headers = {'Content-type': 'application/x-www-form-urlencoded'}
        api_call_response = requests.post(CODE_URL, auth=HTTPBasicAuth(CLIENT_ID, CLIENT_SECRET),
                                          headers=headers, data=payload, verify=False)
        return api_call_response
    except requests.exceptions.HTTPError as err:
        raise SystemExit(err)


def get_access_token(device_code):
    headers = {'Content-type': 'application/x-www-form-urlencoded'}
    data = {'grant_type': 'urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Adevice_code', 'device_code': device_code}
    api_call_response = requests.post(TOKEN_URL, auth=HTTPBasicAuth(CLIENT_ID, CLIENT_SECRET),
                                      headers=headers, data=data, verify=False)
    return api_call_response


def main():
    code = get_code()
    result = json.loads(code.text)
    print("Użytkowniku, otwórz ten adres w przeglądarce:" + result['verification_uri_complete'])
    access_token = False
    interval = int(result['interval'])
    while not access_token:
        time.sleep(interval)
        device_code = result['device_code']
        result_access_token = get_access_token(device_code)
        token = json.loads(result_access_token.text)
        if result_access_token.status_code == 400:
            if token['error'] == 'slow_down':
               interval += interval
            if token['error'] == 'access_denied':
                break
        else:
            access_token = token['access_token']
            print("access_token: " + access_token)



if __name__ == "__main__":
    main()
