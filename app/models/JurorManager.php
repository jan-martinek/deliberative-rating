<?php

class JurorManager extends DbManager  implements IAuthenticator {
	protected $table = 'jurors';
	protected $RecordClass = 'Juror';

	public function create(Record $record) {
	    //vytvořit barvu podle stringu jména
	    if (!isset($record->color) OR $record->color == '') {
		$record->color = $this->colorFromName($record->publicName);
	    }

	    return parent::create($record);
	}

	public function findAllByRole($role) {
	    return dibi::query('SELECT * FROM [' . $this->table . '] WHERE active = 1 AND role = %s', $role)->fetchAll();
	}

	
	public function getCacheKey() {
	    return md5(dibi::query('SHOW TABLE STATUS LIKE %s', $this->table)->fetch()->Update_time);
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
		$password = md5($credentials[self::PASSWORD]);

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