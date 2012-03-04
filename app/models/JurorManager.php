<?php

class JurorManager extends DbManager  implements IAuthenticator {
	protected $table = 'jurors';
	protected $RecordClass = 'Juror';

	public function create(Record $record) {
	   	$record->password = sha1(rand(0,10000000).rand(0,10000000));
		$record->active = 0;

	    return parent::create($record);
	}

	public function findAllByRole($role) {
	    return dibi::query('SELECT * FROM [' . $this->table . '] WHERE role = %s', $role)->fetchAll();
	}

	/**
	 * Performs an authentication
	 * @param  array
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		$username = $credentials[self::USERNAME];
		$password = sha1($credentials[self::PASSWORD]);

		$row = dibi::fetch('SELECT * FROM jurors WHERE email = %s', $username);

		if (!$row) {
			throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $password) {
			throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
		}

		unset($row->password);
		return new Identity($row->id, $row->role, $row);
	}
}