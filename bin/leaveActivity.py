#!/usr/bin/python3
import sys
import requests
import json
from bs4 import BeautifulSoup

def joinActivity(username = '', password = '', activityID = ''):
    creds = {
        'UserName': username,
        'Password': password,

        'RememberMe': 'true',
        'login': 'Login'
    }

    headers = {
        'Host': 'fitness.flexybox.com',
        'Origin': 'https://fitness.flexybox.com',
        'Upgrade-Insecure-Requests': '1',
        'DNT': '1',
        'Content-Type': 'application/x-www-form-urlencoded',
        'User-Agent': 'RasmusBundsgaard/OBBC UA 1.0',
        'Referer': 'https://fitness.flexybox.com/obbc/Account/LogOn'
    }
    s = requests.Session()

    s.headers = headers;

    loggedIn = s.post(
        'https://fitness.flexybox.com/obbc/Account/LogOn',
        data = creds
    )

    html = BeautifulSoup(loggedIn.text, "html.parser")

    if html.find('div', {'class': 'validation-summary-errors'}):
        return {'msg': 'Forkert brugernavn/adgangskode', 'status': 401}

    _res = s.get('https://fitness.flexybox.com/obbc/TeamActivity/LeaveActivityWithLinkID?activityLinkID='+activityID)

    res = json.loads(_res.text)

    if not 'Msg' in res:
        return { 'msg': 'Hold ikke fundet', 'status': 404 }

    return { 'msg': BeautifulSoup(res['Msg'], "html.parser").text.strip(), 'status': 200 }


def main():
    username = sys.argv[1]
    password = sys.argv[2]
    activityID = sys.argv[3]
    result = joinActivity(username, password, activityID)

    print(json.dumps(result))


if __name__ == '__main__':
    main()
