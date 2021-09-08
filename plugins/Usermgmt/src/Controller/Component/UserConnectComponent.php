<?php
declare(strict_types=1);

/**
 * CakePHP 4.x User Management Plugin
 * Copyright (c) Chetan Varshney (The Director of Ektanjali Softwares Pvt Ltd), Product Copyright No- 11498/2012-CO/L
 *
 * Licensed under The GPL License
 * For full copyright and license information, please see the LICENSE.txt
 *
 * Product From - https://ektanjali.com
 * Product Demo - https://cakephp4-user-management.ektanjali.com
 */

namespace Usermgmt\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventInterface;
use Cake\Controller\ComponentRegistry;

class UserConnectComponent extends Component {

	public $registry;
	public $controller;
	public $request;
	public $response;
	public $session;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		$this->controller = $this->getController();
		$this->request = $this->controller->getRequest();
		$this->response = $this->controller->getResponse();
		$this->session = $this->request->getSession();
	}

	public function facebook_connect($redirectUrl=null) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'facebook'.DS.'src'.DS.'Facebook'.DS.'autoload.php');

		if(empty($redirectUrl)) {
			$redirectUrl = SITE_URL.'login/fb';
		}

		$fb = new \Facebook\Facebook([
				'app_id'=>FB_APP_ID,
				'app_secret'=>FB_SECRET,
				'default_graph_version'=>'v5.0'
			]);

		$helper = $fb->getRedirectLoginHelper();

		if(isset($_GET['state'])) {
			$helper->getPersistentDataHandler()->set('state', $_GET['state']);
		}

		$fbData = [];

		if(isset($_GET['error'])) {
			$fbData['error'] = $_GET['error_description'];
			$fbData['line_number'] = __LINE__;
		}
		else if(isset($_GET['code'])) {
			try {
				$accessToken = $helper->getAccessToken();

				if(isset($accessToken)) {
					$fb->setDefaultAccessToken($accessToken);
					$oAuth2Client = $fb->getOAuth2Client();

					if(!$accessToken->isLongLived()) {
						// Exchanges a short-lived access token for a long-lived one
						try {
							$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
						}
						catch (\Facebook\Exceptions\FacebookSDKException $ex) {
							$fbData['error'] = "Exception occured, with message: ".$ex->getMessage();
							$fbData['line_number'] = __LINE__;
						}
					}

					if(empty($fbData['error'])) {
						$_SESSION['facebook_access_token'] = (string) $accessToken;

						try {
							$response = $fb->get('/me?fields=id,first_name,last_name,name,email,gender');
						}
						catch(\Facebook\Exceptions\FacebookResponseException $ex) {
							$fbData['error'] = "Exception occured, with message: ".$ex->getMessage();
							$fbData['line_number'] = __LINE__;
						}
						catch(Facebook\Exceptions\FacebookSDKException $ex) {
							$fbData['error'] = "Exception occured, with message: ".$ex->getMessage();
							$fbData['line_number'] = __LINE__;
						}

						if(empty($fbData['error'])) {
							$fbData = $response->getDecodedBody() + $this->getDefaultData();

							$fbData['picture'] = 'http://graph.facebook.com/'.$fbData['id'].'/picture?type=large';
							$fbData['logoutURL'] = $helper->getLogoutUrl($accessToken, SITE_URL);
							$fbData['access_token'] = (string) $accessToken;
						}
					}
				}
			}
			catch(\Facebook\Exceptions\FacebookResponseException $ex) {
				$fbData['error'] = "Exception occured, code: ".$ex->getCode()." with message: ".$ex->getMessage();
				$fbData['line_number'] = __LINE__;
			}
			catch(\Facebook\Exceptions\FacebookSDKException $ex) {
				$fbData['error'] = "Exception occured, with message: ".$ex->getMessage();
				$fbData['line_number'] = __LINE__;
			}
		}
		else {
			$permissions = array_map('trim', explode(',', FB_SCOPE));

			$fbData['redirectURL'] = $helper->getLoginUrl($redirectUrl, $permissions)."&display=popup";
		}

		return $fbData;
	}
	public function twitter_connect($redirectUrl=null) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'twitter'.DS.'autoload.php');

		if(empty($redirectUrl)) {
			$redirectUrl = SITE_URL.'login/twt';
		}

		$twtData = [];

		if(!empty($_GET['oauth_verifier']) && !empty($_GET['oauth_token'])) {
			$oauth_token = $this->session->read('oauth_token');
			$oauth_token_secret = $this->session->read('oauth_token_secret');

			if(!empty($oauth_token) && !empty($oauth_token_secret)) {
				if($oauth_token === $_GET['oauth_token']) {
					$connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWT_APP_ID, TWT_SECRET, $oauth_token, $oauth_token_secret);

					$access_token = $connection->oauth('oauth/access_token', ['oauth_verifier'=>$_GET['oauth_verifier']]);

					$connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWT_APP_ID, TWT_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

					$params = ['include_email'=>'true', 'include_entities'=>'false', 'skip_status'=>'true'];

					$user = $connection->get('account/verify_credentials', $params);

					if(!isset($user->errors)) {
						$twtData = json_decode(json_encode($user), true) + $this->getDefaultData();

						if(!empty($twtData['name'])) {
							$name = explode(' ', $twtData['name'], 2);

							$twtData['first_name'] = $name[0];

							if(isset($name[1])) {
								$twtData['last_name'] = $name[1];
							}
						}

						if(isset($twtData['screen_name'])) {
							$twtData['username'] = $twtData['screen_name'];
						}

						if(isset($twtData['profile_image_url'])) {
							$twtData['picture'] = $twtData['profile_image_url'];
						}

						$twtData['access_token'] = $access_token['oauth_token'];
						$twtData['access_secret'] = $access_token['oauth_token_secret'];
					}
					else {
						$twtData['error'] = $user->errors[0]->message;
						$twtData['line_number'] = __LINE__;
					}
				}
				else {
					$twtData['error'] = __('Oauth token mis-matched');
					$twtData['line_number'] = __LINE__;
				}
			}
			else {
				$twtData['error'] = __('Oauth token and secret not found in session.');
				$twtData['line_number'] = __LINE__;
			}
		}
		else if(!isset($_GET['denied'])) {
			$connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWT_APP_ID, TWT_SECRET);

			$request_token = $connection->oauth('oauth/request_token', ['oauth_callback'=>$redirectUrl]);

			$this->session->write('oauth_token', $request_token['oauth_token']);
			$this->session->write('oauth_token_secret', $request_token['oauth_token_secret']);

			$twtData['redirectURL'] = $connection->url('oauth/authorize', ['oauth_token'=>$request_token['oauth_token']]);
		}
		else if(isset($_GET['denied'])) {
			$twtData['error'] = __('User denied authorisation');
			$twtData['line_number'] = __LINE__;
		}

		return $twtData;
	}
	public function linkedin_connect($redirectUrl=null) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'linkedin'.DS.'src'.DS.'LinkedIn'.DS.'Client.php');

		if(empty($redirectUrl)) {
			$redirectUrl = SITE_URL.'login/ldn';
		}

		$client = new \Client(LDN_API_KEY, LDN_SECRET_KEY);

		$ldnData = [];

		if(isset($_GET['error'])) {
			$ldnData['error'] = $_GET['error_description'];
		}
		else if(isset($_GET['code'])) {
			try {
				$access_token = $client->fetchAccessToken($_GET['code'], $redirectUrl);

				$profileData = $client->fetch('/v2/me');

				$emailData = $client->fetch('/v2/emailAddress?q=members&projection=(elements*(handle~))');

				$pictureData = $client->fetch('/v2/me?projection=(id,profilePicture(displayImage~digitalmediaAsset:playableStreams))');

				if(!empty($emailData['elements'][0]['handle~']['emailAddress']) && !empty($profileData['id'])) {
					$ldnData = $this->getDefaultData();

					$ldnData['id'] = $profileData['id'];
					$ldnData['email'] = $emailData['elements'][0]['handle~']['emailAddress'];

					if(!empty($profileData['localizedFirstName'])) {
						$ldnData['first_name'] = $profileData['localizedFirstName'];
					}

					if(!empty($profileData['localizedLastName'])) {
						$ldnData['last_name'] = $profileData['localizedLastName'];
					}

					$ldnData['name'] = trim($ldnData['first_name'].' '.$ldnData['last_name']);

					if(isset($pictureData['profilePicture'])) {
						$ldnData['picture'] = $client->getProfilePic($pictureData['profilePicture']);
					}

					$ldnData['access_token'] = $access_token['access_token'];
				}
				else {
					$ldnData['error'] = __('User data not found');
					$ldnData['line_number'] = __LINE__;
				}
			}
			catch (\Exception $ex) {
				$ldnData['error'] = "Exception occured, with message: ".$ex->getMessage();
				$ldnData['line_number'] = __LINE__;
			}
		}
		else {
			$ldnData['redirectURL'] = $client->getAuthorizationUrl($redirectUrl, 'r_liteprofile r_emailaddress');
		}

		return $ldnData;
	}
	public function gmail_connect($redirectUrl=null) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'google'.DS.'vendor'.DS.'autoload.php');

		if(empty($redirectUrl)) {
			$redirectUrl = SITE_URL.'login/gmail';
		}

		$client = new \Google_Client();

		$client->setClientId(GMAIL_CLIENT_ID);
		$client->setClientSecret(GMAIL_CLIENT_SECRET);
		$client->setRedirectUri($redirectUrl);
		$client->setDeveloperKey(GMAIL_API_KEY);
		$client->setScopes(['email', 'profile']);
		$client->setPrompt('select_account');

		$objOAuthService = new \Google_Service_Oauth2($client);

		$gmailData = [];

		if(isset($_GET['error'])) {
			$gmailData['error'] = __('User denied permission');
			$gmailData['line_number'] = __LINE__;
		}
		else if(isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $client->getAccessToken();
		}

		if($client->getAccessToken()) {
			$userData = $objOAuthService->userinfo->get();
			$userData = json_decode(json_encode($userData), true);

			if(!empty($userData['email']) && !empty($userData['verifiedEmail'])) {
				$gmailData = $userData + $this->getDefaultData();

				if(!empty($userData['givenName'])) {
					$gmailData['first_name'] = $userData['givenName'];
				}

				if(!empty($userData['familyName'])) {
					$gmailData['last_name'] = $userData['familyName'];
				}

				if(!empty($userData['name'])) {
					$gmailData['name'] = $userData['name'];
				}
				else {
					$gmailData['name'] = trim($gmailData['first_name'].' '.$gmailData['last_name']);
				}

				if(empty($gmailData['first_name'])) {
					$emailArr = explode('@', $gmailData['email']);
					$gmailData['first_name'] = $emailArr[0];
				}
				if(empty($gmailData['name'])) {
					$emailArr = explode('@', $gmailData['email']);
					$gmailData['name'] = $emailArr[0];
				}
			}
			else {
				$gmailData['error'] = __('User data not found');
				$gmailData['line_number'] = __LINE__;
			}
		}
		else {
			if(empty($gmailData['error'])) {
				$gmailData['redirectURL'] = $client->createAuthUrl();
			}
		}

		return $gmailData;
	}
	public function getDefaultData() {
		$default = [];
		
		$default['first_name'] = $default['last_name'] = $default['name'] = $default['username'] = $default['gender'] = $default['location'] = $default['picture'] = '';

		return $default;
	}
}
