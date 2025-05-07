<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Admin;
use Exception;
use Google\Service\Calendar;
use Google\Service\Sheets;
use Google\Service\Drive;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\UpdateValuesRequest;

trait GoogleTrait
{
    public function getClient($user = true)
    {
        if ($user === true) {
            $configJson = 'client_secret.json';
        } else {
            $configJson = 'admin_client_secret.json';
        }

        $filePath = public_path($configJson);
        $applicationName = config('app.name');

        // create the client
        $client = new \Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($filePath);
        $client->setAccessType('offline'); // necessary for getting the refresh token
        $client->setPrompt('consent'); // necessary for getting the refresh token
        // scopes determine what google endpoints we can access. keep it simple for now.
        if ($user === false) {
            $client->setScopes([
                \Google\Service\Oauth2::USERINFO_PROFILE,
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
                \Google\Service\Drive::DRIVE_FILE,
                \Google\Service\Sheets::SPREADSHEETS,
                'https://www.googleapis.com/auth/adwords',
                'https://www.googleapis.com/auth/tagmanager.readonly',
                'https://www.googleapis.com/auth/tagmanager.manage.users',
                'https://www.googleapis.com/auth/tagmanager.manage.accounts',
                'https://www.googleapis.com/auth/tagmanager.edit.containers',
                'https://www.googleapis.com/auth/tagmanager.delete.containers',
                'https://www.googleapis.com/auth/tagmanager.edit.containerversions',
                'https://www.googleapis.com/auth/tagmanager.publish',
            ]);
        } else {
            $client->setScopes([
                \Google\Service\Oauth2::USERINFO_PROFILE,
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
                \Google\Service\Calendar::CALENDAR,
            ]);
        }
        $client->setIncludeGrantedScopes(true);
        return $client;
    }

    public function getUserClient($id = null): \Google_Client
    {
        if (empty($id)) {
            $user = User::find(auth('web')->user()->id);
        } else {
            $user = User::find($id);
        }

        $accessToken = stripslashes($user->google_access_token);

        /**
         * Get client and set access token
         */
        $client = $this->getClient();
        $client->setAccessToken($accessToken);
        /**
         * Handle refresh
         */
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            if (empty($refreshToken)) {
                return $client;
            }

            // fetch new access token
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
            
            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);
            // save new access token
            $user->google_access_token = json_encode($accessToken);
            $user->save();
        }

        return $client;
    }

    public function checkRefreshToken($user)
    {
        if (!empty($user->google_access_token)) {
            $accessToken = stripslashes($user->google_access_token);
            $google_client = $this->getClient();
            $google_client->setAccessToken($accessToken);
            if ($google_client->isAccessTokenExpired() && empty($google_client->getRefreshToken())) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    private function getAdminClient($id = 1): \Google_Client
    {
        $user = Admin::find($id);

        $accessToken = stripslashes($user->google_access_token);

        /**
         * Get client and set access token
         */
        $client = $this->getClient(false);
        $client->setAccessToken($accessToken);
        /**
         * Handle refresh
         */
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            if (empty($refreshToken)) {
                return $client;
            }

            // fetch new access token
            $client->fetchAccessTokenWithRefreshToken($refreshToken);

            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);
            // save new access token
            $user->google_access_token = json_encode($accessToken);
            $user->save();
        }

        return $client;
    }

    public function checkRefreshTokenNew($googleAccount)
    {
        if (empty($googleAccount->access_token)) {
            return false;
        }

        $accessToken = stripslashes($googleAccount->access_token);

        $googleClient = $this->getClient(false);
        $googleClient->setAccessToken($accessToken);

        $isAccessTokenExpired = $googleClient->isAccessTokenExpired();
        if ($isAccessTokenExpired) {
            $refreshToken = $googleClient->getRefreshToken();
            if (empty($refreshToken)) {
                return false;
            }

            // Refresh & save access token
            $googleClient->fetchAccessTokenWithRefreshToken($refreshToken);
            
            $accessToken = $googleClient->getAccessToken();

            $googleClient->setAccessToken($accessToken);

            $googleAccount->access_token = json_encode($accessToken);
            $googleAccount->save();
        }
        
        return true;
    }

    private function createNewSpreadsheet($client_name, $client_id)
    {
        $client = $this->getAdminClient();
        if ($client) {
            // Create a new Google Sheets service
            $service = new Sheets($client);

            // Create a new spreadsheet
            $spreadsheet = new Spreadsheet([
                'properties' => [
                    'title' => $client_name . '\'s (' . $client_id . ') Lead Sheet',
                ],
            ]);

            // Call the Sheets API to create the spreadsheet
            $spreadsheet = $service->spreadsheets->create($spreadsheet);
            $spreadsheetId = $spreadsheet->spreadsheetId;

            // Rename the default sheet to "Leads Frequency"
            $renameRequest = new Request([
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => 0, // SheetId of the default sheet is 0
                        'title' => 'Leads Frequency'
                    ],
                    'fields' => 'title'
                ]
            ]);

            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                'requests' => [$renameRequest]
            ]);

            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

            $this->setHeaders($spreadsheetId);

            // Set the permissions to make the spreadsheet public
            $driveService = new Drive($client);
            $driveService->permissions->create(
                $spreadsheetId,
                new \Google_Service_Drive_Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ])
            );

            return $spreadsheetId;
        }
        return null;
    }

    private function setHeaders($spreadsheetId)
    {
        $client = $this->getAdminClient();

        $service = new \Google_Service_Sheets($client);

        // Range for the "Leads Frequency" sheet
        $range = 'Leads Frequency!A1:D1';
        $values = [
            ['Name', 'Email', 'Mobile Number', 'Additional Data']
        ];

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

        // Check for errors
        if ($result->getUpdatedCells() === null) {
            dd('Error occurred: ' . $result->getError()->getMessage());
        }
    }


    private function saveToGoogleSheet($name, $email = null, $mobileNumber, $additionalData, $sheetName, $spreadsheetId)
    {
        $client = $this->getAdminClient();
        $service = new \Google_Service_Sheets($client);
        $range = $sheetName . '!A:D';
        $additionalDataCol = '';
        $additionalDataCount = count($additionalData);
        if (!empty($additionalData) && $additionalDataCount > 0) {
            foreach ($additionalData as $key => $val) {
                $additionalDataCol .= "{$val['key']}: {$val['value']}";
                if ($key < $additionalDataCount - 1) {
                    $additionalDataCol .= "\n";
                }
            }
        }
        $values = [
            [$name, $email ?? '', $mobileNumber, $additionalDataCol]
        ];
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }
}
