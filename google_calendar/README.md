# Google Calendar setup

This folder holds OAuth credentials and token for the Google Calendar API.

Required files
- `credentials.json`: OAuth client credentials from Google Cloud Console.
- `token.json`: OAuth access/refresh token generated after authorization.

Steps
1) Create a Google Cloud project and enable the Google Calendar API.
2) Create OAuth credentials (Web application) with redirect URI:
   `http://localhost/psy-system/google_calendar/authorize.php`
3) Save the downloaded `credentials.json` into this folder.
4) From `psy-system`, install dependencies:
   `composer install`
5) Open `/psy-system/google_calendar/authorize.php` in the browser to generate `token.json`.

Notes
- Do not commit real credentials or token to public repos.