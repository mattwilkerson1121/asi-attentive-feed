import argparse
import requests  # you may need to install this https://docs.python-requests.org/en/latest/user/install/
import json
import time
import datetime;
 
from distutils.util import strtobool

ct = datetime.datetime.now()
API_KEY = 'bW9PeWh1WDVVd3U5YzBmNUtZeWcwclZQQjJkTXdiU2YxcVVw'  # Set this
API_BASE_URL = 'https://api.attentivemobile.com'
STATUS_INTERVAL = 10


def initiate_catalog_upload(validate_only, api_key):
    post_url = API_BASE_URL + '/v1/product-catalog/uploads'
    r = requests.post(
        post_url,
        json={'validateOnly': validate_only},
        headers={'Authorization': 'Bearer ' + api_key},
    )
    assert r.status_code == 200, "Are you sure your api key is correct?"
    resp = r.json()
    return resp['uploadId'], resp['uploadUrl']


def print_errors(errors):
    print("Validation Errors:")
    for error in errors:
        print(json.dumps(error))


def wait_for_validation(upload_id, validate_only, api_key, counter):
    """
    The file at this point should now be queued up and we are awaiting validation. If there are any
    validation errors, we'll print them out from here. You may want to integrate your own more
    advanced monitoring.
    """
    print("Waiting for validation...start time:- "+ct)
    time.sleep(STATUS_INTERVAL)
    get_url = API_BASE_URL + '/v1/product-catalog/uploads/' + upload_id
    r = requests.get(get_url, headers={'Authorization': 'Bearer ' + api_key}).json()
    if r['errors']:
        # Consider implementing alerting over here
        print_errors(r['errors'])
    if r['status'] == 'validated':
        print("Validated...end time:- "+ct)
        return
    # waiting approximately an hour before giving up. Totally up to you how long, but Attentive should
    # rarely be an hour behind in processing
    if counter == 360:
        print("Giving up on waiting for validation for " + upload_id)
        return

    wait_for_validation(upload_id, validate_only, api_key, counter + 1)


def upload_catalog(filepath, validate_only, api_key):
    upload_id, upload_url = initiate_catalog_upload(validate_only, api_key)
    with open(filepath, 'rb') as f:
        r = requests.put(upload_url, data=f)
        assert r.status_code == 200, 'Unexpected issue uploading'
    wait_for_validation(upload_id, validate_only, api_key, 0)
    return upload_id


if __name__ == '__main__':
    parser = argparse.ArgumentParser(
        description='CLI utility script to demonstrate and assist in sending your generated product catalog to Attentive via its API'
    )
    parser.add_argument('filepath', help='Path to your catalog file')
    parser.add_argument(
        '--validateOnly',
        default=True,
        dest='validate_only',
        type=lambda x: bool(strtobool(x)),
        help='Boolean flag to choose whether or not Attentive should only validate the file for correctness only or not. '
        'Defaults to True to prevent saving invalid data during development. Set to False when everything passes without errors.',
    )
    parser.add_argument(
        '--apiKey',
        dest='api_key',
        help='You can pass the API Key in here as an argument or set it in the API_KEY variable at the top of this file',
    )
    args = parser.parse_args()

    key = args.api_key or API_KEY
    assert key, (
        'Please either pass in the apiKey argument to this script, or '
        'update the API_KEY variable at the top of this file to authenticate to Attentive'
    )
    id = upload_catalog(args.filepath, args.validate_only, key)
