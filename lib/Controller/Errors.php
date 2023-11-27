<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Ambrose Larkin <ambroseLarkin@goldendelta.space>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Secrets\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\Secrets\Service\SecretNotFound;

trait Errors {
	protected function handleNotFound(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (SecretNotFound $e) {
			$message = ['message' => $e->getMessage()];
			return new DataResponse($message, Http::STATUS_NOT_FOUND);
		}
	}
}
