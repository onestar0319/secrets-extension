<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Tobias Knöppler <thecalcaholic@web.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Secrets\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Secrets\Db\Secret;
use OCA\Secrets\Db\SecretMapper;
use Sabre\DAV\Exception\MethodNotAllowed;

class SecretService {
	private SecretMapper $mapper;

	public function __construct(SecretMapper $mapper) {
		$this->mapper = $mapper;
	}

	/**
	 * @param string $userId
	 * @return array<Secret>
	 */
	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	/**
	 * @return never
	 * @throws SecretNotFound
	 */
	private function handleException(Exception $e) {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new SecretNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find(string $uuid, string $userId): Secret {
		try {
			return $this->mapper->find($uuid, $userId);

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * @throws SecretNotFound
	 */
	public function findPublic(string $uuid): Secret {
		try {
			return $this->mapper->findPublic($uuid);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(string $title, string $encrypted, string $iv, string $userId): Secret {
		$uuid_bytes = openssl_random_pseudo_bytes(16);
		$uuid_bytes[6] = chr(ord($uuid_bytes[6]) & 0x0f | 0x40); // set version to 4 (0100)
		$uuid_bytes[8] = chr(ord($uuid_bytes[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		$secret = new Secret();
		$secret->setUuid(bin2hex($uuid_bytes));
		$secret->setTitle($title);
		$secret->setEncrypted($encrypted);
		$secret->setIv($iv);
		$secret->setUserId($userId);
		return $this->mapper->insert($secret);
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function getId(string $uuid): int {
		return $this->mapper->getId($uuid);
	}

	/**
	 * @throws SecretNotFound
	 */
	public function invalidate(string $uuid): Secret {
		try {
			return $this->mapper->invalidate($uuid);
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	/**
	 * @throws SecretNotFound
	 */
	public function delete(string $uuid, string $userId): Secret {
		try {
			$secret = $this->mapper->find($uuid, $userId);
			$this->mapper->delete($secret);
			return $secret;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
