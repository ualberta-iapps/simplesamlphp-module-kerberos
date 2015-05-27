<?php

/**
 * Kerberos KDC authentication source.
 *
 * This class is an example authentication source which will always return a user with
 * a static set of attributes.
 *
 * @author Tyler Antonio, University of Alberta <tantonio@ualberta.ca>
 * @package simpleSAMLphp
 */
class sspmod_kerberos_Auth_Source_Krb5 extends sspmod_core_Auth_UserPassBase {


	/**
	 * The attributes we return.
	 */
	private $attributes;

	/**
	 * Configuration
	 */
	private $config;

	/**
	 * Kerberos KDC authentication resource
	 */
	private $krb5;

	/**
	 * Kerberos realm
	 */
	private $realm;

	private $stripRealm;


	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		/* Ensure that the krb5 PHP module is installed and loaded */
		if(!extension_loaded('krb5')){
			throw new Exception('Missing required PHP module krb5 for authentication source '. $this->authId);
		}

		/* Make sure that all required parameters are present. */
		foreach (array('realm') as $param) {
			if (!array_key_exists($param, $config)) {
				throw new Exception('Missing required attribute \'' . $param .
					'\' for authentication source ' . $this->authId);
			}

			if (!is_string($config[$param])) {
				throw new Exception('Expected parameter \'' . $param .
					'\' for authentication source ' . $this->authId .
					' to be a string. Instead it was: ' .
					var_export($config[$param], TRUE));
			}
		}

		$this->krb5 = new KRB5CCache();
		$this->realm = '@'. $config['realm'];
		$this->stripRealm = $config['stripRealm'];

	}

	/**
	 * Remove the scoping from a string
	 *
	 * @param string $string  The string containing a scope
	 * @return string  The provided string with the scrope removed
	 */
	private function stripScope($string){
		assert('is_string($string)');

		preg_match('/^[A-Za-z0-9\-\.]+/', $string, $matches);

		return $matches[0];
	}

	/**
	 * Attempt to log in using the given username and password.
	 *
	 * @param string $username  The username the user wrote.
	 * @param string $password  The password the user wrote.
	 * @return array  Associative array with the users attributes.
	 */
	protected function login($username, $password){
		assert('is_string($username)');
		assert('is_string($password)');

		$principal = $this->stripScope($username) . $this->realm;

		try{
			$this->krb5->initPassword($principal, $password);
			$uid = ($this->stripRealm)? $this->stripScope($this->krb5->getPrincipal()) : $this->krb5->getPrincipal();
			$attributes = array(
				'uid' => array($uid),
			);

			return $attributes;
		}
		catch (Exception $error){
			throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}
	}

}

?>